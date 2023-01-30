<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%work_msg_audit_info_chatrecord}}".
	 *
	 * @property int                              $id
	 * @property int                              $audit_info_id 会话内容ID
	 * @property string                           $title         聊天记录标题
	 *
	 * @property WorkMsgAuditInfo                 $auditInfo
	 * @property WorkMsgAuditInfoChatrecordItem[] $workMsgAuditInfoChatrecordItems
	 * @property WorkMsgAuditInfoChatrecordItem[] $workMsgAuditInfoChatrecordItemsRecord
	 * @property WorkMsgAuditInfoMixed[]          $workMsgAuditInfoMixeds
	 */
	class WorkMsgAuditInfoChatrecord extends \yii\db\ActiveRecord
	{
		const MSG_TYPE = 'chatrecord';

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_msg_audit_info_chatrecord}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['audit_info_id'], 'integer'],
				[['title'], 'string', 'max' => 64],
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
				'title'         => Yii::t('app', '聊天记录标题'),
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
		public function getWorkMsgAuditInfoChatrecordItems ()
		{
			return $this->hasMany(WorkMsgAuditInfoChatrecordItem::className(), ['record_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoChatrecordItemsRecord ()
		{
			return $this->hasMany(WorkMsgAuditInfoChatrecordItem::className(), ['chatrecord_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoMixeds ()
		{
			return $this->hasMany(WorkMsgAuditInfoMixed::className(), ['chatrecord_id' => 'id']);
		}

		public function dumpData ()
		{
			$data = [
				'title' => $this->title,
				'item'  => []
			];

			$itemData = [];
			if (!empty($this->workMsgAuditInfoChatrecordItems)) {
				foreach ($this->workMsgAuditInfoChatrecordItems as $workMsgAuditInfoChatrecordItem) {
					array_push($itemData, $workMsgAuditInfoChatrecordItem->dumpData());
				}

				if (!empty($itemData)) {
					$itemData = array_column($itemData, NULL, 'sort');
					ksort($itemData);

					$data['item'] = $itemData;
				}
			}

			return $data;
		}

		/**
		 * @param                  $corpId
		 * @param WorkMsgAuditInfo $auditInfo
		 * @param                  $info
		 * @param boolean          $needCreate
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 * @throws \Throwable
		 */
		public static function create ($corpId, $auditInfo, $info, $needCreate = false)
		{
			$chatRecordInfo = self::findOne(['audit_info_id' => $auditInfo->id]);

			if (empty($chatRecordInfo) || $needCreate) {
				$chatRecordInfo                = new self();
				$chatRecordInfo->audit_info_id = $auditInfo->id;
				$chatRecordInfo->title         = $info['title'];

				if (!$chatRecordInfo->validate() || !$chatRecordInfo->save()) {
					throw new InvalidDataException(SUtils::modelError($chatRecordInfo));
				}

				WorkMsgAuditInfoChatrecordItem::create($corpId, $chatRecordInfo->id, $auditInfo, $info);
			}

			return $chatRecordInfo->id;
		}
	}
