<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\SUtils;
	use Yii;
	use yii\helpers\Json;

	/**
	 * This is the model class for table "{{%work_user_external_profile}}".
	 *
	 * @property int      $id
	 * @property int      $user_id            成员ID
	 * @property string   $external_position  对外职务，如果设置了该值，则以此作为对外展示的职务，否则以position来展示。
	 * @property string   $external_corp_name 企业对外简称，需从已认证的企业简称中选填。可在“我的企业”页中查看企业简称认证状态
	 * @property string   $external_attr      属性列表，目前支持文本、网页、小程序三种类型
	 *
	 * @property WorkUser $user
	 */
	class WorkUserExternalProfile extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_user_external_profile}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['user_id'], 'integer'],
				[['external_position', 'external_corp_name'], 'string', 'max' => 64],
				[['external_attr'], 'string', 'max' => 255],
				[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkUser::className(), 'targetAttribute' => ['user_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'                 => Yii::t('app', 'ID'),
				'user_id'            => Yii::t('app', '成员ID'),
				'external_position'  => Yii::t('app', '对外职务，如果设置了该值，则以此作为对外展示的职务，否则以position来展示。'),
				'external_corp_name' => Yii::t('app', '企业对外简称，需从已认证的企业简称中选填。可在“我的企业”页中查看企业简称认证状态'),
				'external_attr'      => Yii::t('app', '属性列表，目前支持文本、网页、小程序三种类型'),
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
		 * @param int   $userId
		 * @param array $externalInfo
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 */
		public static function setExternalProfile ($userId, $externalInfo)
		{
			$workUserExternalProfile = static::findOne(['user_id' => $userId]);

			if (empty($workUserExternalProfile)) {
				$workUserExternalProfile          = new WorkUserExternalProfile();
				$workUserExternalProfile->user_id = $userId;
			}

			if (!empty($externalInfo['external_position'])) {
				$workUserExternalProfile->external_position = $externalInfo['external_position'];
			}

			if (!empty($externalInfo['external_corp_name'])) {
				$workUserExternalProfile->external_corp_name = $externalInfo['external_corp_name'];
			}

			if (!empty($externalInfo['external_attr'])) {
				$workUserExternalProfile->external_attr = Json::encode($externalInfo['external_attr'], JSON_UNESCAPED_UNICODE);
			}

			if ($workUserExternalProfile->dirtyAttributes) {
				if (!$workUserExternalProfile->validate() || !$workUserExternalProfile->save()) {
					throw new InvalidDataException(SUtils::modelError($workUserExternalProfile));
				}
			}

			return $workUserExternalProfile->id;
		}
	}
