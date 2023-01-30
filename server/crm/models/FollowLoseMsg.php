<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%follow_lose_msg}}".
	 *
	 * @property int          $id
	 * @property int          $corp_id     企业id
	 * @property int          $uid         主账号id
	 * @property int          $sub_id      子账号id
	 * @property int          $user_id     成员id
	 * @property string       $context     原因
	 * @property int          $status      状态
	 * @property int          $sort        排序
	 * @property int          $create_time 创建时间
	 * @property int          $update_time 修改时间
	 *
	 * @property WorkCorp     $corp
	 * @property SubUser      $sub
	 * @property WorkUser     $user
	 */
	class FollowLoseMsg extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%follow_lose_msg}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_id', 'uid', 'sub_id', 'user_id', 'status', 'sort', 'create_time', 'update_time'], 'integer'],
				[['context'], 'string'],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
				[['sub_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubUser::className(), 'targetAttribute' => ['sub_id' => 'sub_id']],
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
				'corp_id'     => Yii::t('app', '企业id'),
				'uid'         => Yii::t('app', '主账号id'),
				'sub_id'      => Yii::t('app', '子账号id'),
				'user_id'     => Yii::t('app', '成员id'),
				'context'     => Yii::t('app', '原因'),
				'status'      => Yii::t('app', '状态'),
				'sort'        => Yii::t('app', '排序'),
				'create_time' => Yii::t('app', '创建时间'),
				'update_time' => Yii::t('app', '修改时间'),
			];
		}



		public static function getDb ()
		{
			return Yii::$app->get('mdb');
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
		public function getSub ()
		{
			return $this->hasOne(SubUser::className(), ['sub_id' => 'sub_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getUser ()
		{
			return $this->hasOne(WorkUser::className(), ['id' => 'user_id']);
		}
	}
