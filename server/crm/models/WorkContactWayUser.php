<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%work_contact_way_user}}".
	 *
	 * @property int            $id
	 * @property int            $config_id 联系方式的配置id
	 * @property int            $user_id   成员ID
	 *
	 * @property WorkUser       $user
	 * @property WorkContactWay $config
	 */
	class WorkContactWayUser extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_contact_way_user}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['config_id', 'user_id'], 'integer'],
				[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkUser::className(), 'targetAttribute' => ['user_id' => 'id']],
				[['config_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkContactWay::className(), 'targetAttribute' => ['config_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'        => Yii::t('app', 'ID'),
				'config_id' => Yii::t('app', '联系方式的配置id'),
				'user_id'   => Yii::t('app', '成员ID'),
			];
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
		public function getConfig ()
		{
			return $this->hasOne(WorkContactWay::className(), ['id' => 'config_id']);
		}

		/**
		 * @param $configId
		 * @param $userId
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 */
		public static function setData ($configId, $userId)
		{
			$wayUser = static::findOne(['config_id' => $configId, 'user_id' => $userId]);

			if (empty($wayUser)) {
				$wayUser = new WorkContactWayUser();
			}

			$wayUser->config_id = $configId;
			$wayUser->user_id   = $userId;

			if ($wayUser->dirtyAttributes) {
				if (!$wayUser->validate() || !$wayUser->save()) {
					throw new InvalidDataException(SUtils::modelError($wayUser));
				}
			}

			return $wayUser->id;
		}
	}
