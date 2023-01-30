<?php
	/**
	 * 群打卡
	 * User: xingchangyu
	 * Date: 2020/11/27
	 * Time: 17：00
	 */

	namespace app\queue;

	use app\models\ExternalTimeLine;
	use app\models\WorkExternalContact;
	use app\models\WorkExternalContactFollowStatistic;
	use app\models\WorkGroupClockPrize;
	use app\models\WorkGroupClockTask;
	use app\util\WorkUtils;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;

	class SyncClockJob extends BaseObject implements JobInterface
	{
		public $corpId;
		public $activityId;
		public $joinId;
		public $externalId;
		public $isFollow;//是否是跟进统计补充数据

		public function execute ($queue)
		{
			ini_set('memory_limit', '1024M');
			set_time_limit(0);

			if (!empty($this->joinId) && !empty($this->externalId)) {//补充数据
				$externalContact = WorkExternalContact::findOne($this->externalId);
				if (!empty($externalContact)) {
					$lineList = ExternalTimeLine::find()->where(['event' => 'punch_card', 'event_id' => $this->joinId, 'external_id' => 0])->all();
					if (!empty($lineList)) {
						/**@var ExternalTimeLine $line * */
						foreach ($lineList as $line) {
							$line->external_id = $this->externalId;
							$remark            = $line->remark;
							if (strpos($remark, '【未知客户】') !== false) {
								$remark       = str_replace('【未知客户】', '【' . $externalContact->name . '】', $remark);
								$line->remark = $remark;
							}
							$line->update();
						}
					}
				}
			} elseif (!empty($this->activityId) && !empty($this->corpId)) {//活动删除
				$clockTask = WorkGroupClockTask::find()->where(['activity_id' => $this->activityId, 'reward_type' => 1])->all();
				if (!empty($clockTask)) {
					/** @var WorkGroupClockTask $task */
					foreach ($clockTask as $task) {
						if (!empty($task->config_id)) {
							$workApi = WorkUtils::getWorkApi($this->corpId, WorkUtils::EXTERNAL_API);
							if (!empty($workApi)) {
								try {
									$workApi->ECDelContactWay($task->config_id);
								} catch (\Exception $e) {

								}
							}
						}
					}
				}
			} elseif (!empty($this->corpId)) {//补发
				\Yii::error($this->isFollow,'isFollow');
				if (!empty($this->isFollow)) {
					WorkExternalContactFollowStatistic::supplyData($this->corpId);
				} else {
					WorkGroupClockPrize::supplySend($this->corpId);
				}
			}
		}
	}