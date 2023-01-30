<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\SUtils;
	use app\util\WxPay\RedPacketPay;
	use Yii;
	use yii\db\Expression;

	/**
	 * This is the model class for table "{{%red_pack_order}}".
	 *
	 * @property int    $id
	 * @property int    $uid            账户ID
	 * @property int    $corp_id        企业ID
	 * @property int    $type           活动类型：1、红包引流，2、裂变引流，3、抽奖引流，4、红包拉新，5、红包群发，6、群打卡
	 * @property int    $rid            裂变任务id
	 * @property int    $jid            参与表id
	 * @property int    $hid            好友助力表id
	 * @property int    $external_id    外部联系人id
	 * @property string $openid         外部联系人openid
	 * @property string $amount         红包金额
	 * @property string $order_id       支付订单号
	 * @property int    $ispay          是否支付1是0否
	 * @property string $pay_time       支付时间
	 * @property string $transaction_id 第三方支付订单号
	 * @property string $remark         备注
	 * @property int    $send_time      发送时间
	 * @property int    $send_type      发送类型：1、首拆，2、首拆剩余，3、好友拆
	 *
	 * @property User   $u
	 */
	class RedPackOrder extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%red_pack_order}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['uid', 'corp_id', 'rid', 'jid', 'hid', 'external_id', 'ispay', 'send_time'], 'integer'],
				[['amount'], 'number'],
				[['pay_time'], 'safe'],
				[['openid'], 'string', 'max' => 64],
				[['order_id', 'transaction_id'], 'string', 'max' => 50],
				[['remark'], 'string', 'max' => 100],
				[['uid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['uid' => 'uid']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'             => Yii::t('app', 'ID'),
				'uid'            => Yii::t('app', '账户ID'),
				'corp_id'        => Yii::t('app', '企业ID'),
				'type'           => Yii::t('app', '活动类型：1、红包引流，2、裂变引流，3、抽奖引流，4、红包拉新，5、红包群发'),
				'rid'            => Yii::t('app', '裂变任务id'),
				'jid'            => Yii::t('app', '参与表id'),
				'hid'            => Yii::t('app', '好友助力表id'),
				'external_id'    => Yii::t('app', '外部联系人id'),
				'openid'         => Yii::t('app', '外部联系人openid'),
				'amount'         => Yii::t('app', '红包金额'),
				'order_id'       => Yii::t('app', '支付订单号'),
				'ispay'          => Yii::t('app', '是否支付1是0否'),
				'pay_time'       => Yii::t('app', '支付时间'),
				'transaction_id' => Yii::t('app', '第三方支付订单号'),
				'remark'         => Yii::t('app', '备注'),
				'send_time'      => Yii::t('app', '发送时间'),
				'send_type'      => Yii::t('app', '发送类型：1、首拆，2、首拆剩余，3、好友拆'),
			];
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
		 * @return \yii\db\ActiveQuery
		 */
		public function getU ()
		{
			return $this->hasOne(User::className(), ['uid' => 'uid']);
		}

		/*
		 * 红包发送
		 * $orderData 发送数据['uid'=>'账户id','corp_id'=>'企业id','rid'=>'活动id','jid'=>'参与表id','hid'=>'好友助力表id','external_id'=>'外部联系人id','openid'=>'外部联系人openid','amount'=>'红包金额','remark'=>'备注','send_type'=>'发送类型']
		 * $type 1、红包引流，2、裂变引流，3、抽奖引流
		 */
		public static function sendRedPack ($orderData, $type = 1,$appid='')
		{

			if (empty($orderData['corp_id']) || empty($orderData['uid']) || empty($orderData['openid'])) {
				throw new InvalidDataException('参数不正确');
			}
			$corp_id                      = $orderData['corp_id'];
			$remark                       = $orderData['remark'];
			$amount                       = $orderData['amount'];
			$send_type                    = !empty($orderData['send_type']) ? $orderData['send_type'] : 0;
			$order_id                     = '44' . date('YmdHis') . mt_rand(111111, 999999) . mt_rand(11, 99);
			$sendData                     = [];
			$sendData['partner_trade_no'] = $order_id;
			$sendData['openid']           = $orderData['openid'];
			$sendData['amount']           = $amount * 100;
			$sendData['desc']             = $remark;

			try {
				$redPacketPay = new RedPacketPay();
				$resData      = $redPacketPay->RedPacketSend($corp_id, $sendData,$appid);
				\Yii::error($sendData, 'sendData');
				\Yii::error($resData, 'resData');
				if ($resData['return_code'] == 'SUCCESS' && $resData['result_code'] == 'SUCCESS') {
					$redOrder          = new RedPackOrder();
					$redOrder->uid     = $orderData['uid'];
					$redOrder->type    = $type;
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
					$redOrder->send_type      = $send_type;

					if (!$redOrder->validate() || !$redOrder->save()) {
						throw new InvalidDataException(SUtils::modelError($redOrder));
					}

					return true;
				} else {
					$msg = isset($resData['err_code_des']) && !empty($resData['err_code_des']) ? $resData['err_code_des'] : '';
					$msg = empty($msg) && isset($resData['return_msg']) ? $resData['return_msg'] : $msg;
					throw new InvalidDataException($msg);
				}
			} catch (\Exception $e) {
				throw new InvalidDataException($e->getMessage());
			}
		}

		/*
		 * 获取活动的已发放金额
		 * $rid 活动id
		 * $type 1、红包引流，2、裂变引流，3、抽奖引流
		 */
		public static function getGiveOut ($rid, $type = 1)
		{
			$select    = new Expression('sum(amount) amount');
			$orderInfo = static::find()->where(['rid' => $rid, 'ispay' => 1, 'type' => $type])->select($select)->one();

			$amount = !empty($orderInfo['amount']) ? $orderInfo['amount'] : '0';

			return (string) $amount;
		}
	}
