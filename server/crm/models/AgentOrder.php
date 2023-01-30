<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%agent_order}}".
	 *
	 * @property int    $id
	 * @property int    $agent_uid      代理商用户id
	 * @property int    $uid            用户ID
	 * @property int    $type           订单类别 1：新开、2：延期、3：升级、4：降级、5：重新入驻
	 * @property double $money          实际价格
	 * @property double $discount       折扣
	 * @property double $original_price 原价
	 * @property int    $status         订单状态 1：未审核、2：已审核、3：已撤销
	 * @property int    $package_id     套餐ID
	 * @property int    $package_time   套餐时长
	 * @property int    $time_type      套餐时长类型:1日2月3年
	 * @property int    $end_time       套餐失效时间
	 * @property int    $create_time    创建时间
	 * @property string $update_time    更新时间
	 * @property int    $pass_time      提单审核时间
	 * @property int    $eid            代理商员工id
	 * @property string $extrainfo      额外信息
	 * @property int    $agent_type     帐号类型 0总账号 1代理商
	 */
	class AgentOrder extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%agent_order}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['agent_uid', 'uid', 'agent_type', 'type', 'status', 'package_id', 'package_time', 'time_type', 'end_time', 'create_time', 'pass_time', 'eid'], 'integer'],
				[['money', 'discount', 'original_price'], 'number'],
				[['create_time'], 'required'],
				[['update_time'], 'safe'],
				[['extrainfo'], 'string'],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'             => Yii::t('app', 'ID'),
				'agent_uid'      => Yii::t('app', '代理商用户id'),
				'uid'            => Yii::t('app', '用户ID'),
				'type'           => Yii::t('app', '订单类别 1：新开、2：延期、3：升级、4：降级、5：重新入驻'),
				'money'          => Yii::t('app', '实际价格'),
				'discount'       => Yii::t('app', '折扣'),
				'original_price' => Yii::t('app', '原价'),
				'status'         => Yii::t('app', '订单状态 1：未审核、2：已审核、3：已撤销'),
				'package_id'     => Yii::t('app', '套餐ID'),
				'package_time'   => Yii::t('app', '套餐时长'),
				'time_type'      => Yii::t('app', '套餐时长类型:1日2月3年'),
				'end_time'       => Yii::t('app', '套餐失效时间'),
				'create_time'    => Yii::t('app', '创建时间'),
				'update_time'    => Yii::t('app', '更新时间'),
				'pass_time'      => Yii::t('app', '提单审核时间'),
				'eid'            => Yii::t('app', '代理商员工id'),
				'extrainfo'      => Yii::t('app', '额外信息'),
				'agent_type'     => Yii::t('app', '帐号类型 0总账号 1代理商'),
			];
		}

		/**
		 * @param array $orderData
		 *
		 * @return User|null
		 * @throws InvalidDataException
		 */
		public static function create ($orderData)
		{
			if (empty($orderData['agent_uid']) || empty($orderData['uid'])) {
				throw new InvalidDataException('数据错误！');
			}
			$agent = Agent::findOne(['uid' => $orderData['agent_uid']]);
			if (empty($agent)) {
				throw new InvalidDataException('代理商数据错误！');
			}
			$existOrder = static::findOne(['agent_uid' => $orderData['agent_uid'], 'uid' => $orderData['uid'], 'status' => 1]);
			if (!empty($existOrder)) {
				throw new InvalidDataException('客户存在未审核的提单，需撤销后才能重新提单！');
			}

			$agentOrder                 = new AgentOrder();
			$agentOrder->agent_uid      = $agent->uid;
			$agentOrder->eid            = $orderData['eid'];
			$agentOrder->uid            = $orderData['uid'];
			$agentOrder->agent_type     = $orderData['agent_type'] ? $orderData['agent_type'] : 0;
			$agentOrder->type           = $orderData['type'];
			$agentOrder->original_price = $orderData['original_price'];
			$agentOrder->discount       = $agent->discount;

			if ($agentOrder->type == 3 && isset($orderData['money'])) {
				$agentOrder->money = $orderData['money'];
			} else {
				$agentOrder->money = $agent->discount * $orderData['original_price'];
			}
			if ($agentOrder->type == 2 && isset($orderData['discount'])) {
				$agentOrder->discount = $agent->discount * $orderData['discount'];
				$agentOrder->money    = $agent->discount * $orderData['original_price'] * $orderData['discount'];
			}
			$agentOrder->status         = $orderData['status'];
			$agentOrder->package_id     = $orderData['package_id'];
			$agentOrder->package_time   = $orderData['package_time'];
			$agentOrder->time_type      = $orderData['time_type'];
			$agentOrder->extrainfo      = $orderData['extrainfo'];
			$agentOrder->create_time    = time();

			if ($agentOrder->validate() && $agentOrder->save()) {
				return true;
			} else {
				throw new InvalidDataException(SUtils::modelError($agentOrder));
			}
		}
	}
