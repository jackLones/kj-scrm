<?php

	namespace app\queue;

	use app\components\InvalidDataException;
	use app\models\AwardsActivity;
	use app\models\AwardsRecords;
	use app\models\RedPackOrder;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;

	class SyncAwardJob extends BaseObject implements JobInterface
	{
		public $award_id;
		public $award_status;
		public $sendData;//发放红包数据

		public function execute ($queue)
		{
			ini_set('memory_limit', '1024M');
			set_time_limit(0);
			\Yii::error($this->award_id, 'award_id');
			$award = AwardsActivity::findOne($this->award_id);
			if (empty($award)) {
				return false;
			}
			try {
				if (empty($this->sendData)) {
					$status = !empty($this->award_status) ? $this->award_status : 2;
					AwardsActivity::handleData($award, $status);
				} elseif (!empty($this->sendData['hid'])) {
					$records = AwardsRecords::findOne($this->sendData['hid']);
					if (!empty($records) && ($records->is_record == 1) && ($records->status == 0)) {
						try {
							$res = RedPackOrder::sendRedPack($this->sendData, 3);
							if (!empty($res)) {
								$records->status = 1;
								$records->update();
								//补发
								\Yii::$app->queue->delay(10)->push(new SyncAwardJob([
									'award_id' => $award->id,
									'sendData'   => ['is_all' => 1, 'uid' => $award->uid]
								]));
							}
						} catch (InvalidDataException $e) {
							\Yii::error($e->getMessage(), 'RedPackOrder');
						}
					}
				} elseif (!empty($this->sendData['is_all']) && !empty($this->sendData['uid'])){
					//如果微信支付有钱，补发之前没发的
					AwardsActivity::supplySend($this->sendData['uid']);
				}
			} catch (\Exception $e) {
				\Yii::error($e->getMessage(), 'award_id');
			}
		}
	}