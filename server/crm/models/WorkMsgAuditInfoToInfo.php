<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%work_msg_audit_info_to_info}}".
	 *
	 * @property int                 $id
	 * @property int                 $audit_info_id 会话内容ID
	 * @property int                 $to_type       接收者身份：1、企业成员；2、外部联系人；3、群机器人
	 * @property int                 $user_id       成员ID
	 * @property int                 $external_id   外部联系人ID
	 * @property string              $to            消息接收id。同一企业内容为userid，非相同企业为external_userid
	 *
	 * @property WorkExternalContact $external
	 * @property WorkMsgAuditInfo    $auditInfo
	 * @property WorkUser            $user
	 */
	class WorkMsgAuditInfoToInfo extends \yii\db\ActiveRecord
	{
		const IS_WORK_USER = 1;
		const IS_EXTERNAL_USER = 2;
		const IS_ROBOT_USER = 3;

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_msg_audit_info_to_info}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['audit_info_id', 'to_type', 'user_id', 'external_id'], 'integer'],
				[['to'], 'required'],
				[['to'], 'string', 'max' => 64],
				[['external_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkExternalContact::className(), 'targetAttribute' => ['external_id' => 'id']],
				[['audit_info_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAuditInfo::className(), 'targetAttribute' => ['audit_info_id' => 'id']],
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
				'to_type'       => Yii::t('app', '接收者身份：1、企业成员；2、外部联系人；3、群机器人'),
				'user_id'       => Yii::t('app', '成员ID'),
				'external_id'   => Yii::t('app', '外部联系人ID'),
				'to'            => Yii::t('app', '消息接收id。同一企业内容为userid，非相同企业为external_userid'),
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
		public function getAuditInfo ()
		{
			return $this->hasOne(WorkMsgAuditInfo::className(), ['id' => 'audit_info_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getUser ()
		{
			return $this->hasOne(WorkUser::className(), ['id' => 'user_id']);
		}

		/**
		 * @param $corpId
		 * @param $infoId
		 * @param $toList
		 *
		 * @return array
		 *
		 * @throws InvalidDataException
		 * @throws \Throwable
		 */
		public static function create ($corpId, $infoId, $toList)
		{
			if (empty($toList)) {
				throw new InvalidDataException('参数不正确');
			}

			$result = [
				'count'   => count($toList),
				'success' => 0,
				'failed'  => 0,
			];

			foreach ($toList as $toUser) {
				try {
					$toInfo = self::findOne(['audit_info_id' => $infoId, 'to' => $toUser]);
					if (empty($toInfo)) {
						$toInfo                = new self();
						$toInfo->audit_info_id = $infoId;

						switch (SUtils::getUserType($toUser)) {
							case SUtils::IS_WORK_USER:
								$toInfo->to_type = self::IS_WORK_USER;

								$workUserId = WorkUser::getUserId($corpId, $toUser);
								if (!empty($workUserId)) {
									$toInfo->user_id = $workUserId;
								}
								break;
							case SUtils::IS_EXTERNAL_USER:
								$toInfo->to_type = self::IS_EXTERNAL_USER;

								$externalId = WorkExternalContact::getExternalId($corpId, $toUser);
								if (!empty($externalId)) {
									$toInfo->external_id = $externalId;
								}
								break;
							case SUtils::IS_ROBOT_USER:
								$toInfo->to_type = self::IS_ROBOT_USER;

								break;
							default:

								break;
						}

						$toInfo->to = $toUser;
						if (!$toInfo->validate() || !$toInfo->save()) {
							throw new InvalidDataException(SUtils::modelError($toInfo));
						}
					}

					$result['success']++;
				} catch (\Exception $e) {
					$result['failed']++;

					Yii::error($e->getMessage(), __CLASS__ . "-" . __FUNCTION__);
				}
			}

			return $result;
		}
	}
