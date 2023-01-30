<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%work_msg_audit_info_meeting}}".
	 *
	 * @property int                              $id
	 * @property int                              $audit_info_id 会话内容ID
	 * @property string                           $topic         会议主题
	 * @property string                           $starttime     会议开始时间
	 * @property string                           $endtime       会议结束时间
	 * @property string                           $address       会议地址
	 * @property string                           $remarks       会议备注
	 * @property int                              $meetingtype   会议消息类型。101发起会议邀请消息、102处理会议邀请消息
	 * @property string                           $meetingid     会议ID
	 * @property int                              $status        会议邀请处理状态。1 参加会议、2 拒绝会议、3 待定
	 *
	 * @property WorkMsgAuditInfoChatrecordItem[] $workMsgAuditInfoChatrecordItems
	 * @property WorkMsgAuditInfo                 $auditInfo
	 * @property WorkMsgAuditInfoMixed[]          $workMsgAuditInfoMixeds
	 */
	class WorkMsgAuditInfoMeeting extends \yii\db\ActiveRecord
	{
		const MSG_TYPE = 'meeting';

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_msg_audit_info_meeting}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['audit_info_id'], 'integer'],
				[['remarks'], 'string'],
				[['topic', 'meetingid'], 'string', 'max' => 64],
				[['address'], 'string', 'max' => 255],
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
				'topic'         => Yii::t('app', '会议主题'),
				'starttime'     => Yii::t('app', '会议开始时间'),
				'endtime'       => Yii::t('app', '会议结束时间'),
				'address'       => Yii::t('app', '会议地址'),
				'remarks'       => Yii::t('app', '会议备注'),
				'meetingtype'   => Yii::t('app', '会议消息类型。101发起会议邀请消息、102处理会议邀请消息'),
				'meetingid'     => Yii::t('app', '会议ID'),
				'status'        => Yii::t('app', '会议邀请处理状态。1 参加会议、2 拒绝会议、3 待定'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoChatrecordItems ()
		{
			return $this->hasMany(WorkMsgAuditInfoChatrecordItem::className(), ['meeting_id' => 'id']);
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
			return $this->hasMany(WorkMsgAuditInfoMixed::className(), ['meeting_id' => 'id']);
		}

		public function dumpData ()
		{
			//拼凑日期格式
			$dateStr = WorkMsgAuditInfo::spellDate($this->starttime, $this->endtime);

			return [
				'topic'       => $this->topic,
				'starttime'   => $this->starttime,
				'endtime'     => $this->endtime,
				'dateStr'     => $dateStr,
				'address'     => $this->address,
				'remarks'     => $this->remarks,
				'meetingtype' => $this->meetingtype,
				'meetingid'   => $this->meetingid,
				'status'      => $this->status,
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
			$meetingInfo = self::findOne(['audit_info_id' => $infoId]);

			if (empty($meetingInfo) || $needCreate) {
				$meetingInfo                = new self();
				$meetingInfo->audit_info_id = $infoId;
				$meetingInfo->topic         = $info['topic'];
				$meetingInfo->starttime     = $info['starttime'];
				$meetingInfo->endtime       = $info['endtime'];
				$meetingInfo->address       = $info['address'];
				$meetingInfo->remarks       = $info['remarks'];
				$meetingInfo->meetingtype   = $info['meetingtype'];
				$meetingInfo->meetingid     = (string)$info['meetingid'];
				$meetingInfo->status        = isset($info['status']) ? $info['status'] : 0;

				if (!$meetingInfo->validate() || !$meetingInfo->save()) {
					throw new InvalidDataException(SUtils::modelError($meetingInfo));
				}
			}

			return $meetingInfo->id;
		}
	}
