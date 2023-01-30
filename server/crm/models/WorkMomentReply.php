<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%work_moment_reply}}".
	 *
	 * @property int                 $id
	 * @property int                 $moment_id   朋友圈ID
	 * @property int                 $reply_id    回复ID
	 * @property int                 $user_id     成员ID
	 * @property int                 $external_id 外部联系人ID
	 * @property string              $openid      外部非联系人openid
	 * @property string              $content     回复内容
	 * @property int                 $status      状态：0、删除；1：正常
	 * @property string              $del_time    删除时间
	 * @property string              $create_time 创建时间
	 *
	 * @property WorkExternalContact $external
	 * @property WorkMoments         $moment
	 * @property WorkMomentReply     $reply
	 * @property WorkMomentReply[]   $workMomentReplies
	 * @property WorkUser            $user
	 */
	class WorkMomentReply extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_moment_reply}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['moment_id', 'reply_id', 'user_id', 'external_id', 'status'], 'integer'],
				[['content'], 'required'],
				[['del_time', 'create_time'], 'safe'],
				[['openid'], 'string', 'max' => 64],
				[['content'], 'string'],
				[['external_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkExternalContact::className(), 'targetAttribute' => ['external_id' => 'id']],
				[['moment_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMoments::className(), 'targetAttribute' => ['moment_id' => 'id']],
				[['reply_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMomentReply::className(), 'targetAttribute' => ['reply_id' => 'id']],
				[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkUser::className(), 'targetAttribute' => ['user_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => Yii::t('app', 'ID'),
				'moment_id'   => Yii::t('app', '朋友圈ID'),
				'reply_id'    => Yii::t('app', '回复ID'),
				'user_id'     => Yii::t('app', '成员ID'),
				'external_id' => Yii::t('app', '外部联系人ID'),
				'openid'      => Yii::t('app', '外部非联系人openid'),
				'content'     => Yii::t('app', '回复内容'),
				'status'      => Yii::t('app', '状态：0、删除；1：正常'),
				'del_time'    => Yii::t('app', '删除时间'),
				'create_time' => Yii::t('app', '创建时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getExternal ()
		{
			return $this->hasOne(WorkExternalContact::className(), ['id' => 'external_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getMoment ()
		{
			return $this->hasOne(WorkMoments::className(), ['id' => 'moment_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getReply ()
		{
			return $this->hasOne(WorkMomentReply::className(), ['id' => 'reply_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMomentReplies ()
		{
			return $this->hasMany(WorkMomentReply::className(), ['reply_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getUser ()
		{
			return $this->hasOne(WorkUser::className(), ['id' => 'user_id']);
		}
	}
