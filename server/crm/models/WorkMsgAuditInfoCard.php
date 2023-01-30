<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%work_msg_audit_info_card}}".
	 *
	 * @property int                 $id
	 * @property int                 $audit_info_id 会话内容ID
	 * @property string              $corpname      名片所有者所在的公司名称
	 * @property int                 $user_id       成员ID
	 * @property int                 $external_id   外部联系人ID
	 * @property string              $userid        名片所有者的id，同一公司是userid，不同公司是external_userid
	 *
	 * @property WorkMsgAuditInfo    $auditInfo
	 * @property WorkExternalContact $external
	 * @property WorkUser            $user
	 */
	class WorkMsgAuditInfoCard extends \yii\db\ActiveRecord
	{
		const MSG_TYPE = 'card';

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_msg_audit_info_card}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['audit_info_id', 'user_id', 'external_id'], 'integer'],
				[['corpname', 'userid'], 'string', 'max' => 64],
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
				'corpname'      => Yii::t('app', '名片所有者所在的公司名称'),
				'user_id'       => Yii::t('app', '成员ID'),
				'external_id'   => Yii::t('app', '外部联系人ID'),
				'userid'        => Yii::t('app', '名片所有者的id，同一公司是userid，不同公司是external_userid'),
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
			$data = [
				'corpname'  => $this->corpname,
				'userid'    => $this->userid,
				'user_info' => ''
			];

			if (!empty($this->user)) {
				$data['user_info'] = $this->user->dumpMiniData();
			}

			if (!empty($this->external)) {
				$data['user_info'] = $this->external->dumpMiniData();
			}

			return $data;
		}

		/**
		 * @param $corpId
		 * @param $infoId
		 * @param $info
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 * @throws \Throwable
		 */
		public static function create ($corpId, $infoId, $info)
		{
			$cardInfo = self::findOne(['audit_info_id' => $infoId]);

			if (empty($cardInfo)) {
				$cardInfo                = new self();
				$cardInfo->audit_info_id = $infoId;
				$cardInfo->corpname      = $info['corpname'];

				switch (SUtils::getUserType($info['userid'])) {
					case SUtils::IS_WORK_USER:
						$workUserId = WorkUser::getUserId($corpId, $info['userid']);
						if (!empty($workUserId)) {
							$cardInfo->user_id = $workUserId;
						}

						break;
					case SUtils::IS_EXTERNAL_USER:
						$externalId = WorkExternalContact::getExternalId($corpId, $info['userid']);
						if (!empty($externalId)) {
							$cardInfo->external_id = $externalId;
						}

						break;
					default:

						break;
				}

				$cardInfo->userid = $info['userid'];

				if (!$cardInfo->validate() || !$cardInfo->save()) {
					throw new InvalidDataException(SUtils::modelError($cardInfo));
				}

				//发送提醒
				$auditInfo = WorkMsgAuditInfo::findOne($infoId);
				if (!empty($auditInfo->chat_id)) {
					WorkChatRemindSend::creat($auditInfo, static::MSG_TYPE);
				}
			}

			return $cardInfo->id;
		}
	}
