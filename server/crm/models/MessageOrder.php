<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\DateUtil;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%message_order}}".
	 *
	 * @property int    $id
	 * @property int    $uid            用户ID
	 * @property string $order_id       支付订单号
	 * @property string $pay_way        支付方式 weixin 等
	 * @property string $pay_type       支付类型
	 * @property string $goods_type     商品类型
	 * @property int    $goods_id       产品id
	 * @property string $goods_name     产品名称
	 * @property string $goods_describe 产品描述
	 * @property string $goods_price    产品价格
	 * @property int    $add_time       创建时间
	 * @property int    $paytime        支付时间
	 * @property string $truename       支付人姓名
	 * @property int    $ispay          1已支付
	 * @property string $openid
	 * @property string $transaction_id 第三方支付订单号
	 * @property string $extrainfo      额外信息
	 *
	 * @property User   $u
	 */
	class MessageOrder extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%message_order}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['uid', 'goods_id', 'ispay'], 'integer'],
				[['pay_way', 'pay_type', 'goods_type', 'add_time'], 'required'],
				[['goods_describe'], 'string'],
				[['goods_price'], 'number'],
				[['order_id', 'pay_way', 'pay_type', 'goods_type'], 'string', 'max' => 50],
				[['goods_name'], 'string', 'max' => 200],
				[['truename', 'openid', 'transaction_id'], 'string', 'max' => 250],
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
				'uid'            => Yii::t('app', '用户ID'),
				'order_id'       => Yii::t('app', '支付订单号'),
				'pay_way'        => Yii::t('app', '支付方式 weixin 等'),
				'pay_type'       => Yii::t('app', '支付类型 '),
				'goods_type'     => Yii::t('app', '商品类型'),
				'goods_id'       => Yii::t('app', '产品id'),
				'goods_name'     => Yii::t('app', '产品名称'),
				'goods_describe' => Yii::t('app', '产品描述'),
				'goods_price'    => Yii::t('app', '产品价格'),
				'add_time'       => Yii::t('app', '添加时间'),
				'paytime'        => Yii::t('app', '支付时间'),
				'truename'       => Yii::t('app', '支付人姓名'),
				'ispay'          => Yii::t('app', '1已支付'),
				'openid'         => Yii::t('app', 'Openid'),
				'transaction_id' => Yii::t('app', '第三方支付订单号'),
				'extrainfo'      => Yii::t('app', '额外信息'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getU ()
		{
			return $this->hasOne(User::className(), ['uid' => 'uid']);
		}

		//短信购买成功
		public static function paySuccess ($arrayData)
		{
			$messageOrder = static::findOne(['order_id' => $arrayData['out_trade_no']]);
			if (!empty($messageOrder) && empty($messageOrder['ispay'])) {
				$transaction = \Yii::$app->db->beginTransaction();
				try {
					$messageOrder->openid         = $arrayData['openid'];
					$messageOrder->transaction_id = $arrayData['transaction_id'];
					$messageOrder->ispay          = 1;
					$payTime                      = DateUtil::getCurrentTime();
					if (!empty($arrayData['time_end'])) {
						$payTime = date('Y-m-d H:i:s', strtotime($arrayData['time_end']));
					}
					$messageOrder->paytime = $payTime;
					if (!$messageOrder->validate() || !$messageOrder->save()) {
						throw new InvalidDataException(SUtils::modelError($messageOrder));
					}
					//短信包
					$extraInfo = json_decode($messageOrder->extrainfo, 1);
					if (empty($extraInfo['message_num'])) {
						throw new InvalidDataException('无短信个数');
					}
					$user = User::findOne($messageOrder->uid);
					$user->updateCounters(['message_num' => $extraInfo['message_num']]);
					$transaction->commit();
				} catch (InvalidDataException $e) {
					$transaction->rollBack();
					throw new InvalidDataException($e->getMessage());
				}
			}
		}
	}
