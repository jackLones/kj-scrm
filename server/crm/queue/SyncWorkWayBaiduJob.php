<?php
	/**
	 * 百度统计
	 * User: xingchangyu
	 * Date: 2020/07/13
	 * Time: 17：00
	 */

	namespace app\queue;

	use app\components\InvalidDataException;
	use app\models\WorkContactWayBaiduCode;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;

	class SyncWorkWayBaiduJob extends BaseObject implements JobInterface
	{
		public $baidu_id;
		public $baidu_code_id;
		public $corp_id;
		public $user;
		public $party;

		public function execute ($queue)
		{
			\Yii::error($this->baidu_code_id, 'baidu_code_id');

			try {
				if (!empty($this->baidu_code_id)) {//删除
					$time      = time();
					$baiDuInfo = WorkContactWayBaiduCode::findOne($this->baidu_code_id);
					if (empty($baiDuInfo)) {
						return false;
					}
					//过期时间有变动，重新进队列
					if ($baiDuInfo->expire_time > $time) {
						$second = $baiDuInfo->expire_time - $time;
						\Yii::$app->queue->delay($second)->push(new SyncWorkWayBaiduJob([
							'baidu_code_id' => $this->baidu_code_id,
							'corp_id'       => $this->corp_id,
						]));

						return false;
					}

					WorkContactWayBaiduCode::delConfigId($this->corp_id, $baiDuInfo->config_id);
					$baiDuInfo->queue_id      = 0;
					$baiDuInfo->config_status = 0;
					$baiDuInfo->update();
				} elseif (!empty($this->baidu_id)) {//根据活动id来更新二维码
					if (!empty($this->user) || !empty($this->party)) {
						$codeList = WorkContactWayBaiduCode::find()->where(['way_id' => $this->baidu_id, 'config_status' => 1])->all();
						if (!empty($codeList)) {
							/**@var WorkContactWayBaiduCode $code * */
							foreach ($codeList as $code) {
								try {
									WorkContactWayBaiduCode::updateCode($this->baidu_id, $code->id, $this->user, $this->party);
								} catch (InvalidDataException $e) {
									continue;
								}
							}
						}
					}
				}
			} catch (\Exception $e) {
				\Yii::error($e->getMessage(), 'baidu_code_id');
			}
		}
	}