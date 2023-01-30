<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%money_set}}".
	 *
	 * @property int    $id
	 * @property int    $uid      商户id
	 * @property int    $corp_id  企业微信id
	 * @property int    $sub_id   子账户ID
	 * @property string $money    金额
	 * @property int    $send_num 发送次数
	 * @property int    $status   1启用2禁用3删除
	 * @property int    $time     设置时间
	 */
	class MoneySet extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%money_set}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['uid', 'corp_id', 'sub_id', 'send_num', 'status', 'time'], 'integer'],
				[['money'], 'required'],
				[['money'], 'number'],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'       => Yii::t('app', 'ID'),
				'uid'      => Yii::t('app', '商户id'),
				'corp_id'  => Yii::t('app', '企业微信id'),
				'sub_id'   => Yii::t('app', '子账户ID'),
				'money'    => Yii::t('app', '金额'),
				'send_num' => Yii::t('app', '发送次数'),
				'status'   => Yii::t('app', '1启用2禁用3删除'),
				'time'     => Yii::t('app', '设置时间'),
			];
		}
	}
