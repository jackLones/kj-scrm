<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%auth_store_status}}".
	 *
	 * @property int      $id
	 * @property int      $corp_id     企业微信
	 * @property int      $status      门店状态
	 * @property string   $create_time 创建时间
	 *
	 * @property WorkCorp $corp
	 */
	class AuthStoreStatus extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%auth_store_status}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_id', 'status'], 'integer'],
				[['create_time'], 'safe'],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
			];
		}

		public static function getDb ()
		{
			return Yii::$app->get('mdb');
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => Yii::t('app', 'ID'),
				'corp_id'     => Yii::t('app', '企业微信'),
				'status'      => Yii::t('app', '门店状态'),
				'create_time' => Yii::t('app', '创建时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getCorp ()
		{
			return $this->hasOne(WorkCorp::className(), ['id' => 'corp_id']);
		}
	}
