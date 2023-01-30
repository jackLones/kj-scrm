<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2020/11/30
	 * Time: 5:48 下午
	 */

	namespace app\queue;

	use app\models\WorkUser;
	use app\models\WorkUserStatistic;
	use app\util\DateUtil;
	use app\util\SUtils;
	use app\util\WorkUtils;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;

	class WorkGetUserBeheviorJob extends BaseObject implements JobInterface
	{
		public $corp_id;
		/** @var WorkUser $user_data */
		public $user_data;
		public $stime;
		public $etime;
		public $type;

		public function execute ($queue)
		{
			try {
				$workApi = WorkUtils::getWorkApi($this->corp_id, WorkUtils::EXTERNAL_API);
			} catch (\Exception $e) {
				\Yii::error($e->getMessage(), 'work-user-getApi');
			}
			if (!empty($workApi)) {
				try {
					$behavior               = [];
					$behavior['userid'][]   = $this->user_data->userid;
					$behavior['start_time'] = $this->stime;
					$behavior['end_time']   = $this->etime;

					$statisticData = $workApi->ECGetUserBeheviorData($behavior);
					$sData         = SUtils::Object2Array($statisticData);
					\Yii::error($sData, '$sData');
					if (!empty($sData)) {
						foreach ($sData as $yData) {
							if (!empty($yData['chat_cnt']) || !empty($yData['message_cnt']) || !empty($yData['reply_percentage']) || !empty($yData['avg_reply_time']) || !empty($yData['negative_feedback_cnt']) || !empty($yData['new_apply_cnt']) || !empty($yData['new_contact_cnt'])) {
								$workUserStatistic = WorkUserStatistic::findOne(['corp_id' => $this->corp_id, 'userid' => $this->user_data->userid, 'time' => $this->stime]);
								if (empty($workUserStatistic)) {
									$workUserStatistic          = new WorkUserStatistic();
									$workUserStatistic->corp_id = $this->corp_id;
									$workUserStatistic->userid  = $this->user_data->userid;
								}

								$workUserStatistic->new_apply_cnt         = $yData['new_apply_cnt'];
								$workUserStatistic->new_contact_cnt       = $yData['new_contact_cnt'];
								$workUserStatistic->negative_feedback_cnt = $yData['negative_feedback_cnt'];
								$workUserStatistic->chat_cnt              = $yData['chat_cnt'];
								$workUserStatistic->message_cnt           = $yData['message_cnt'];
								$workUserStatistic->reply_percentage      = strval($yData['reply_percentage']);
								$workUserStatistic->avg_reply_time        = strval($yData['avg_reply_time']);
								$workUserStatistic->time                  = $yData['stat_time'];
								$workUserStatistic->data_time             = date('Y-m-d', $yData['stat_time']);
								$workUserStatistic->create_time           = DateUtil::getCurrentTime();

								if (!$workUserStatistic->save()) {
									\Yii::error(SUtils::modelError($workUserStatistic), 'workUserStatistic_error');
								} else {
									\Yii::error($workUserStatistic->id, 'workUserStatistic');
								}

								if (!empty($yData['new_apply_cnt'])) {
									$new_apply_cnt                  = $this->user_data->new_apply_cnt;
									$this->user_data->new_apply_cnt = $new_apply_cnt + $yData['new_apply_cnt'];
								}
								if (!empty($yData['new_contact_cnt'])) {
									$new_contact_cnt                  = $this->user_data->new_contact_cnt;
									$this->user_data->new_contact_cnt = $new_contact_cnt + $yData['new_contact_cnt'];
								}
								if (!empty($yData['negative_feedback_cnt'])) {
									$negative_feedback_cnt                  = $this->user_data->negative_feedback_cnt;
									$this->user_data->negative_feedback_cnt = $negative_feedback_cnt + $yData['negative_feedback_cnt'];
								}
								if (!empty($yData['chat_cnt'])) {
									$chat_cnt                  = $this->user_data->chat_cnt;
									$this->user_data->chat_cnt = $chat_cnt + $yData['chat_cnt'];
								}
								if (!empty($yData['message_cnt'])) {
									$message_cnt                  = $this->user_data->message_cnt;
									$this->user_data->message_cnt = $message_cnt + $yData['message_cnt'];
								}

								if (!empty($yData['reply_percentage'])) {
									$reply_percentage                  = WorkUserStatistic::getReplyPercentage($this->corp_id, $this->user_data->userid, 1);
									$this->user_data->reply_percentage = strval($reply_percentage);
								}

								if (!empty($yData['avg_reply_time'])) {
									$avg_reply_time                  = WorkUserStatistic::getReplyPercentage($this->corp_id, $this->user_data->userid, 2);
									$this->user_data->avg_reply_time = strval($avg_reply_time);
								}

								$this->user_data->save();

							}

							if ($this->type == 0) {
								break;
							}
						}

					}
				} catch (\Exception $e) {
					\Yii::error($e->getMessage(), 'workUser-exception');
				}
			}
		}

	}