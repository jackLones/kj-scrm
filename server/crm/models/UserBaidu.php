<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%user_baidu}}".
	 *
	 * @property int    $id
	 * @property int    $uid         用户ID
	 * @property string $token       token
	 * @property string $update_time 更新时间
	 * @property string $create_time 创建时间
	 *
	 * @property User   $u
	 */
	class UserBaidu extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%user_baidu}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['uid'], 'integer'],
				[['update_time', 'create_time'], 'safe'],
				[['token'], 'string', 'max' => 128],
				[['uid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['uid' => 'uid']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => Yii::t('app', 'ID'),
				'uid'         => Yii::t('app', '用户ID'),
				'token'       => Yii::t('app', 'token'),
				'update_time' => Yii::t('app', '更新时间'),
				'create_time' => Yii::t('app', '创建时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getU ()
		{
			return $this->hasOne(User::className(), ['uid' => 'uid']);
		}
	}
