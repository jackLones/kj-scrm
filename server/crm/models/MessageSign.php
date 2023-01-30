<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%message_sign}}".
	 *
	 * @property int               $id
	 * @property int               $uid        用户ID
	 * @property string            $title      短信签名
	 * @property int               $status     状态，-1：删除、0：待审核、1：已审核、2：审核失败
	 * @property string            $error_msg  失败原因
	 * @property string            $apply_time 申请时间
	 *
	 * @property MessagePush[]     $messagePushes
	 * @property User              $u
	 * @property MessageTemplate[] $messageTemplates
	 */
	class MessageSign extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%message_sign}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['uid', 'status'], 'integer'],
				[['apply_time'], 'safe'],
				[['title'], 'string', 'max' => 50],
				[['error_msg'], 'string', 'max' => 250],
				[['uid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['uid' => 'uid']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'         => Yii::t('app', 'ID'),
				'uid'        => Yii::t('app', '用户ID'),
				'title'      => Yii::t('app', '短信签名'),
				'status'     => Yii::t('app', '状态，-1：删除、0：待审核、1：已审核、2：审核失败'),
				'error_msg'  => Yii::t('app', '失败原因'),
				'apply_time' => Yii::t('app', '申请时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getMessagePushes ()
		{
			return $this->hasMany(MessagePush::className(), ['sign_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getU ()
		{
			return $this->hasOne(User::className(), ['uid' => 'uid']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getMessageTemplates ()
		{
			return $this->hasMany(MessageTemplate::className(), ['sign_id' => 'id']);
		}
	}
