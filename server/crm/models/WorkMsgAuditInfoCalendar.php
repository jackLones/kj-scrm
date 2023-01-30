<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%work_msg_audit_info_calendar}}".
	 *
	 * @property int                                $id
	 * @property int                                $audit_info_id 会话内容ID
	 * @property string                             $title         日程主题
	 * @property int                                $user_id       成员ID
	 * @property int                                $external_id   外部联系人ID
	 * @property string                             $creatorname   日程组织者
	 * @property string                             $starttime     日程开始时间 单位秒
	 * @property string                             $endtime       日程结束时间 单位秒
	 * @property string                             $place         日程地点
	 * @property string                             $remarks       日程备注
	 *
	 * @property WorkExternalContact                $external
	 * @property WorkMsgAuditInfo                   $auditInfo
	 * @property WorkUser                           $user
	 * @property WorkMsgAuditInfoCalendarAttendee[] $workMsgAuditInfoCalendarAttendees
	 * @property WorkMsgAuditInfoMixed[]            $workMsgAuditInfoMixeds
	 */
	class WorkMsgAuditInfoCalendar extends \yii\db\ActiveRecord
	{
		const MSG_TYPE = "calendar";

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_msg_audit_info_calendar}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['audit_info_id', 'user_id', 'external_id'], 'integer'],
				[['remarks'], 'string'],
				[['title', 'creatorname'], 'string', 'max' => 64],
				[['place'], 'string', 'max' => 255],
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
				'title'         => Yii::t('app', '日程主题'),
				'user_id'       => Yii::t('app', '成员ID'),
				'external_id'   => Yii::t('app', '外部联系人ID'),
				'creatorname'   => Yii::t('app', '日程组织者'),
				'starttime'     => Yii::t('app', '日程开始时间 单位秒'),
				'endtime'       => Yii::t('app', '日程结束时间 单位秒'),
				'place'         => Yii::t('app', '日程地点'),
				'remarks'       => Yii::t('app', '日程备注'),
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
		public function getWorkMsgAuditInfoCalendarAttendees ()
		{
			return $this->hasMany(WorkMsgAuditInfoCalendarAttendee::className(), ['calendar_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoMixeds ()
		{
			return $this->hasMany(WorkMsgAuditInfoMixed::className(), ['calendar_id' => 'id']);
		}

		public function dumpData ()
		{
			//拼凑日期格式
			$dateStr = WorkMsgAuditInfo::spellDate($this->starttime, $this->endtime);

			return [
				'title'       => $this->title,
				'user_id'     => $this->user_id,
				'external_id' => $this->external_id,
				'creatorname' => $this->creatorname,
				'starttime'   => $this->starttime,
				'endtime'     => $this->endtime,
				'dateStr'     => $dateStr,
				'place'       => $this->place,
				'remarks'     => $this->remarks,
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
			$calendarInfo = self::findOne(['audit_info_id' => $infoId]);

			if (empty($calendarInfo) || $needCreate) {
				$calendarInfo                = new self();
				$calendarInfo->audit_info_id = $infoId;
				$calendarInfo->title         = $info['title'];

				switch (SUtils::getUserType($info['creatorname'])) {
					case SUtils::IS_WORK_USER:
						$workUserId = WorkUser::getUserId($corpId, $info['creatorname']);
						if (!empty($workUserId)) {
							$calendarInfo->user_id = $workUserId;
						}

						break;
					case SUtils::IS_EXTERNAL_USER:
						$externalId = WorkExternalContact::getExternalId($corpId, $info['creatorname']);
						if (!empty($externalId)) {
							$calendarInfo->external_id = $externalId;
						}

						break;
					default:

						break;
				}

				$calendarInfo->creatorname = $info['creatorname'];
				$calendarInfo->starttime   = $info['starttime'];
				$calendarInfo->endtime     = $info['endtime'];
				$calendarInfo->place       = $info['place'];
				$calendarInfo->remarks     = $info['remarks'];

				if (!$calendarInfo->validate() || !$calendarInfo->save()) {
					throw new InvalidDataException(SUtils::modelError($calendarInfo));
				}

				foreach ($info['attendeename'] as $attendee) {
					try {
						WorkMsgAuditInfoCalendarAttendee::create($corpId, $calendarInfo->id, $attendee);
					} catch (\Exception $e) {
						Yii::error($e->getMessage(), __CLASS__ . '-' . __FUNCTION__ . ':attendeeCreate');
					}
				}
			}

			return $calendarInfo->id;
		}
	}
