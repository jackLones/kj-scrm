<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\components\InvalidParameterException;
	use app\queue\SyncClockJob;
	use app\util\DateUtil;
	use app\util\SUtils;
	use app\util\WorkUtils;
	use app\util\WxPay\RedPacketPay;
	use dovechen\yii2\weWork\src\dataStructure\ExternalContactWay;
	use Yii;
	use yii\helpers\Json;

	/**
	 * This is the model class for table "{{%work_group_clock_prize}}".
	 *
	 * @property int                $id
	 * @property int                $join_id      参与者ID
	 * @property int                $task_id      任务ID
	 * @property int                $send         0未发送1已发送
	 * @property int                $send_time    发送时间
	 * @property int                $days         打卡天数
	 * @property int                $type         奖品类型 1实物 2红包
	 * @property string             $money_amount 红包金额
	 * @property string             $reward_name  奖品名称
	 * @property int                $create_time  创建时间
	 *
	 * @property WorkGroupClockJoin $join
	 * @property WorkGroupClockTask $task
	 */
	class WorkGroupClockPrize extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_group_clock_prize}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['join_id', 'task_id', 'send', 'days', 'send_time', 'create_time'], 'integer'],
				[['money_amount'], 'number'],
				[['reward_name'], 'string', 'max' => 50],
				[['join_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkGroupClockJoin::className(), 'targetAttribute' => ['join_id' => 'id']],
				[['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkGroupClockTask::className(), 'targetAttribute' => ['task_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'           => 'ID',
				'join_id'      => '参与者ID',
				'task_id'      => '任务ID',
				'send'         => '0未发送1已发送',
				'send_time'    => '发送时间',
				'days'         => '打卡天数',
				'type'         => '奖品类型 1实物 2红包',
				'money_amount' => '红包金额',
				'reward_name'  => '奖品名称',
				'create_time'  => '创建时间',
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getJoin ()
		{
			return $this->hasOne(WorkGroupClockJoin::className(), ['id' => 'join_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getTask ()
		{
			return $this->hasOne(WorkGroupClockTask::className(), ['id' => 'task_id']);
		}

		/**
		 *
		 * @return object|\yii\db\Connection|null
		 *
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function getDb ()
		{
			return Yii::$app->get('mdb');
		}

		/**
		 * @param $data
		 * @param $external_userid
		 * @param $openid
		 *
		 * @return bool
		 *
		 */
		public static function addData ($data, $external_userid, $openid)
		{
			$prize = self::findOne(['join_id' => $data['join_id'], 'task_id' => $data['task_id']]);
			if (empty($prize)) {
				$prize               = new WorkGroupClockPrize();
				$prize->create_time  = time();
				$prize->join_id      = $data['join_id'];
				$prize->task_id      = $data['task_id'];
				$prize->days         = $data['days'];
				$prize->type         = $data['type'];
				$prize->money_amount = $data['money_amount'];
				$prize->reward_name  = $data['reward_name'];
				$prize->save();
				if (($data['type'] == 2) && !empty($prize->money_amount)) {
					$activity = $prize->task->activity;
					//发放零钱到客户
					$userCorp             = UserCorpRelation::findOne(['corp_id' => $activity->corp_id]);
					$uid                  = !empty($userCorp) ? $userCorp->uid : '';
					$orderData['corp_id'] = $activity->corp_id;
					$orderData['remark']  = '打卡成功，已成功发放到零钱';
					$orderData['amount']  = $data['money_amount'];
					$orderData['uid']     = $uid;
					$orderData['rid']     = $activity->id;
					$orderData['jid']     = $data['join_id'];
					$orderData['task_id'] = $data['task_id'];
					$externalId           = '';
					if (!empty($external_userid)) {
						$contact    = WorkExternalContact::findOne(['corp_id' => $activity->corp_id, 'external_userid' => $external_userid]);
						$openid     = !empty($contact) ? $contact->openid : '';
						$externalId = !empty($contact) ? $contact->id : '';
					}
					$orderData['openid']      = $openid;
					$orderData['external_id'] = $externalId;
					$result = self::sendChange($orderData);
					//补发之前未发放的
					if(isset($result['error']) && ($result['error'] == 0)){
						\Yii::$app->queue->delay(5)->push(new SyncClockJob([
							'corpId'     => $activity->corp_id,
						]));
					}
				}
			}

			return $prize;
		}

		/**
		 * @param        $orderData
		 * @param string $appid
		 *
		 * @return bool
		 *
		 */
		public static function sendChange ($orderData, $appid = '')
		{
			if (empty($orderData['corp_id']) || empty($orderData['uid']) || empty($orderData['openid'])) {
				return true;
			}
			$corp_id                      = $orderData['corp_id'];
			$remark                       = $orderData['remark'];
			$amount                       = $orderData['amount'];
			$order_id                     = '44' . date('YmdHis') . mt_rand(111111, 999999) . mt_rand(11, 99);
			$sendData                     = [];
			$sendData['partner_trade_no'] = $order_id;
			$sendData['openid']           = $orderData['openid'];
			$sendData['amount']           = $amount * 100;
			$sendData['desc']             = $remark;

			try {
				$redPacketPay = new RedPacketPay();
				$resData      = $redPacketPay->RedPacketSend($corp_id, $sendData, $appid);
				\Yii::error($sendData, 'sendData-PuchCard');
				\Yii::error($resData, 'resData-PuchCard');
				if ($resData['return_code'] == 'SUCCESS' && $resData['result_code'] == 'SUCCESS') {
					$redOrder          = new RedPackOrder();
					$redOrder->uid     = $orderData['uid'];
					$redOrder->type    = 6;
					$redOrder->corp_id = $orderData['corp_id'];
					$redOrder->rid     = $orderData['rid'];
					$redOrder->jid     = $orderData['jid'];
					if (isset($orderData['hid'])) {
						$redOrder->hid = $orderData['hid'];
					}
					$redOrder->external_id    = $orderData['external_id'];
					$redOrder->openid         = $orderData['openid'];
					$redOrder->amount         = $amount;
					$redOrder->order_id       = $order_id;
					$redOrder->ispay          = 1;
					$redOrder->pay_time       = $resData['payment_time'];
					$redOrder->transaction_id = $resData['payment_no'];
					$redOrder->remark         = $remark;
					$redOrder->send_time      = time();

					if (!$redOrder->validate() || !$redOrder->save()) {
						\Yii::error(SUtils::modelError($redOrder), '$redOrder-PunchCard');
					}

					$prize = self::findOne(['task_id' => $orderData['task_id'], 'join_id' => $orderData['jid']]);
					if (empty($prize->send)) {
						$prize->send      = 1;
						$prize->send_time = time();
						$prize->save();
					}

					return ['error' => 0];
				} else {
					$msg = isset($resData['err_code_des']) && !empty($resData['err_code_des']) ? $resData['err_code_des'] : '';
					$msg = empty($msg) && isset($resData['return_msg']) ? $resData['return_msg'] : $msg;

					return ['error' => 1, 'msg' => $msg];
				}
			} catch (\Exception $e) {
				\Yii::error($e->getMessage(), 'getMessage-PunchCard');

				return ['error' => 1, 'msg' => $e->getMessage()];
			}
		}

		//如果微信支付有钱，补发之前没发的红包
		public static function supplySend ($corpId)
		{
			$cacheSendKey = 'supplySend_clock_' . $corpId;
			$cacheSend    = \Yii::$app->cache->get($cacheSendKey);
			if (!empty($cacheSend)) {
				return '';
			}
			\Yii::$app->cache->set($cacheSendKey, 1, 600);
			$userCorp     = UserCorpRelation::findOne(['corp_id' => $corpId]);
			$activityList = WorkGroupClockActivity::find()->where(['corp_id' => $corpId, 'status' => [1, 2, 3], 'is_del' => 0])->select('id')->all();
			if (!empty($activityList)) {
				/**@var WorkGroupClockActivity $activity * */
				foreach ($activityList as $activity) {
					$prizeList = WorkGroupClockPrize::find()->alias('cp');
					$prizeList = $prizeList->leftJoin('{{%work_group_clock_join}} cj', 'cj.id = cp.join_id');
					$prizeList = $prizeList->where(['cp.send' => 0, 'cp.type' => 2, 'cj.activity_id' => $activity->id]);
					$prizeList = $prizeList->select('cj.external_id,cj.openid,cp.join_id,cp.task_id,cp.money_amount');
					$prizeList = $prizeList->asArray()->all();
					foreach ($prizeList as $prize) {
						if (!empty($prize['money_amount'])) {
							$orderData            = [];
							$orderData['corp_id'] = $corpId;
							$orderData['remark']  = '打卡成功，已成功发放到零钱';
							$orderData['amount']  = $prize['money_amount'];
							$orderData['uid']     = $userCorp->uid;
							$orderData['rid']     = $activity->id;
							$orderData['jid']     = $prize['join_id'];
							$orderData['task_id'] = $prize['task_id'];
							if (!empty($prize['external_id'])) {
								$contact    = WorkExternalContact::findOne($prize['external_id']);
								$openid     = !empty($contact) ? $contact->openid : '';
								$externalId = !empty($contact) ? $contact->id : '';
							} else {
								$openid     = $prize['openid'];
								$externalId = '';
							}
							if (empty($openid)) {
								continue;
							}
							$orderData['openid']      = $openid;
							$orderData['external_id'] = $externalId;
							WorkGroupClockPrize::sendChange($orderData);
						}
					}
				}
			}

			\Yii::$app->cache->delete($cacheSendKey);
		}
	}
