<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%work_tag_user}}".
	 *
	 * @property int      $id
	 * @property int      $tag_id  授权的企业的标签ID
	 * @property int      $user_id 授权的企业的成员ID
	 *
	 * @property WorkTag  $tag
	 * @property WorkUser $user
	 */
	class WorkTagUser extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_tag_user}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['tag_id', 'user_id'], 'integer'],
				[['tag_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkTag::className(), 'targetAttribute' => ['tag_id' => 'id']],
				[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkUser::className(), 'targetAttribute' => ['user_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'      => Yii::t('app', 'ID'),
				'tag_id'  => Yii::t('app', '授权的企业的标签ID'),
				'user_id' => Yii::t('app', '授权的企业的成员ID'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getTag ()
		{
			return $this->hasOne(WorkTag::className(), ['id' => 'tag_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getUser ()
		{
			return $this->hasOne(WorkUser::className(), ['id' => 'user_id']);
		}

		/**
		 * @param $tagId
		 * @param $userId
		 *
		 * @return int
		 */
		public static function setTagUser ($tagId, $userId)
		{
			$workTagUser = static::findOne(['tag_id' => $tagId, 'user_id' => $userId]);

			if (empty($workTag)) {
				$workTagUser = new WorkTagUser();

				$workTagUser->tag_id  = $tagId;
				$workTagUser->user_id = $userId;

				$workTagUser->save();
			}

			return $workTagUser->id;
		}

		/**
		 *
		 * @return object|\yii\db\Connection|null
		 *
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function getDb ()
		{
			return Yii::$app->get('mdb');
		}
	}
