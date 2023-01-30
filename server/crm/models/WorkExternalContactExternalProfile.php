<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\SUtils;
	use Yii;
	use yii\helpers\Json;

	/**
	 * This is the model class for table "{{%work_external_contact_external_profile}}".
	 *
	 * @property int                 $id
	 * @property int                 $external_userid    外部联系人ID
	 * @property string              $external_corp_name 企业对外简称，需从已认证的企业简称中选填。可在“我的企业”页中查看企业简称认证状态
	 * @property string              $external_attr      属性列表，目前支持文本、网页、小程序三种类型
	 *
	 * @property WorkExternalContact $externalUser
	 */
	class WorkExternalContactExternalProfile extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_external_contact_external_profile}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['external_userid'], 'integer'],
				[['external_corp_name'], 'string', 'max' => 64],
				[['external_userid'], 'exist', 'skipOnError' => true, 'targetClass' => WorkExternalContact::className(), 'targetAttribute' => ['external_userid' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'                 => Yii::t('app', 'ID'),
				'external_userid'    => Yii::t('app', '外部联系人ID'),
				'external_corp_name' => Yii::t('app', '企业对外简称，需从已认证的企业简称中选填。可在“我的企业”页中查看企业简称认证状态'),
				'external_attr'      => Yii::t('app', '属性列表，目前支持文本、网页、小程序三种类型'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getExternalUser ()
		{
			return $this->hasOne(WorkExternalContact::className(), ['id' => 'external_userid']);
		}

		/**
		 * @param int   $externalUserId
		 * @param array $externalInfo
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 */
		public static function setExternalProfile ($externalUserId, $externalInfo)
		{
			$externalContactExternalProfile = static::findOne(['external_userid' => $externalUserId]);

			if (empty($externalContactExternalProfile)) {
				$externalContactExternalProfile                  = new WorkExternalContactExternalProfile();
				$externalContactExternalProfile->external_userid = $externalUserId;
			}

			if (!empty($externalInfo['external_corp_name'])) {
				$externalContactExternalProfile->external_corp_name = $externalInfo['external_corp_name'];
			}

			if (!empty($externalInfo['external_attr'])) {
				$externalContactExternalProfile->external_attr = Json::encode($externalInfo['external_attr'], JSON_UNESCAPED_UNICODE);
			}

			if ($externalContactExternalProfile->dirtyAttributes) {
				if (!$externalContactExternalProfile->validate() || !$externalContactExternalProfile->save()) {
					throw new InvalidDataException(SUtils::modelError($externalContactExternalProfile));
				}
			}

			return $externalContactExternalProfile->id;
		}
	}
