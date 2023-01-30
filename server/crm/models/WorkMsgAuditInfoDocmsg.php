<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%work_msg_audit_info_docmsg}}".
	 *
	 * @property int                              $id
	 * @property int                              $audit_info_id 会话内容ID
	 * @property string                           $title         在线文档名称
	 * @property string                           $link_url      在线文档链接
	 * @property int                              $user_id       成员ID
	 * @property int                              $external_id   外部联系人ID
	 * @property string                           $doc_creator   在线文档创建者。本企业成员创建为userid；外部企业成员创建为external_userid
	 *
	 * @property WorkExternalContact              $external
	 * @property WorkMsgAuditInfo                 $auditInfo
	 * @property WorkUser                         $user
	 * @property WorkMsgAuditInfoMixed[]          $workMsgAuditInfoMixeds
	 * @property WorkMsgAuditInfoChatrecordItem[] $workMsgAuditInfoChatrecordItems
	 */
	class WorkMsgAuditInfoDocmsg extends \yii\db\ActiveRecord
	{
		const MSG_TYPE = "docmsg";

		const IS_WORK_USER = 1;
		const IS_EXTERNAL_USER = 2;
		const IS_ROBOT_USER = 3;

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_msg_audit_info_docmsg}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['audit_info_id', 'user_id', 'external_id'], 'integer'],
				[['link_url'], 'string'],
				[['title', 'doc_creator'], 'string', 'max' => 64],
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
				'title'         => Yii::t('app', '在线文档名称'),
				'link_url'      => Yii::t('app', '在线文档链接'),
				'user_id'       => Yii::t('app', '成员ID'),
				'external_id'   => Yii::t('app', '外部联系人ID'),
				'doc_creator'   => Yii::t('app', '在线文档创建者。本企业成员创建为userid；外部企业成员创建为external_userid'),
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
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoMixeds ()
		{
			return $this->hasMany(WorkMsgAuditInfoMixed::className(), ['docmsg_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoChatrecordItems ()
		{
			return $this->hasMany(WorkMsgAuditInfoChatrecordItem::className(), ['docmsg_id' => 'id']);
		}

		public function dumpData ()
		{
			return [
				'title'       => $this->title,
				'link_url'    => $this->link_url,
				'user_id'     => $this->user_id,
				'external_id' => $this->external_id,
				'doc_creator' => $this->doc_creator,
			];
		}

		/**
		 * @param      $corpId
		 * @param      $infoId
		 * @param      $info
		 * @param bool $needCreate
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 * @throws \Throwable
		 */
		public static function create ($corpId, $infoId, $info, $needCreate = false)
		{
			$docMsgInfo = self::findOne(['audit_info_id' => $infoId]);

			if (empty($docMsgInfo) || $needCreate) {
				$docMsgInfo                = new self();
				$docMsgInfo->audit_info_id = $infoId;
				$docMsgInfo->title         = $info['title'];
				$docMsgInfo->link_url      = $info['link_url'];

				switch (SUtils::getUserType($info['doc_creator'])) {
					case self::IS_WORK_USER:
						$workUserId = WorkUser::getUserId($corpId, $info['doc_creator']);
						if (!empty($workUserId)) {
							$docMsgInfo->user_id = $workUserId;
						}

						break;
					case self::IS_EXTERNAL_USER:
						$externalId = WorkExternalContact::getExternalId($corpId, $info['doc_creator']);
						if (!empty($externalId)) {
							$docMsgInfo->external_id = $externalId;
						}

						break;
					default:

						break;
				}

				$docMsgInfo->doc_creator = $info['doc_creator'];

				if (!$docMsgInfo->validate() || !$docMsgInfo->save()) {
					throw new InvalidDataException(SUtils::modelError($docMsgInfo));
				}
			}

			return $docMsgInfo->id;
		}
	}
