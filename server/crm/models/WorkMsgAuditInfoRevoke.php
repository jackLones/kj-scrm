<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%work_msg_audit_info_revoke}}".
	 *
	 * @property int              $id
	 * @property int              $audit_info_id 会话内容ID
	 * @property int              $pre_info_id   会话内容ID
	 * @property string           $pre_msgid     标识撤回的原消息的msgid
	 *
	 * @property WorkMsgAuditInfo $preInfo
	 * @property WorkMsgAuditInfo $auditInfo
	 */
	class WorkMsgAuditInfoRevoke extends \yii\db\ActiveRecord
	{
		const MSG_TYPE = 'revoke';

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_msg_audit_info_revoke}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['audit_info_id', 'pre_info_id'], 'integer'],
				[['pre_msgid'], 'string', 'max' => 64],
				[['pre_info_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAuditInfo::className(), 'targetAttribute' => ['pre_info_id' => 'id']],
				[['audit_info_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAuditInfo::className(), 'targetAttribute' => ['audit_info_id' => 'id']],
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
				'pre_info_id'   => Yii::t('app', '会话内容ID'),
				'pre_msgid'     => Yii::t('app', '标识撤回的原消息的msgid'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getPreInfo ()
		{
			return $this->hasOne(WorkMsgAuditInfo::className(), ['id' => 'pre_info_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getAuditInfo ()
		{
			return $this->hasOne(WorkMsgAuditInfo::className(), ['id' => 'audit_info_id']);
		}

		public function dumpData ()
		{
			return [
				'pre_info_id' => $this->pre_info_id,
				'content'     => $this->preInfo->dumpData(false, false, true)
			];
		}

		/**
		 * @param $infoId
		 * @param $info
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 */
		public static function create ($infoId, $info)
		{
			$revokeMsg = self::findOne(['audit_info_id' => $infoId]);

			if (empty($revokeMsg)) {
				$revokeMsg                = new self();
				$revokeMsg->audit_info_id = $infoId;

				$preMsgInfo             = WorkMsgAuditInfo::findOne(['msgid' => $info['pre_msgid']]);
				$revokeMsg->pre_info_id = $preMsgInfo->id;
				$revokeMsg->pre_msgid   = $info['pre_msgid'];

				if (!$revokeMsg->validate() || !$revokeMsg->save()) {
					throw new InvalidDataException(SUtils::modelError($revokeMsg));
				}
			}

			return $revokeMsg->id;
		}
	}
