<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%work_msg_audit_info_agree}}".
	 *
	 * @property int                 $id
	 * @property int                 $audit_info_id 会话内容ID
	 * @property int                 $user_id       成员ID
	 * @property int                 $external_id   外部联系人ID
	 * @property string              $userid        同意、不同意协议者的userid，外部企业默认为external_userid
	 * @property int                 $agree_type    是否同意：0、不同意；1、同意
	 * @property string              $agree_time    同意、不同意协议的时间，utc时间，ms单位
	 *
	 * @property WorkMsgAuditInfo    $auditInfo
	 * @property WorkExternalContact $external
	 * @property WorkUser            $user
	 */
	class WorkMsgAuditInfoAgree extends \yii\db\ActiveRecord
	{
		const AGREE_MSG_TYPE = 'agree';
		const DISAGREE_MSG_TYPE = 'disagree';

		const DISAGREE_TYPE = 0;
		const AGREE_TYPE = 1;

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_msg_audit_info_agree}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['audit_info_id', 'user_id', 'external_id', 'agree_type'], 'integer'],
				[['userid'], 'string', 'max' => 64],
				[['audit_info_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAuditInfo::className(), 'targetAttribute' => ['audit_info_id' => 'id']],
				[['external_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkExternalContact::className(), 'targetAttribute' => ['external_id' => 'id']],
				[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkUser::className(), 'targetAttribute' => ['user_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'            => Yii::t('app', 'ID'),
				'audit_info_id' => Yii::t('app', '会话内容ID'),
				'user_id'       => Yii::t('app', '成员ID'),
				'external_id'   => Yii::t('app', '外部联系人ID'),
				'userid'        => Yii::t('app', '同意、不同意协议者的userid，外部企业默认为external_userid'),
				'agree_type'    => Yii::t('app', '是否同意：0、不同意；1、同意'),
				'agree_time'    => Yii::t('app', '同意、不同意协议的时间，utc时间，ms单位'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getAuditInfo ()
		{
			return $this->hasOne(WorkMsgAuditInfo::className(), ['id' => 'audit_info_id']);
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
		public function getUser ()
		{
			return $this->hasOne(WorkUser::className(), ['id' => 'user_id']);
		}

		public function dumpData ()
		{
			return [
				'agree_type' => $this->agree_type,
				'agree_time' => $this->agree_time
			];
		}

		/**
		 * @param $corpId
		 * @param $infoId
		 * @param $info
		 * @param $agreeType
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 * @throws \Throwable
		 */
		public static function create ($corpId, $infoId, $info, $agreeType)
		{
			$agreeMsg = self::findOne(['audit_info_id' => $infoId]);

			if (empty($agreeMsg)) {
				$agreeMsg                = new self();
				$agreeMsg->audit_info_id = $infoId;

				switch (SUtils::getUserType($info['userid'])) {
					case SUtils::IS_WORK_USER:
						$workUserId = WorkUser::getUserId($corpId, $info['userid']);
						if (!empty($workUserId)) {
							$agreeMsg->user_id = $workUserId;
						}

						break;
					case SUtils::IS_EXTERNAL_USER:
						$externalId = WorkExternalContact::getExternalId($corpId, $info['userid']);
						if (!empty($externalId)) {
							$agreeMsg->external_id = $externalId;
						}

						break;
					default:

						break;
				}

				$agreeMsg->userid = $info['userid'];

				switch ($agreeType) {
					case self::AGREE_MSG_TYPE:
						$agreeMsg->agree_type = self::AGREE_TYPE;

						break;
					case self::DISAGREE_MSG_TYPE:
						$agreeMsg->agree_type = self::DISAGREE_TYPE;

						break;
					default:
						break;
				}

				$agreeMsg->agree_time = $info['agree_time'];

				if (!$agreeMsg->validate() || !$agreeMsg->save()) {
					throw new InvalidDataException(SUtils::modelError($agreeMsg));
				}
			}

			return $agreeMsg->id;
		}
	}
