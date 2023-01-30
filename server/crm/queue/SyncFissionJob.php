<?php
	/**
	 * 裂变引流状态及自动发放
	 * User: xingchangyu
	 * Date: 2020/06/23
	 * Time: 13：00
	 */

	namespace app\queue;

	use app\models\Fission;
	use app\models\FissionJoin;
	use app\models\RedPackOrder;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;
	use app\components\InvalidDataException;

	class SyncFissionJob extends BaseObject implements JobInterface
	{
		public $fission_id;
		public $fission_status;
		public $sendData;//发放红包数据

		public function execute ($queue)
		{
			ini_set('memory_limit', '1024M');
			set_time_limit(0);
			\Yii::error($this->fission_id, 'fission_id');
			\Yii::error($this->sendData, 'sendData');
			$fission = Fission::findOne($this->fission_id);
			if (empty($fission)) {
				return false;
			}
			try {
				if (empty($this->sendData)) {
					$status = !empty($this->fission_status) ? $this->fission_status : 3;

					Fission::handleData($fission, $status);
				} elseif (!empty($this->sendData['jid'])) {//发送红包
					$fissionJoin = FissionJoin::findOne($this->sendData['jid']);
					if (!empty($fissionJoin) && ($fissionJoin->status == 2 && $fissionJoin->prize_status == 0)) {
						try {
							$res = RedPackOrder::sendRedPack($this->sendData, 2);
							if (!empty($res)) {
								$fissionJoin->prize_status = 1;
								$fissionJoin->update();
								//补发
								\Yii::$app->queue->delay(10)->push(new SyncFissionJob([
									'fission_id' => $fission->id,
									'sendData'   => ['is_all' => 1, 'uid' => $fission->uid]
								]));
							}
						} catch (InvalidDataException $e) {
							\Yii::error($e->getMessage(), 'RedPackOrder');
						}
					}
				} elseif (!empty($this->sendData['is_all']) && !empty($this->sendData['uid'])) {//如果微信支付有钱，补发之前没发的
					Fission::supplySend($this->sendData['uid']);
				}
			} catch (\Exception $e) {
				\Yii::error($e->getMessage(), 'fission_id');
			}
		}
	}