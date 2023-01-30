<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%work_msg_audit_info_calendar_attendee}}".
	 *
	 * @property int                      $id
	 * @property int                      $calendar_id  日程ID
	 * @property int                      $user_id      成员ID
	 * @property int                      $external_id  外部联系人ID
	 * @property string                   $attendeename 日程参与人
	 *
	 * @property WorkExternalContact      $external
	 * @property WorkMsgAuditInfoCalendar $calendar
	 * @property WorkUser                 $user
	 */
	class WorkMsgAuditInfoCalendarAttendee extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_msg_audit_info_calendar_attendee}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['calendar_id', 'user_id', 'external_id'], 'integer'],
				[['attendeename'], 'string', 'max' => 64],
				[['external_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkExternalContact::className(), 'targetAttribute' => ['external_id' => 'id']],
				[['calendar_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAuditInfoCalendar::className(), 'targetAttribute' => ['calendar_id' => 'id']],
				[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkUser::className(), 'targetAttribute' => ['user_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'           => Yii::t('app', 'ID'),
				'calendar_id'  => Yii::t('app', '日程ID'),
				'user_id'      => Yii::t('app', '成员ID'),
				'external_id'  => Yii::t('app', '外部联系人ID'),
				'attendeename' => Yii::t('app', '日程参与人'),
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
		public function getCalendar ()
		{
			return $this->hasOne(WorkMsgAuditInfoCalendar::className(), ['id' => 'calendar_id']);
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
		 * @param $calendarId
		 * @param $user
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 * @throws \Throwable
		 */
		public static function create ($corpId, $calendarId, $user)
		{
			$attendeeInfo = self::findOne(['calendar_id' => $calendarId, 'attendeename' => $user]);

			if (empty($attendeeInfo)) {
				$attendeeInfo              = new self();
				$attendeeInfo->calendar_id = $calendarId;

				switch (SUtils::getUserType($user)) {
					case SUtils::IS_WORK_USER:
						$workUserId = WorkUser::getUserId($corpId, $user);
						if (!empty($workUserId)) {
							$attendeeInfo->user_id = $workUserId;
						}

						break;
					case SUtils::IS_EXTERNAL_USER:
						$externalId = WorkExternalContact::getExternalId($corpId, $user);
						if (!empty($externalId)) {
							$attendeeInfo->external_id = $externalId;
						}

						break;
					default:

						break;
				}

				$attendeeInfo->attendeename = $user;

				if (!$attendeeInfo->validate() || !$attendeeInfo->save()) {
					throw new InvalidDataException(SUtils::modelError($attendeeInfo));
				}
			}

			return $attendeeInfo->id;
		}
	}
