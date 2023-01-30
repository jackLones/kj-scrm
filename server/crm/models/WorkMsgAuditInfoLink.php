<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%work_msg_audit_info_link}}".
	 *
	 * @property int                              $id
	 * @property int                              $audit_info_id 会话内容ID
	 * @property string                           $title         消息标题
	 * @property string                           $description   消息描述
	 * @property string                           $link_url      链接url地址
	 * @property string                           $image_url     链接图片url
	 *
	 * @property WorkMsgAuditInfoChatrecordItem[] $workMsgAuditInfoChatrecordItems
	 * @property WorkMsgAuditInfo                 $auditInfo
	 * @property WorkMsgAuditInfoMixed[]          $workMsgAuditInfoMixeds
	 */
	class WorkMsgAuditInfoLink extends \yii\db\ActiveRecord
	{
		const MSG_TYPE = 'link';

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_msg_audit_info_link}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['audit_info_id'], 'integer'],
				[['link_url', 'image_url'], 'string'],
				[['title'], 'string', 'max' => 64],
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
				'link_url'      => Yii::t('app', '链接url地址'),
				'image_url'     => Yii::t('app', '链接图片url'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoChatrecordItems ()
		{
			return $this->hasMany(WorkMsgAuditInfoChatrecordItem::className(), ['link_id' => 'id']);
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
		public function getWorkMsgAuditInfoMixeds ()
		{
			return $this->hasMany(WorkMsgAuditInfoMixed::className(), ['link_id' => 'id']);
		}

		public function dumpData ()
		{
			return [
				'title'       => $this->title,
				'description' => $this->description,
				'link_url'    => $this->link_url,
				'image_url'   => $this->image_url,
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
			$linkInfo = self::findOne(['audit_info_id' => $infoId]);

			if (empty($linkInfo) || $needCreate) {
				$linkInfo                = new self();
				$linkInfo->audit_info_id = $infoId;
				$linkInfo->title         = $info['title'];
				$linkInfo->description   = $info['description'];
				$linkInfo->link_url      = $info['link_url'];
				$linkInfo->image_url     = $info['image_url'];

				if (!$linkInfo->validate() || !$linkInfo->save()) {
					throw new InvalidDataException(SUtils::modelError($linkInfo));
				}

				//发送提醒
				$auditInfo = WorkMsgAuditInfo::findOne($infoId);
				if (!empty($auditInfo->chat_id)) {
					WorkChatRemindSend::creat($auditInfo, static::MSG_TYPE);
				}
			}

			return $linkInfo->id;
		}
	}
