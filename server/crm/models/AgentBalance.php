<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%agent_balance}}".
	 *
	 * @property int    $id
	 * @property int    $uid           代理商用户id
	 * @property double $balance       金额
	 * @property int    $type          金额变化类别 0：减少、1：增加
	 * @property int    $blance_type   明细类型1充值 2提单 9其他
	 * @property string $des           描述
	 * @property int    $order_id      订单ID
	 * @property int    $operator_type 操作者类别1总后台2财务3代理商
	 * @property int    $time          创建时间
	 */
	class AgentBalance extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%agent_balance}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['uid', 'type', 'blance_type', 'order_id', 'operator_type', 'time'], 'integer'],
				[['balance'], 'number'],
				[['des'], 'string'],
				[['time'], 'required'],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'            => Yii::t('app', 'ID'),
				'uid'           => Yii::t('app', '代理商用户id'),
				'balance'       => Yii::t('app', '金额'),
				'type'          => Yii::t('app', '金额变化类别 0：减少、1：增加'),
				'blance_type'   => Yii::t('app', '明细类型1充值 2提单 9其他'),
				'des'           => Yii::t('app', '描述'),
				'order_id'      => Yii::t('app', '订单ID'),
				'operator_type' => Yii::t('app', '操作者类别1总后台2财务3代理商'),
				'time'          => Yii::t('app', '创建时间'),
			];
		}

		/**
		 * @param array $balanceData
		 *
		 * @return User|null
		 * @throws InvalidDataException
		 */
		public static function create ($balanceData)
		{
			$balance                = new AgentBalance();
			$balance->uid           = $balanceData['uid'];
			$balance->balance       = $balanceData['balance'];
			$balance->type          = $balanceData['type'];
			$balance->blance_type   = $balanceData['blance_type'];
			$balance->des           = $balanceData['des'];
			$balance->operator_type = $balanceData['operator_type'];
			$balance->order_id      = isset($balanceData['order_id']) ? $balanceData['order_id'] : 0;
			$balance->time          = time();

			if ($balance->validate() && $balance->save()) {
				return true;
			} else {
				throw new InvalidDataException(SUtils::modelError($balance));
			}
		}
	}
