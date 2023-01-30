<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%work_user_author_relation}}".
	 *
	 * @property int           $id
	 * @property int           $corp_id     企业微信ID
	 * @property int           $user_id     企业微信成员ID
	 * @property int           $author_id   公众号ID
	 * @property int           $agent_id    企业微信应用ID
	 * @property int           $status      状态：0、关闭；1：开启
	 * @property string        $create_time 创建时间
	 *
	 * @property WorkCorpAgent $agent
	 * @property WxAuthorize   $author
	 * @property WorkCorp      $corp
	 * @property WorkUser      $user
	 */
	class WorkUserAuthorRelation extends \yii\db\ActiveRecord
	{
		const SEND_CLOSE = 0;
		const SEND_OPEN = 1;

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_user_author_relation}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_id', 'user_id', 'author_id', 'agent_id', 'status'], 'integer'],
				[['create_time'], 'safe'],
				[['agent_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorpAgent::className(), 'targetAttribute' => ['agent_id' => 'id']],
				[['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => WxAuthorize::className(), 'targetAttribute' => ['author_id' => 'author_id']],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
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
				'corp_id'     => Yii::t('app', '企业微信ID'),
				'user_id'     => Yii::t('app', '企业微信成员ID'),
				'author_id'   => Yii::t('app', '公众号ID'),
				'agent_id'    => Yii::t('app', '企业微信应用ID'),
				'status'      => Yii::t('app', '状态：0、关闭；1：开启'),
				'create_time' => Yii::t('app', '创建时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getAgent ()
		{
			return $this->hasOne(WorkCorpAgent::className(), ['id' => 'agent_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getAuthor ()
		{
			return $this->hasOne(WxAuthorize::className(), ['author_id' => 'author_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getCorp ()
		{
			return $this->hasOne(WorkCorp::className(), ['id' => 'corp_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getUser ()
		{
			return $this->hasOne(WorkUser::className(), ['id' => 'user_id']);
		}
	}
