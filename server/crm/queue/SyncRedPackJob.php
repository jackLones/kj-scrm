<?php
	/**
	 * 红包裂变状态及自动发放
	 * User: xingchangyu
	 * Date: 2020/06/02
	 * Time: 13：00
	 */

	namespace app\queue;

	use app\components\InvalidDataException;
	use app\models\RedPack;
	use app\models\RedPackHelpDetail;
	use app\models\RedPackJoin;
	use app\models\RedPackOrder;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;

	class SyncRedPackJob extends BaseObject implements JobInterface
	{
		public $red_pack_id;
		public $red_status;
		public $sendData;//发放红包数据

		public function execute ($queue)
		{
			ini_set('memory_limit', '1024M');
			set_time_limit(0);
			\Yii::error($this->red_pack_id, 'red_pack_id');
			$redPack = RedPack::findOne($this->red_pack_id);
			if (empty($redPack)) {
				return false;
			}
			$status = !empty($this->red_status) ? $this->red_status : 3;
			try {
				if (empty($this->sendData)) {
					\Yii::error($this->red_status, 'red_status');
					if ($status == 3) {
						if (in_array($redPack->status, [0, 3, 4, 5])) {
							return false;
						}
						$now_date = date('Y-m-d 23:59:59');
						$time     = time();
						$end_time = strtotime($redPack->end_time);
						//如果结束时间大于今天
						if ($redPack->end_time > $now_date) {
							return false;
						}
						//结束时间有变动，重新进队列
						if ($end_time > $time) {
							$second = $end_time - $time;
							\Yii::$app->queue->delay($second)->push(new SyncRedPackJob([
								'red_pack_id' => $redPack->id
							]));

							return false;
						}
					}
					RedPack::handleData($redPack, $status);
				} elseif(!empty($this->sendData['hid']) || !empty($this->sendData['jid'])) {//发放红包
					if (!empty($this->sendData['hid'])) {//助力者发放
						$helpInfo = RedPackHelpDetail::findOne($this->sendData['hid']);
						if (!empty($helpInfo)) {
							try {
								$res = RedPackOrder::sendRedPack($this->sendData);
								if (!empty($res)) {
									$helpInfo->send_status = 1;
									$helpInfo->send_type   = 1;
									$helpInfo->update();
									$is_send = 1;
								}
							} catch (InvalidDataException $e) {
								$is_send = 0;
								\Yii::error($e->getMessage(), 'RedPackOrder');
							}
						}
					} elseif (!empty($this->sendData['jid'])) {//参与者发放
						$joinInfo = RedPackJoin::findOne($this->sendData['jid']);
						if (!empty($joinInfo)) {
							try {
								$res = RedPackOrder::sendRedPack($this->sendData);
								if (!empty($res)) {
									if (isset($this->sendData['first_send_status'])) {
										$joinInfo->first_send_status = $this->sendData['first_send_status'];
									}
									if (isset($this->sendData['first_send_type'])) {
										$joinInfo->first_send_type = $this->sendData['first_send_type'];
									}
									if (isset($this->sendData['send_status'])) {
										$joinInfo->send_status = $this->sendData['send_status'];
									}
									if (isset($this->sendData['send_type'])) {
										$joinInfo->send_type = $this->sendData['send_type'];
									}

									$joinInfo->update();
									$is_send = 1;
								}
							} catch (InvalidDataException $e) {
								$is_send = 0;
								\Yii::error($e->getMessage(), 'RedPackOrder');
							}
						}
					}
					//补发剩余的
					if(!empty($is_send)){
						\Yii::$app->queue->delay(10)->push(new SyncRedPackJob([
							'red_pack_id' => $redPack->id,
							'sendData'   => ['is_all' => 1, 'uid' => $redPack->uid]
						]));
					}
				} elseif (!empty($this->sendData['is_all']) && !empty($this->sendData['uid'])){
					//如果微信支付有钱，补发之前没发的
					RedPack::supplySend($redPack->uid);
				}
			} catch (\Exception $e) {
				\Yii::error($e->getMessage(), 'red_pack_id');
			}
		}
	}