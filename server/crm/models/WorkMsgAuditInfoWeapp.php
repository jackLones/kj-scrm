<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%work_msg_audit_info_weapp}}".
	 *
	 * @property int                              $id
	 * @property int                              $audit_info_id 会话内容ID
	 * @property string                           $title         消息标题
	 * @property string                           $description   消息描述
	 * @property string                           $username      用户名称
	 * @property string                           $displayname   小程序名称
	 *
	 * @property WorkMsgAuditInfoChatrecordItem[] $workMsgAuditInfoChatrecordItems
	 * @property WorkMsgAuditInfoMixed[]          $workMsgAuditInfoMixeds
	 * @property WorkMsgAuditInfo                 $auditInfo
	 */
	class WorkMsgAuditInfoWeapp extends \yii\db\ActiveRecord
	{
		const MSG_TYPE = 'weapp';

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_msg_audit_info_weapp}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['audit_info_id'], 'integer'],
				[['title', 'username', 'displayname'], 'string', 'max' => 64],
				[['description'], 'string', 'max' => 255],
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
				'title'         => Yii::t('app', '消息标题'),
				'description'   => Yii::t('app', '消息描述'),
				'username'      => Yii::t('app', '用户名称'),
				'displayname'   => Yii::t('app', '小程序名称'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoChatrecordItems ()
		{
			return $this->hasMany(WorkMsgAuditInfoChatrecordItem::className(), ['weapp_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoMixeds ()
		{
			return $this->hasMany(WorkMsgAuditInfoMixed::className(), ['weapp_id' => 'id']);
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
				'title'       => $this->title,
				'description' => $this->description,
				'username'    => $this->username,
				'displayname' => $this->displayname,
			];
		}

		/**
		 * @param      $infoId
		 * @param      $info
		 * @param bool $needCreate
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 */
		public static function create ($infoId, $info, $needCreate = false)
		{
			$weappInfo = self::findOne(['audit_info_id' => $infoId]);

			if (empty($weappInfo) || $needCreate) {
				$weappInfo                = new self();
				$weappInfo->audit_info_id = $infoId;
				$title                    = $info['title'];
				$description              = $info['description'];
				if (mb_strlen($title, 'utf-8') > 64) {
					$title = mb_substr($title, 0, 64, 'utf-8');
				}
				if (mb_strlen($description, 'utf-8') > 255) {
					$description = mb_substr($description, 0, 255, 'utf-8');
				}
				$weappInfo->title       = $title;
				$weappInfo->description = $description;
				$weappInfo->username    = $info['username'];
				$weappInfo->displayname = $info['displayname'];

				if (!$weappInfo->validate() || !$weappInfo->save()) {
					throw new InvalidDataException(SUtils::modelError($weappInfo));
				}

				//发送提醒
				$auditInfo = WorkMsgAuditInfo::findOne($infoId);
				if (!empty($auditInfo->chat_id)) {
					WorkChatRemindSend::creat($auditInfo, static::MSG_TYPE);
				}
			}

			return $weappInfo->id;
		}
	}
