<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%work_moment_user_config}}".
	 *
	 * @property int      $id
	 * @property int      $corp_id     企业ID
	 * @property int      $user_id     成员ID
	 * @property int      $heard       自定义头像
	 * @property string   $banner_info 背景图设置，最多5个
	 * @property string   $description 签名
	 * @property int      $status      状态：0、关闭；1：开启
	 * @property string   $create_time 创建时间
	 *
	 * @property WorkUser $user
	 * @property WorkCorp $corp
	 */
	class WorkMomentUserConfig extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_moment_user_config}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_id', 'user_id', 'status'], 'integer'],
				[['banner_info', 'heard'], 'string'],
				[['create_time'], 'safe'],
				[['description'], 'string', 'max' => 64],
				[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkUser::className(), 'targetAttribute' => ['user_id' => 'id']],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => Yii::t('app', 'ID'),
				'corp_id'     => Yii::t('app', '企业ID'),
				'user_id'     => Yii::t('app', '成员ID'),
				'heard'       => Yii::t('app', '自定义头像'),
				'banner_info' => Yii::t('app', '背景图设置，最多5个'),
				'description' => Yii::t('app', '签名'),
				'status'      => Yii::t('app', '状态：0、关闭；1：开启'),
				'create_time' => Yii::t('app', '创建时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getUser ()
		{
			return $this->hasOne(WorkUser::className(), ['id' => 'user_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getCorp ()
		{
			return $this->hasOne(WorkCorp::className(), ['id' => 'corp_id']);
		}
	}
