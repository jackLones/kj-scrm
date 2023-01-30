<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\SUtils;
	use app\util\WorkUtils;
	use dovechen\yii2\weWork\src\dataStructure\MsgAuditCheckAgree;
	use Yii;

	/**
	 * This is the model class for table "{{%work_msg_audit_agree}}".
	 *
	 * @property int                 $id
	 * @property int                 $audit_id           会话存档ID
	 * @property int                 $user_id            成员ID
	 * @property string              $userid             内部成员的userid
	 * @property int                 $external_id        外部联系人ID
	 * @property string              $exteranalopenid    外部成员的externalopenid
	 * @property int                 $chat_id            企业群ID
	 * @property string              $roomid             企业外部群ID
	 * @property string              $agree_status       同意："Agree"，不同意："Disagree"，默认同意："Default_Agree"
	 * @property string              $status_change_time 同意状态改变的具体时间，utc时间
	 *
	 * @property WorkChat            $chat
	 * @property WorkMsgAudit        $audit
	 * @property WorkExternalContact $external
	 * @property WorkUser            $user
	 */
	class WorkMsgAuditAgree extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_msg_audit_agree}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['audit_id', 'user_id', 'external_id', 'chat_id'], 'integer'],
				[['userid', 'exteranalopenid', 'roomid'], 'string', 'max' => 64],
				[['agree_status'], 'string', 'max' => 16],
				[['chat_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkChat::className(), 'targetAttribute' => ['chat_id' => 'id']],
				[['audit_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAudit::className(), 'targetAttribute' => ['audit_id' => 'id']],
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
				'id'                 => Yii::t('app', 'ID'),
				'audit_id'           => Yii::t('app', '会话存档ID'),
				'user_id'            => Yii::t('app', '成员ID'),
				'userid'             => Yii::t('app', '内部成员的userid'),
				'external_id'        => Yii::t('app', '外部联系人ID'),
				'exteranalopenid'    => Yii::t('app', '外部成员的externalopenid'),
				'chat_id'            => Yii::t('app', '企业群ID'),
				'roomid'             => Yii::t('app', '企业外部群ID'),
				'agree_status'       => Yii::t('app', '同意：\"Agree\"，不同意：\"Disagree\"，默认同意：\"Default_Agree\"'),
				'status_change_time' => Yii::t('app', '同意状态改变的具体时间，utc时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getChat ()
		{
			return $this->hasOne(WorkChat::className(), ['id' => 'chat_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getAudit ()
		{
			return $this->hasOne(WorkMsgAudit::className(), ['id' => 'audit_id']);
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

		/**
		 * @param bool $withUser
		 * @param bool $withExternal
		 * @param bool $withRoom
		 *
		 * @return array
		 */
		public function dumpData ($withUser = false, $withExternal = false, $withRoom = false)
		{
			$result = [
				'id'                 => $this->id,
				'userid'             => $this->userid,
				'exteranalopenid'    => $this->exteranalopenid,
				'roomid'             => $this->roomid,
				'agree_status'       => $this->agree_status,
				'status_change_time' => $this->status_change_time,
				'user'               => [],
				'external'           => [],
				'room'               => [],
			];

			if ($withUser && !empty($this->user)) {
				$result['user'] = $this->user->dumpData();
			}

			if ($withExternal && !empty($this->external)) {
				$result['external'] = $this->external->dumpData();
			}

			if ($withRoom && !empty($this->chat)) {
				$result['room'] = $this->chat->dumpData();
			}

			return $result;
		}

		/**
		 * @param $corpId
		 * @param $info
		 *
		 * @return array|int[]
		 *
		 * @throws InvalidDataException
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \Throwable
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function checkSingle ($corpId, $info)
		{
			$result   = [];
			$workCorp = WorkCorp::findOne($corpId);

			if (empty($workCorp)) {
				throw new InvalidDataException('参数不正确');
			}

			$msgAuditApi = WorkUtils::getMsgAuditApi($corpId);
			if (!empty($msgAuditApi)) {
				$data      = ['info' => $info];
				$args      = MsgAuditCheckAgree::parseFromArray($data);
				$agreeInfo = $msgAuditApi->CheckSingleAgree($args);

				if (!empty($agreeInfo['agreeinfo'])) {
					$result = self::setAgree($workCorp->workMsgAudit->id, $agreeInfo['agreeinfo']);
				}
			}

			return $result;
		}

		/**
		 * @param $msgAuditId
		 * @param $agreeInfo
		 *
		 * @return array
		 *
		 * @throws \Throwable
		 */
		public static function setAgree ($msgAuditId, $agreeInfo)
		{
			$result = [
				'count'   => 0,
				'success' => 0,
				'failed'  => 0,
				'info'    => [],
			];

			$workMsgAudit = WorkMsgAudit::findOne($msgAuditId);
			if (!empty($workMsgAudit) && !empty($agreeInfo)) {
				$result['count'] = count($agreeInfo);

				foreach ($agreeInfo as $info) {
					try {
						$workUserId        = WorkUser::getUserId($workMsgAudit->corp_id, $info['userid']);
						$externalContactId = WorkExternalContact::getExternalId($workMsgAudit->corp_id, $info['exteranalopenid']);
						$agreeInfo         = self::findOne(['audit_id' => $workMsgAudit->id, 'userid' => $info['userid'], 'exteranalopenid' => $info['exteranalopenid'], 'agree_status' => $info['agree_status'], 'status_change_time' => $info['status_change_time']]);

						if (empty($agreeInfo) && !empty($workUserId) && !empty($externalContactId)) {
							$agreeInfo = self::findOne(['audit_id' => $workMsgAudit->id, 'user_id' => $workUserId, 'external_id' => $externalContactId, 'agree_status' => $info['agree_status'], 'status_change_time' => $info['status_change_time']]);
						} elseif (empty($agreeInfo) && !empty($workUserId)) {
							$agreeInfo = self::findOne(['audit_id' => $workMsgAudit->id, 'user_id' => $workUserId, 'exteranalopenid' => $info['exteranalopenid'], 'agree_status' => $info['agree_status'], 'status_change_time' => $info['status_change_time']]);
						} elseif (empty($agreeInfo) && !empty($externalContactId)) {
							$agreeInfo = self::findOne(['audit_id' => $workMsgAudit->id, 'userid' => $info['userid'], 'external_id' => $externalContactId, 'agree_status' => $info['agree_status'], 'status_change_time' => $info['status_change_time']]);
						}

						if (empty($agreeInfo)) {
							$agreeInfo                     = new self();
							$agreeInfo->audit_id           = $workMsgAudit->id;
							$agreeInfo->user_id            = !empty($workUserId) ? $workUserId : NULL;
							$agreeInfo->userid             = $info['userid'];
							$agreeInfo->external_id        = !empty($externalContactId) ? $externalContactId : NULL;
							$agreeInfo->exteranalopenid    = $info['exteranalopenid'];
							$agreeInfo->agree_status       = $info['agree_status'];
							$agreeInfo->status_change_time = $info['status_change_time'];

							if (!$agreeInfo->validate() || !$agreeInfo->save()) {
								throw new InvalidDataException(SUtils::modelError($agreeInfo));
							}
						}

						$result['success']++;
						array_push($result['info'], $agreeInfo->dumpData(true, true));
					} catch (\Exception $e) {
						$result['failed']++;

						Yii::error($e->getMessage(), __CLASS__ . "-" . __FUNCTION__ . ":setAgree");
					}
				}
			}

			return $result;
		}

		/**
		 * @param $corpId
		 * @param $chatId
		 *
		 * @return array|int[]
		 *
		 * @throws InvalidDataException
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \Throwable
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function checkRoom ($corpId, $chatId)
		{
			$result   = [];
			$workCorp = WorkCorp::findOne($corpId);

			if (empty($workCorp)) {
				throw new InvalidDataException('参数不正确');
			}

			$msgAuditApi = WorkUtils::getMsgAuditApi($corpId);
			if (!empty($msgAuditApi)) {
				$data      = ['roomid' => $chatId];
				$args      = MsgAuditCheckAgree::parseFromArray($data);
				$agreeInfo = $msgAuditApi->CheckRoomAgree($args);

				if (!empty($agreeInfo['agreeinfo'])) {
					$result = self::setRoomAgree($workCorp->workMsgAudit->id, $chatId, $agreeInfo['agreeinfo']);
				}
			}

			return $result;
		}

		/**
		 * @param $msgAuditId
		 * @param $chatId
		 * @param $agreeInfo
		 *
		 * @return array
		 *
		 * @throws \Throwable
		 */
		public static function setRoomAgree ($msgAuditId, $chatId, $agreeInfo)
		{
			$result = [
				'count'   => 0,
				'success' => 0,
				'failed'  => 0,
				'info'    => [],
			];

			$workMsgAudit = WorkMsgAudit::findOne($msgAuditId);
			if (!empty($workMsgAudit) && !empty($agreeInfo)) {
				$result['count'] = count($agreeInfo);

				$workChatId = WorkChat::getChatId($workMsgAudit->corp_id, $chatId);

				foreach ($agreeInfo as $info) {
					try {
						$externalContactId = WorkExternalContact::getExternalId($workMsgAudit->corp_id, $info['exteranalopenid']);
						$agreeInfo         = self::findOne(['audit_id' => $workMsgAudit->id, 'roomid' => $chatId, 'exteranalopenid' => $info['exteranalopenid'], 'agree_status' => $info['agree_status'], 'status_change_time' => $info['status_change_time']]);

						if (empty($agreeInfo) && !empty($workChatId) && !empty($externalContactId)) {
							$agreeInfo = self::findOne(['audit_id' => $workMsgAudit->id, 'chat_id' => $workChatId, 'external_id' => $externalContactId, 'agree_status' => $info['agree_status'], 'status_change_time' => $info['status_change_time']]);
						} elseif (empty($agreeInfo) && !empty($workChatId)) {
							$agreeInfo = self::findOne(['audit_id' => $workMsgAudit->id, 'chat_id' => $workChatId, 'exteranalopenid' => $info['exteranalopenid'], 'agree_status' => $info['agree_status'], 'status_change_time' => $info['status_change_time']]);
						} elseif (empty($agreeInfo) && !empty($externalContactId)) {
							$agreeInfo = self::findOne(['audit_id' => $workMsgAudit->id, 'roomid' => $chatId, 'external_id' => $externalContactId, 'agree_status' => $info['agree_status'], 'status_change_time' => $info['status_change_time']]);
						}

						if (empty($agreeInfo)) {
							$agreeInfo                     = new self();
							$agreeInfo->audit_id           = $workMsgAudit->id;
							$agreeInfo->external_id        = !empty($externalContactId) ? $externalContactId : NULL;
							$agreeInfo->exteranalopenid    = $info['exteranalopenid'];
							$agreeInfo->chat_id            = !empty($workChatId) ? $workChatId : NULL;
							$agreeInfo->roomid             = $chatId;
							$agreeInfo->agree_status       = $info['agree_status'];
							$agreeInfo->status_change_time = $info['status_change_time'];

							if (!$agreeInfo->validate() || !$agreeInfo->save()) {
								throw new InvalidDataException(SUtils::modelError($agreeInfo));
							}
						}

						$result['success']++;
						array_push($result['info'], $agreeInfo->dumpData(true, true, true));
					} catch (\Exception $e) {
						$result['failed']++;

						Yii::error($e->getMessage(), __CLASS__ . "-" . __FUNCTION__ . ":setRoomAgree");
					}
				}
			}

			return $result;
		}
	}
