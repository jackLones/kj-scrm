<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\queue\MsgAuditNoticeJob;
	use app\util\SUtils;
	use app\util\WebsocketUtil;
	use Yii;

	/**
	 * This is the model class for table "{{%work_msg_audit_info}}".
	 *
	 * @property int                                 $id
	 * @property int                                 $audit_id       会话存档ID
	 * @property string                              $msgid          消息id，消息的唯一标识，企业可以使用此字段进行消息去重
	 * @property string                              $action         消息动作，send发送消息；recall撤回消息；switch切换企业日志三种类型
	 * @property int                                 $from_type      发送者身份：1、企业成员；2、外部联系人；3、群机器人
	 * @property int                                 $to_type        接收者身份：1、企业成员；2、外部联系人；3、群机器人
	 * @property int                                 $user_id        成员ID
	 * @property int                                 $external_id    外部联系人ID
	 * @property int                                 $to_user_id     接收者成员ID
	 * @property int                                 $to_external_id 接收者外部联系人ID
	 * @property string                              $from           消息发送方id。同一企业内容为userid，非相同企业为external_userid。消息如果是机器人发出，也为external_userid
	 * @property string                              $tolist         消息接收方列表，可能是多个，同一个企业内容为userid，非相同企业为external_userid
	 * @property int                                 $chat_id        外部群ID
	 * @property string                              $roomid         群聊消息的群id。如果是单聊则为空
	 * @property string                              $content        缩略消息
	 * @property string                              $msgtype        消息类型：文本：text； 图片：image；撤回：revoke；同意：agree；不同意：disagree；语音：voice；视频：video；名片：card；位置：location；表情：emotion；文件：file；链接：link；小程序：weapp；会话记录：chatrecord；待办：todo；投票：vote；填表：collect；红包：redpacket；会议邀请：meeting；在线文档：docmsg；MarkDown：markdown；图文：news；日程：calendar；混合：mixed
	 * @property string                              $msgtime        消息发送时间戳，utc时间，ms单位
	 *
	 * @property LimitWordMsg[]                      $limitWordMsgs
	 * @property WorkChatRemindSend[]                $workChatRemindSends
	 * @property WorkMsgAudit                        $audit
	 * @property WorkChat                            $chat
	 * @property WorkExternalContact                 $external
	 * @property WorkExternalContact                 $toExternal
	 * @property WorkUser                            $toUser
	 * @property WorkUser                            $user
	 * @property WorkMsgAuditInfoAgree[]             $workMsgAuditInfoAgrees
	 * @property WorkMsgAuditInfoCalendar[]          $workMsgAuditInfoCalendars
	 * @property WorkMsgAuditInfoCard[]              $workMsgAuditInfoCards
	 * @property WorkMsgAuditInfoChatrecord[]        $workMsgAuditInfoChatrecords
	 * @property WorkMsgAuditInfoChatrecordItem[]    $workMsgAuditInfoChatrecordItems
	 * @property WorkMsgAuditInfoCollect[]           $workMsgAuditInfoCollects
	 * @property WorkMsgAuditInfoDocmsg[]            $workMsgAuditInfoDocmsgs
	 * @property WorkMsgAuditInfoEmotion[]           $workMsgAuditInfoEmotions
	 * @property WorkMsgAuditInfoFile[]              $workMsgAuditInfoFiles
	 * @property WorkMsgAuditInfoImage[]             $workMsgAuditInfoImages
	 * @property WorkMsgAuditInfoLink[]              $workMsgAuditInfoLinks
	 * @property WorkMsgAuditInfoLocation[]          $workMsgAuditInfoLocations
	 * @property WorkMsgAuditInfoMarkdown[]          $workMsgAuditInfoMarkdowns
	 * @property WorkMsgAuditInfoMeeting[]           $workMsgAuditInfoMeetings
	 * @property WorkMsgAuditInfoMixed[]             $workMsgAuditInfoMixeds
	 * @property WorkMsgAuditInfoNews[]              $workMsgAuditInfoNews
	 * @property WorkMsgAuditInfoRedpacket[]         $workMsgAuditInfoRedpackets
	 * @property WorkMsgAuditInfoRevoke[]            $workMsgAuditInfoRevokes
	 * @property WorkMsgAuditInfoRevoke[]            $workMsgAuditInfoPreRevokes
	 * @property WorkMsgAuditInfoText[]              $workMsgAuditInfoTexts
	 * @property WorkMsgAuditInfoToInfo[]            $workMsgAuditInfoToInfos
	 * @property WorkMsgAuditInfoTodo[]              $workMsgAuditInfoTodos
	 * @property WorkMsgAuditInfoVideo[]             $workMsgAuditInfoVideos
	 * @property WorkMsgAuditInfoVoice[]             $workMsgAuditInfoVoices
	 * @property WorkMsgAuditInfoVote[]              $workMsgAuditInfoVotes
	 * @property WorkMsgAuditInfoWeapp[]             $workMsgAuditInfoWeapps
	 * @property WorkMsgAuditInfoMeetingVoiceCall[]  $workMsgAuditInfoMeetingVoiceCall
	 * @property WorkMsgAuditInfoVoipDocShare[]      $workMsgAuditInfoVoipDocShare
	 * @property WorkMsgAuditInfoExternalRedpacket[] $workMsgAuditInfoExternalRedpacket
	 */
	class WorkMsgAuditInfo extends \yii\db\ActiveRecord
	{
		const IS_WORK_USER = 1;
		const IS_EXTERNAL_USER = 2;
		const IS_ROBOT_USER = 3;

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_msg_audit_info}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['audit_id', 'from_type', 'to_type', 'user_id', 'external_id', 'to_user_id', 'to_external_id', 'chat_id'], 'integer'],
				[['msgid', 'action', 'from', 'tolist', 'msgtype'], 'required'],
				[['tolist', 'content'], 'string'],
				[['msgid', 'from', 'roomid'], 'string', 'max' => 64],
				[['action', 'msgtype'], 'string', 'max' => 32],
				[['audit_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAudit::className(), 'targetAttribute' => ['audit_id' => 'id']],
				[['chat_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkChat::className(), 'targetAttribute' => ['chat_id' => 'id']],
				[['external_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkExternalContact::className(), 'targetAttribute' => ['external_id' => 'id']],
				[['to_external_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkExternalContact::className(), 'targetAttribute' => ['to_external_id' => 'id']],
				[['to_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkUser::className(), 'targetAttribute' => ['to_user_id' => 'id']],
				[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkUser::className(), 'targetAttribute' => ['user_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'             => Yii::t('app', 'ID'),
				'audit_id'       => Yii::t('app', '会话存档ID'),
				'msgid'          => Yii::t('app', '消息id，消息的唯一标识，企业可以使用此字段进行消息去重'),
				'action'         => Yii::t('app', '消息动作，send发送消息；recall撤回消息；switch切换企业日志三种类型'),
				'from_type'      => Yii::t('app', '发送者身份：1、企业成员；2、外部联系人；3、群机器人'),
				'to_type'        => Yii::t('app', '接收者身份：1、企业成员；2、外部联系人；3、群机器人'),
				'user_id'        => Yii::t('app', '成员ID'),
				'external_id'    => Yii::t('app', '外部联系人ID'),
				'to_user_id'     => Yii::t('app', '接收者成员ID'),
				'to_external_id' => Yii::t('app', '接收者外部联系人ID'),
				'from'           => Yii::t('app', '消息发送方id。同一企业内容为userid，非相同企业为external_userid。消息如果是机器人发出，也为external_userid'),
				'tolist'         => Yii::t('app', '消息接收方列表，可能是多个，同一个企业内容为userid，非相同企业为external_userid'),
				'chat_id'        => Yii::t('app', '外部群ID'),
				'roomid'         => Yii::t('app', '群聊消息的群id。如果是单聊则为空'),
				'content'        => Yii::t('app', '缩略消息'),
				'msgtype'        => Yii::t('app', '消息类型：文本：text； 图片：image；撤回：revoke；同意：agree；不同意：disagree；语音：voice；视频：video；名片：card；位置：location；表情：emotion；文件：file；链接：link；小程序：weapp；会话记录：chatrecord；待办：todo；投票：vote；填表：collect；红包：redpacket；会议邀请：meeting；在线文档：docmsg；MarkDown：markdown；图文：news；日程：calendar；混合：mixed'),
				'msgtime'        => Yii::t('app', '消息发送时间戳，utc时间，ms单位'),
			];
		}

		/**
		 *
		 * @return object|\yii\db\Connection|null
		 *
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function getDb ()
		{
			return Yii::$app->get('mdb');
		}

		public function beforeSave ($insert)
		{
			$this->content = rawurlencode($this->content);

			return parent::beforeSave($insert); // TODO: Change the autogenerated stub
		}

		public function afterFind ()
		{
			if (!empty($this->content)) {
				$this->content = rawurldecode($this->content);
			}

			parent::afterFind(); // TODO: Change the autogenerated stub
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getLimitWordMsgs ()
		{
			return $this->hasMany(LimitWordMsg::className(), ['audit_info_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkChatRemindSends ()
		{
			return $this->hasMany(WorkChatRemindSend::className(), ['audit_info_id' => 'id']);
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
		public function getChat ()
		{
			return $this->hasOne(WorkChat::className(), ['id' => 'chat_id']);
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
		public function getToExternal ()
		{
			return $this->hasOne(WorkExternalContact::className(), ['id' => 'to_external_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getToUser ()
		{
			return $this->hasOne(WorkUser::className(), ['id' => 'to_user_id']);
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
		public function getWorkMsgAuditInfoAgrees ()
		{
			return $this->hasMany(WorkMsgAuditInfoAgree::className(), ['audit_info_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoCalendars ()
		{
			return $this->hasMany(WorkMsgAuditInfoCalendar::className(), ['audit_info_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoCards ()
		{
			return $this->hasMany(WorkMsgAuditInfoCard::className(), ['audit_info_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoChatrecords ()
		{
			return $this->hasMany(WorkMsgAuditInfoChatrecord::className(), ['audit_info_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoChatrecordItems ()
		{
			return $this->hasMany(WorkMsgAuditInfoChatrecordItem::className(), ['audit_info_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoCollects ()
		{
			return $this->hasMany(WorkMsgAuditInfoCollect::className(), ['audit_info_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoDocmsgs ()
		{
			return $this->hasMany(WorkMsgAuditInfoDocmsg::className(), ['audit_info_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoEmotions ()
		{
			return $this->hasMany(WorkMsgAuditInfoEmotion::className(), ['audit_info_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoFiles ()
		{
			return $this->hasMany(WorkMsgAuditInfoFile::className(), ['audit_info_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoImages ()
		{
			return $this->hasMany(WorkMsgAuditInfoImage::className(), ['audit_info_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoLinks ()
		{
			return $this->hasMany(WorkMsgAuditInfoLink::className(), ['audit_info_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoLocations ()
		{
			return $this->hasMany(WorkMsgAuditInfoLocation::className(), ['audit_info_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoMarkdowns ()
		{
			return $this->hasMany(WorkMsgAuditInfoMarkdown::className(), ['audit_info_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoMeetings ()
		{
			return $this->hasMany(WorkMsgAuditInfoMeeting::className(), ['audit_info_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoMixeds ()
		{
			return $this->hasMany(WorkMsgAuditInfoMixed::className(), ['audit_info_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoNews ()
		{
			return $this->hasMany(WorkMsgAuditInfoNews::className(), ['audit_info_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoRedpackets ()
		{
			return $this->hasMany(WorkMsgAuditInfoRedpacket::className(), ['audit_info_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoRevokes ()
		{
			return $this->hasMany(WorkMsgAuditInfoRevoke::className(), ['audit_info_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoPreRevokes ()
		{
			return $this->hasMany(WorkMsgAuditInfoRevoke::className(), ['pre_info_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoTexts ()
		{
			return $this->hasMany(WorkMsgAuditInfoText::className(), ['audit_info_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoToInfos ()
		{
			return $this->hasMany(WorkMsgAuditInfoToInfo::className(), ['audit_info_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoTodos ()
		{
			return $this->hasMany(WorkMsgAuditInfoTodo::className(), ['audit_info_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoVideos ()
		{
			return $this->hasMany(WorkMsgAuditInfoVideo::className(), ['audit_info_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoVoices ()
		{
			return $this->hasMany(WorkMsgAuditInfoVoice::className(), ['audit_info_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoVotes ()
		{
			return $this->hasMany(WorkMsgAuditInfoVote::className(), ['audit_info_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoWeapps ()
		{
			return $this->hasMany(WorkMsgAuditInfoWeapp::className(), ['audit_info_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoMeetingVoiceCall ()
		{
			return $this->hasMany(WorkMsgAuditInfoMeetingVoiceCall::className(), ['audit_info_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoVoipDocShare ()
		{
			return $this->hasMany(WorkMsgAuditInfoVoipDocShare::className(), ['audit_info_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoExternalRedpacket ()
		{
			return $this->hasMany(WorkMsgAuditInfoExternalRedpacket::className(), ['audit_info_id' => 'id']);
		}

		public function dumpData ($withFrom = false, $withTo = false, $withInfo = false, $withChat = false)
		{
			$data = [
				'id'             => $this->id,
				'msgid'          => $this->msgid,
				'action'         => $this->action,
				'from_type'      => $this->from_type,
				'to_type'        => $this->to_type,
				'user_id'        => $this->user_id,
				'external_id'    => $this->external_id,
				'to_user_id'     => $this->to_user_id,
				'to_external_id' => $this->to_external_id,
				'chat_id'        => $this->chat_id,
				'roomid'         => $this->roomid,
				'content'        => $this->content,
				'msgtype'        => $this->msgtype,
				'msgtime'        => $this->msgtime,
			];
			//添加质检数据
            $inspection = InspectionViolation::find()
                ->where(['work_msg_audit_info_id' => $this->id])
                ->andWhere(['is_delete' => 0])
                ->one();
            $data['inspection'] = [];
            if(!empty($inspection)) {
                $workUser = WorkUser::findOne($inspection->user_id);
                $data['inspection'] = [
                    'name' => $workUser->name
                ];
            }

			if ($withFrom) {
				$data['from_info'] = [];
				if ($this->from_type == self::IS_WORK_USER) {
					if (!empty($this->user)) {
						$data['from_info'] = $this->user->dumpMiniData();
					} else {
						$data['from_info'] = [
							'name'           => '未知',
							'name_convert'   => '',
							'avatar'         => SUtils::makeGravatar($this->from),
							'corp_name'      => '',
							'corp_full_name' => '',
						];
					}
				}

				if ($this->from_type == self::IS_EXTERNAL_USER) {
					if (!empty($this->external)) {
						$data['from_info'] = $this->external->dumpMiniData();
					} else {
						$data['from_info'] = [
							'name'           => '未知',
							'name_convert'   => '',
							'avatar'         => SUtils::makeGravatar($this->from),
							'corp_name'      => '',
							'corp_full_name' => '',
						];
					}
				}
			}

			if ($withTo) {
				$data['to_info'] = [];
				if ($this->to_type == self::IS_WORK_USER) {
					$data['to_info'] = $this->toUser->dumpMiniData();
				}

				if ($this->to_type == self::IS_EXTERNAL_USER) {
					if (!empty($this->toExternal)) {
						$data['to_info'] = $this->toExternal->dumpMiniData();
					} else {
						$data['to_info'] = [
							'name'           => '未知',
							'name_convert'   => '',
							'avatar'         => SUtils::makeGravatar($this->from),
							'corp_name'      => '',
							'corp_full_name' => '',
						];
					}
				}
			}

			if ($withInfo) {
				$data['info'] = [];
				switch ($this->msgtype) {
					case WorkMsgAuditInfoText::MSG_TYPE:
						if (!empty($this->workMsgAuditInfoTexts)) {
							$data['info'] = $this->workMsgAuditInfoTexts[0]->dumpData();
						}

						break;
					case WorkMsgAuditInfoImage::MSG_TYPE:
						if (!empty($this->workMsgAuditInfoImages)) {
							$data['info'] = $this->workMsgAuditInfoImages[0]->dumpData();
						}

						break;
					case WorkMsgAuditInfoRevoke::MSG_TYPE:
						if (!empty($this->workMsgAuditInfoRevokes)) {
							$data['info'] = $this->workMsgAuditInfoRevokes[0]->dumpData();
						}

						break;
					case WorkMsgAuditInfoAgree::AGREE_MSG_TYPE:
					case WorkMsgAuditInfoAgree::DISAGREE_MSG_TYPE:
						if (!empty($this->workMsgAuditInfoAgrees)) {
							$data['info'] = $this->workMsgAuditInfoAgrees[0]->dumpData();
						}

						break;
					case WorkMsgAuditInfoVoice::MSG_TYPE:
						if (!empty($this->workMsgAuditInfoVoices)) {
							$data['info'] = $this->workMsgAuditInfoVoices[0]->dumpData();
						}

						break;
					case WorkMsgAuditInfoVideo::MSG_TYPE:
						if (!empty($this->workMsgAuditInfoVideos)) {
							$data['info'] = $this->workMsgAuditInfoVideos[0]->dumpData();
						}

						break;
					case WorkMsgAuditInfoCard::MSG_TYPE:
						if (!empty($this->workMsgAuditInfoCards)) {
							$data['info'] = $this->workMsgAuditInfoCards[0]->dumpData();
						}

						break;
					case WorkMsgAuditInfoLocation::MSG_TYPE:
						if (!empty($this->workMsgAuditInfoLocations)) {
							$data['info'] = $this->workMsgAuditInfoLocations[0]->dumpData();
						}

						break;
					case WorkMsgAuditInfoEmotion::MSG_TYPE:
						if (!empty($this->workMsgAuditInfoEmotions)) {
							$data['info'] = $this->workMsgAuditInfoEmotions[0]->dumpData();
						}

						break;
					case WorkMsgAuditInfoFile::MSG_TYPE:
						if (!empty($this->workMsgAuditInfoFiles)) {
							$data['info'] = $this->workMsgAuditInfoFiles[0]->dumpData();
						}

						break;
					case WorkMsgAuditInfoLink::MSG_TYPE:
						if (!empty($this->workMsgAuditInfoLinks)) {
							$data['info'] = $this->workMsgAuditInfoLinks[0]->dumpData();
						}

						break;
					case WorkMsgAuditInfoWeapp::MSG_TYPE:
						if (!empty($this->workMsgAuditInfoWeapps)) {
							$data['info'] = $this->workMsgAuditInfoWeapps[0]->dumpData();
						}

						break;
					case WorkMsgAuditInfoChatrecord::MSG_TYPE:
						if (!empty($this->workMsgAuditInfoChatrecords)) {
							$data['info'] = $this->workMsgAuditInfoChatrecords[0]->dumpData();
						}

						break;
					case WorkMsgAuditInfoTodo::MSG_TYPE:
						if (!empty($this->workMsgAuditInfoTodos)) {
							$data['info'] = $this->workMsgAuditInfoTodos[0]->dumpData();
						}

						break;
					case WorkMsgAuditInfoVote::MSG_TYPE:
						if (!empty($this->workMsgAuditInfoVotes)) {
							$data['info'] = $this->workMsgAuditInfoVotes[0]->dumpData();
						}

						break;
					case WorkMsgAuditInfoCollect::MSG_TYPE:
						if (!empty($this->workMsgAuditInfoCollects)) {
							$data['info'] = $this->workMsgAuditInfoCollects[0]->dumpData();
						}

						break;
					case WorkMsgAuditInfoRedpacket::MSG_TYPE:
						if (!empty($this->workMsgAuditInfoRedpackets)) {
							$data['info'] = $this->workMsgAuditInfoRedpackets[0]->dumpData();
						}

						break;
					case WorkMsgAuditInfoMeeting::MSG_TYPE:
						if (!empty($this->workMsgAuditInfoMeetings)) {
							$data['info'] = $this->workMsgAuditInfoMeetings[0]->dumpData();
						}

						break;
					case WorkMsgAuditInfoDocmsg::MSG_TYPE:
						if (!empty($this->workMsgAuditInfoDocmsgs)) {
							$data['info'] = $this->workMsgAuditInfoDocmsgs[0]->dumpData();
						}

						break;
					case WorkMsgAuditInfoMarkdown::MSG_TYPE:
						if (!empty($this->workMsgAuditInfoMarkdowns)) {
							$data['info'] = $this->workMsgAuditInfoMarkdowns[0]->dumpData();
						}

						break;
					case WorkMsgAuditInfoNews::MSG_TYPE:
						if (!empty($this->workMsgAuditInfoNews)) {
							$data['info'] = $this->workMsgAuditInfoNews[0]->dumpData();
						}

						break;
					case WorkMsgAuditInfoCalendar::MSG_TYPE:
						if (!empty($this->workMsgAuditInfoCalendars)) {
							$data['info'] = $this->workMsgAuditInfoCalendars[0]->dumpData();
						}

						break;
					case WorkMsgAuditInfoMixed::MSG_TYPE:
						if (!empty($this->workMsgAuditInfoMixeds)) {
							$itemData = [];
							foreach ($this->workMsgAuditInfoMixeds as $workMsgAuditInfoMixed) {
								array_push($itemData, $workMsgAuditInfoMixed->dumpData());
							}

							if (!empty($itemData)) {
								$itemData = array_column($itemData, NULL, 'sort');
								ksort($itemData);
							}

							$data['info'] = $itemData;
						}

						break;
					case WorkMsgAuditInfoMeetingVoiceCall::MSG_TYPE:
						if (!empty($this->workMsgAuditInfoMeetingVoiceCall)) {
							$data['info'] = $this->workMsgAuditInfoMeetingVoiceCall[0]->dumpData();
						}

						break;
					case WorkMsgAuditInfoVoipDocShare::MSG_TYPE:
						if (!empty($this->workMsgAuditInfoVoipDocShare)) {
							$data['info'] = $this->workMsgAuditInfoVoipDocShare[0]->dumpData();
						}

						break;
					case WorkMsgAuditInfoExternalRedpacket::MSG_TYPE:
						if (!empty($this->workMsgAuditInfoExternalRedpacket)) {
							$data['info'] = $this->workMsgAuditInfoExternalRedpacket[0]->dumpData();
						}

						break;
					default:
						break;
				}
			}

			if ($withChat && !empty($this->chat_id)) {
				$data['avatarData'] = WorkChat::getChatAvatar($this->chat_id);
			}

			return $data;
		}

		/**
		 * @param $corpId
		 * @param $msgData
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 * @throws \Throwable
		 */
		public static function create ($corpId, $msgData)
		{
			$workCorp = WorkCorp::findOne($corpId);
			if (empty($workCorp)) {
				throw new InvalidDataException('参数不正确');
			}

			if (empty($workCorp->workMsgAudit)) {
				throw new InvalidDataException('参数不正确');
			}

			$auditId = $workCorp->workMsgAudit->id;

			$auditInfo = self::findOne(['msgid' => $msgData['msgid']]);

			if (empty($auditInfo)) {
				$auditInfo           = new self();
				$auditInfo->audit_id = $auditId;
				$auditInfo->msgid    = $msgData['msgid'];
				$auditInfo->action   = $msgData['action'];

				$fromUser = !empty($msgData['from']) ? $msgData['from'] : (!empty($msgData['user']) ? $msgData['user'] : "");
				if (!empty($fromUser)) {
					switch (SUtils::getUserType($fromUser)) {
						case SUtils::IS_WORK_USER:
							$auditInfo->from_type = self::IS_WORK_USER;

							$workUserId = WorkUser::getUserId($corpId, $fromUser);
							if (!empty($workUserId)) {
								$auditInfo->user_id = $workUserId;
							}

							break;
						case SUtils::IS_EXTERNAL_USER:
							$auditInfo->from_type = self::IS_EXTERNAL_USER;

							$externalId = WorkExternalContact::getExternalId($corpId, $fromUser);
							if (!empty($externalId)) {
								$auditInfo->external_id = $externalId;
							}

							break;
						case SUtils::IS_ROBOT_USER:
							$auditInfo->from_type = self::IS_ROBOT_USER;

							break;
						default:

							break;
					}
					$auditInfo->from = $fromUser;
				}

				if (!empty($msgData['tolist'])) {
					$auditInfo->tolist = implode(',', $msgData['tolist']);
				}

				if (!empty($msgData['roomid'])) {
					$auditInfo->roomid = $msgData['roomid'];

					$chatId = WorkChat::getChatId($corpId, $msgData['roomid'], $auditInfo->user_id);
					if (!empty($chatId)) {
						$auditInfo->chat_id = $chatId;
					}
				} else {
					switch (SUtils::getUserType($auditInfo->tolist)) {
						case SUtils::IS_WORK_USER:
							$auditInfo->to_type = self::IS_WORK_USER;

							$workUserId = WorkUser::getUserId($corpId, $auditInfo->tolist);
							if (!empty($workUserId)) {
								$auditInfo->to_user_id = $workUserId;
							}

							break;
						case SUtils::IS_EXTERNAL_USER:
							$auditInfo->to_type = self::IS_EXTERNAL_USER;

							$externalId = WorkExternalContact::getExternalId($corpId, $auditInfo->tolist);
							if (!empty($externalId)) {
								$auditInfo->to_external_id = $externalId;
							}

							break;
						case SUtils::IS_ROBOT_USER:
							$auditInfo->to_type = self::IS_ROBOT_USER;

							break;
						default:

							break;
					}
				}

				if (!empty($msgData['msgtype'])) {
					$auditInfo->msgtype = $msgData['msgtype'];
				}

				$auditInfo->msgtime = !empty($msgData['msgtime']) ? $msgData['msgtime'] : (!empty($msgData['time']) ? $msgData['time'] : "");

				if (!$auditInfo->validate() || !$auditInfo->save()) {
					throw new InvalidDataException(SUtils::modelError($auditInfo));
				}

				if (!empty($msgData['tolist'])) {
					WorkMsgAuditInfoToInfo::create($corpId, $auditInfo->id, $msgData['tolist']);
				}

				if (!empty($msgData['msgtype'])) {
					if ($auditId == 98 && !in_array($msgData['msgtype'], [WorkMsgAuditInfoText::MSG_TYPE, WorkMsgAuditInfoImage::MSG_TYPE])) {
						\Yii::error($msgData, 'msgAuditContent');
					}
					switch ($msgData['msgtype']) {
						case WorkMsgAuditInfoText::MSG_TYPE:
							$auditInfo->content = $msgData[$msgData['msgtype']]['content'];

							WorkMsgAuditInfoText::create($auditInfo->id, $msgData[$msgData['msgtype']]);
							Yii::$app->work->push(new MsgAuditNoticeJob([
								'auditId'      => $auditId,
								'categoryType' => WorkMsgAuditCategory::TEXT_CATEGORY,
								'content'      => '新的文本消息',
							]));

							break;
						case WorkMsgAuditInfoImage::MSG_TYPE:
							$auditInfo->content = '图片';

							WorkMsgAuditInfoImage::create($auditInfo, $msgData[$msgData['msgtype']]);
							Yii::$app->work->push(new MsgAuditNoticeJob([
								'auditId'      => $auditId,
								'categoryType' => WorkMsgAuditCategory::IMAGE_CATEGORY,
								'content'      => '新的图片消息',
							]));

							break;
						case WorkMsgAuditInfoRevoke::MSG_TYPE:
							$auditInfo->content = '撤回';

							WorkMsgAuditInfoRevoke::create($auditInfo->id, $msgData[$msgData['msgtype']]);
							Yii::$app->work->push(new MsgAuditNoticeJob([
								'auditId'      => $auditId,
								'categoryType' => WorkMsgAuditCategory::REVOKE_CATEGORY,
								'content'      => '新的撤回消息',
							]));

							break;
						case WorkMsgAuditInfoAgree::AGREE_MSG_TYPE:
							$auditInfo->content = '同意会话存档';

							WorkMsgAuditInfoAgree::create($corpId, $auditInfo->id, $msgData[$msgData['msgtype']], $msgData['msgtype']);
							Yii::$app->work->push(new MsgAuditNoticeJob([
								'auditId'      => $auditId,
								'categoryType' => WorkMsgAuditCategory::AGREE_CATEGORY,
								'content'      => '新的同意会话存档消息',
							]));

							break;
						case WorkMsgAuditInfoAgree::DISAGREE_MSG_TYPE:
							$auditInfo->content = '拒绝会话存档';

							WorkMsgAuditInfoAgree::create($corpId, $auditInfo->id, $msgData[$msgData['msgtype']], $msgData['msgtype']);
							Yii::$app->work->push(new MsgAuditNoticeJob([
								'auditId'      => $auditId,
								'categoryType' => WorkMsgAuditCategory::DISAGREE_CATEGORY,
								'content'      => '新的拒绝会话存档消息',
							]));

							break;
						case WorkMsgAuditInfoVoice::MSG_TYPE:
							$auditInfo->content = '语音';

							WorkMsgAuditInfoVoice::create($auditInfo, $msgData[$msgData['msgtype']]);
							Yii::$app->work->push(new MsgAuditNoticeJob([
								'auditId'      => $auditId,
								'categoryType' => WorkMsgAuditCategory::VOICE_CATEGORY,
								'content'      => '新的语音消息',
							]));

							break;
						case WorkMsgAuditInfoVideo::MSG_TYPE:
							$auditInfo->content = '视频';

							WorkMsgAuditInfoVideo::create($auditInfo, $msgData[$msgData['msgtype']]);
							Yii::$app->work->push(new MsgAuditNoticeJob([
								'auditId'      => $auditId,
								'categoryType' => WorkMsgAuditCategory::VIDEO_CATEGORY,
								'content'      => '新的视频消息',
							]));

							break;
						case WorkMsgAuditInfoCard::MSG_TYPE:
							$auditInfo->content = '名片';

							WorkMsgAuditInfoCard::create($corpId, $auditInfo->id, $msgData[$msgData['msgtype']]);
							Yii::$app->work->push(new MsgAuditNoticeJob([
								'auditId'      => $auditId,
								'categoryType' => WorkMsgAuditCategory::CARD_CATEGORY,
								'content'      => '新的名片消息',
							]));

							break;
						case WorkMsgAuditInfoLocation::MSG_TYPE:
							$auditInfo->content = '位置';

							WorkMsgAuditInfoLocation::create($auditInfo->id, $msgData[$msgData['msgtype']]);
							Yii::$app->work->push(new MsgAuditNoticeJob([
								'auditId'      => $auditId,
								'categoryType' => WorkMsgAuditCategory::LOCATION_CATEGORY,
								'content'      => '新的位置消息',
							]));

							break;
						case WorkMsgAuditInfoEmotion::MSG_TYPE:
							$auditInfo->content = '表情';

							WorkMsgAuditInfoEmotion::create($auditInfo, $msgData[$msgData['msgtype']]);
							Yii::$app->work->push(new MsgAuditNoticeJob([
								'auditId'      => $auditId,
								'categoryType' => WorkMsgAuditCategory::EMOTION_CATEGORY,
								'content'      => '新的Emotion表情消息',
							]));

							break;
						case WorkMsgAuditInfoFile::MSG_TYPE:
							$auditInfo->content = '文件';

							WorkMsgAuditInfoFile::create($auditInfo, $msgData[$msgData['msgtype']]);
							Yii::$app->work->push(new MsgAuditNoticeJob([
								'auditId'      => $auditId,
								'categoryType' => WorkMsgAuditCategory::FILE_CATEGORY,
								'content'      => '新的文件消息',
							]));

							break;
						case WorkMsgAuditInfoLink::MSG_TYPE:
							$auditInfo->content = '链接';

							WorkMsgAuditInfoLink::create($auditInfo->id, $msgData[$msgData['msgtype']]);
							Yii::$app->work->push(new MsgAuditNoticeJob([
								'auditId'      => $auditId,
								'categoryType' => WorkMsgAuditCategory::LINK_CATEGORY,
								'content'      => '新的链接消息',
							]));

							break;
						case WorkMsgAuditInfoWeapp::MSG_TYPE:
							$auditInfo->content = '小程序';

							WorkMsgAuditInfoWeapp::create($auditInfo->id, $msgData[$msgData['msgtype']]);
							Yii::$app->work->push(new MsgAuditNoticeJob([
								'auditId'      => $auditId,
								'categoryType' => WorkMsgAuditCategory::WEAPP_CATEGORY,
								'content'      => '新的小程序消息',
							]));

							break;
						case WorkMsgAuditInfoChatrecord::MSG_TYPE:
							$auditInfo->content = '聊天记录';

							WorkMsgAuditInfoChatrecord::create($corpId, $auditInfo, $msgData[$msgData['msgtype']]);
							Yii::$app->work->push(new MsgAuditNoticeJob([
								'auditId'      => $auditId,
								'categoryType' => WorkMsgAuditCategory::CHATRECORD_CATEGORY,
								'content'      => '新的聊天记录消息',
							]));

							break;
						case WorkMsgAuditInfoTodo::MSG_TYPE:
							$auditInfo->content = '待办';

							WorkMsgAuditInfoTodo::create($auditInfo->id, $msgData[$msgData['msgtype']]);
							Yii::$app->work->push(new MsgAuditNoticeJob([
								'auditId'      => $auditId,
								'categoryType' => WorkMsgAuditCategory::TODO_CATEGORY,
								'content'      => '新的待办消息',
							]));

							break;
						case WorkMsgAuditInfoVote::MSG_TYPE:
							$auditInfo->content = '投票';
							try {
								WorkMsgAuditInfoVote::create($auditInfo->id, $msgData[$msgData['msgtype']]);
							} catch (\Exception $e) {
								\Yii::error($e->getMessage(), 'voteMsg');
							}
							Yii::$app->work->push(new MsgAuditNoticeJob([
								'auditId'      => $auditId,
								'categoryType' => WorkMsgAuditCategory::VOTE_CATEGORY,
								'content'      => '新的投票消息',
							]));

							break;
						case WorkMsgAuditInfoCollect::MSG_TYPE:
							$auditInfo->content = '填表';

							WorkMsgAuditInfoCollect::create($auditInfo->id, $msgData[$msgData['msgtype']]);
							Yii::$app->work->push(new MsgAuditNoticeJob([
								'auditId'      => $auditId,
								'categoryType' => WorkMsgAuditCategory::COLLECT_CATEGORY,
								'content'      => '新的填表消息',
							]));

							break;
						case WorkMsgAuditInfoRedpacket::MSG_TYPE:
							$auditInfo->content = '红包';

							WorkMsgAuditInfoRedpacket::create($auditInfo->id, $msgData[$msgData['msgtype']]);
							Yii::$app->work->push(new MsgAuditNoticeJob([
								'auditId'      => $auditId,
								'categoryType' => WorkMsgAuditCategory::REDPACKET_CATEGORY,
								'content'      => '新的红包消息',
							]));

							break;
						case WorkMsgAuditInfoMeeting::MSG_TYPE:
							$auditInfo->content = '会议';
							try {
								WorkMsgAuditInfoMeeting::create($auditInfo->id, $msgData[$msgData['msgtype']]);
							} catch (\Exception $e) {
								\Yii::error($e->getMessage(), 'meetingMsg');
							}
							Yii::$app->work->push(new MsgAuditNoticeJob([
								'auditId'      => $auditId,
								'categoryType' => WorkMsgAuditCategory::MEETING_CATEGORY,
								'content'      => '新的会议消息',
							]));

							break;
						case WorkMsgAuditInfoDocmsg::MSG_TYPE:
							$auditInfo->content = '在线文档';
							try {
								WorkMsgAuditInfoDocmsg::create($corpId, $auditInfo->id, $msgData['doc']);
							} catch (\Exception $e) {
								\Yii::error($e->getMessage(), 'docMsg');
							}

							Yii::$app->work->push(new MsgAuditNoticeJob([
								'auditId'      => $auditId,
								'categoryType' => WorkMsgAuditCategory::DOCMSG_CATEGORY,
								'content'      => '新的在线文档消息',
							]));

							break;
						case WorkMsgAuditInfoMarkdown::MSG_TYPE:
							$auditInfo->content = 'MarkDown';
							try {
								WorkMsgAuditInfoMarkdown::create($auditInfo->id, $msgData['info']);
							} catch (\Exception $e) {
								\Yii::error($e->getMessage(), 'MarkDown');
							}

							Yii::$app->work->push(new MsgAuditNoticeJob([
								'auditId'      => $auditId,
								'categoryType' => WorkMsgAuditCategory::MARKDOWN_CATEGORY,
								'content'      => '新的Markdown消息',
							]));

							break;
						case WorkMsgAuditInfoNews::MSG_TYPE:
							$auditInfo->content = '图文';

							WorkMsgAuditInfoNews::create($auditInfo->id, $msgData['info']);
							Yii::$app->work->push(new MsgAuditNoticeJob([
								'auditId'      => $auditId,
								'categoryType' => WorkMsgAuditCategory::NEWS_CATEGORY,
								'content'      => '新的图文消息',
							]));

							break;
						case WorkMsgAuditInfoCalendar::MSG_TYPE:
							$auditInfo->content = '日程';

							WorkMsgAuditInfoCalendar::create($corpId, $auditInfo->id, $msgData[$msgData['msgtype']]);
							Yii::$app->work->push(new MsgAuditNoticeJob([
								'auditId'      => $auditId,
								'categoryType' => WorkMsgAuditCategory::CALENDAR_CATEGORY,
								'content'      => '新的日程消息',
							]));

							break;
						case WorkMsgAuditInfoMixed::MSG_TYPE:
							$auditInfo->content = '混合';

							WorkMsgAuditInfoMixed::create($corpId, $auditInfo, $msgData[$msgData['msgtype']]);
							Yii::$app->work->push(new MsgAuditNoticeJob([
								'auditId'      => $auditId,
								'categoryType' => WorkMsgAuditCategory::MIXED_CATEGORY,
								'content'      => '新的混合类型消息',
							]));

							break;
						case WorkMsgAuditInfoMeetingVoiceCall::MSG_TYPE:
							$auditInfo->content = '音频存档';
							try {
								WorkMsgAuditInfoMeetingVoiceCall::create($auditInfo, $msgData['voiceid'], $msgData[$msgData['msgtype']]);
							} catch (\Exception $e) {
								throw new InvalidDataException($e->getMessage());
							}
							Yii::$app->work->push(new MsgAuditNoticeJob([
								'auditId'      => $auditId,
								'categoryType' => WorkMsgAuditCategory::MEETING_VOICE_CALL_CATEGORY,
								'content'      => '新的音频存档消息',
							]));

							break;
						case WorkMsgAuditInfoVoipDocShare::MSG_TYPE:
							$auditInfo->content = '音频共享文档';
							try {
								WorkMsgAuditInfoVoipDocShare::create($auditInfo, $msgData['voipid'], $msgData[$msgData['msgtype']]);
							} catch (\Exception $e) {
								throw new InvalidDataException($e->getMessage());
							}
							Yii::$app->work->push(new MsgAuditNoticeJob([
								'auditId'      => $auditId,
								'categoryType' => WorkMsgAuditCategory::VOIP_DOC_SHARE_CATEGORY,
								'content'      => '新的音频共享文档消息',
							]));

							break;
						case WorkMsgAuditInfoExternalRedpacket::MSG_TYPE:
							$auditInfo->content = '互动红包';
							try {
								WorkMsgAuditInfoExternalRedpacket::create($auditInfo->id, $msgData['redpacket']);
							} catch (\Exception $e) {
								throw new InvalidDataException($e->getMessage());
							}
							Yii::$app->work->push(new MsgAuditNoticeJob([
								'auditId'      => $auditId,
								'categoryType' => WorkMsgAuditCategory::EXTERNAL_REDPACKET_CATEGORY,
								'content'      => '新的互动红包消息',
							]));

							break;
						default:
							break;
					}
					\Yii::error($auditInfo->id, 'audit_id');
					$auditInfo->save();

					//通知
					$userCorp   = UserCorpRelation::findOne(['corp_id' => $corpId]);
					$uid        = $userCorp->uid;
					$chatName   = '';
					$chatUserId = [];
					if (!empty($auditInfo->chat_id)) {
						$msg_type = 3;//消息类型:0：内部；1：外部；2：内部群聊；3：外部群聊
						$workChat = WorkChat::findOne($auditInfo->chat_id);
						if (!empty($workChat)) {
							$chatName = !empty($workChat->name) ? $workChat->name : $auditInfo->tolist;
							if ($workChat->group_chat == 1) {
								$msg_type = 2;
							}
						}
						$chatInfo = WorkChatInfo::find()->where(['chat_id' => $auditInfo->chat_id])->andWhere(['>', 'user_id', 0])->select('user_id')->all();
						if (!empty($chatInfo)) {
							$chatUserId = array_column($chatInfo, 'user_id');
						}
					} elseif ($auditInfo->from_type == 1 && $auditInfo->to_type == 1) {
						$msg_type = 0;
					} else {
						$msg_type = 1;
					}

					if (
						$msg_type == 1
						&& (
							(empty($auditInfo->user_id) && empty($auditInfo->external_id) && empty($auditInfo->to_user_id) && empty($auditInfo->to_external_id))
							|| (!empty($auditInfo->user_id) && empty($auditInfo->to_external_id))
							|| (!empty($auditInfo->to_user_id) && empty($auditInfo->external_id))
						)
					) {
						//TODO no websocket code
					} else {
						\Yii::$app->websocket->send([
							'channel' => 'push-message',
							'to'      => $uid,
							'type'    => WebsocketUtil::WORK_TYPE,
							'wx_id'   => $workCorp->id,
							'info'    => [
								'type'         => 'audit',
								'from'         => $uid,
								'chat_name'    => $chatName,
								'chat_user_id' => implode(',', $chatUserId),
								'corp_id'      => $workCorp->corpid,
								'msg_list'     => $auditInfo->dumpData(true, true, true, true),
								'msg_type'     => $msg_type
							]
						]);
					}
				}
			}

			return $auditInfo->id;
		}

		public static function getMsgContent ($fromId, $toId = '', $lastTime = 0, $type = 0, $size = 15, $otherData = [])
		{
			$content = [];

			$msgType      = !empty($otherData['msg_type']) ? $otherData['msg_type'] : '';
			$searchName   = isset($otherData['search_name']) ? $otherData['search_name'] : '';
			$startDate    = !empty($otherData['start_date']) ? $otherData['start_date'] : '';
			$endDate      = !empty($otherData['end_date']) ? $otherData['end_date'] : '';
			$chatFromId   = !empty($otherData['chat_from_id']) ? $otherData['chat_from_id'] : '';
			$chatFromType = !empty($otherData['chat_from_type']) ? $otherData['chat_from_type'] : '';

			$msgContent = self::find()->alias('ai')->where(['not in', 'msgtype', ["meeting_voice_call", "voip_doc_share"]]);

			//类型
			if (!empty($msgType)) {
				if ($msgType != 'other') {
					$msgContent = $msgContent->andWhere(['ai.msgtype' => $msgType]);
				} else {
					$msgContent = $msgContent->andWhere('ai.msgtype not in ("text","image","voice","video","file","weapp","news")');
				}
			}

			//搜索词
			if ($searchName !== '') {
				$msgContent = $msgContent->leftJoin('{{%work_msg_audit_info_text}} ait', 'ai.id = ait.audit_info_id');
				$msgContent = $msgContent->andWhere(['ai.msgtype' => 'text']);
				$msgContent = $msgContent->andWhere(['like', 'ait.content_convert', $searchName]);
			}

			//时间范围
			if (!empty($startDate) && !empty($endDate)) {
				$startTime  = strtotime($startDate);
				$endTime    = strtotime($endDate . ' 23:59:59');
				$startTime  = $startTime * 1000;
				$endTime    = $endTime * 1000;
				$msgContent = $msgContent->andWhere(['between', 'ai.msgtime', $startTime, $endTime]);
			}

			switch ($type) {
				case 0:
					$msgContent = $msgContent->andWhere(['ai.from_type' => self::IS_WORK_USER, 'ai.to_type' => self::IS_WORK_USER])
						->andWhere(['or', ['ai.user_id' => $fromId, 'ai.to_user_id' => $toId], ['ai.user_id' => $toId, 'ai.to_user_id' => $fromId]]);

					if (!empty($lastTime)) {
						$msgContent = $msgContent->andWhere(['<', 'ai.msgtime', $lastTime]);
					}
                    if($otherData['is_time']) {//兼容h5端日期搜索
                        $day = time();
                        $start_day =  strtotime(date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m")-7,1,date("Y")))) ;
                        $startTime  = $start_day * 1000;
                        $endTime    = $day * 1000;
                        $msgContent = $msgContent
                            ->select(["*,DATE_FORMAT(FROM_UNIXTIME( msgtime / 1000 ),'%Y-%m-%d') dates"])
                            ->andWhere(['between', 'ai.msgtime', $startTime, $endTime])
                            ->groupBy('dates')
                            ->orderBy(['msgtime' => SORT_DESC])
                            ->asArray()
                            ->all();
                        $list = array_column($msgContent, 'dates');
                        return $list;
                    }
					$msgContent = $msgContent->orderBy(['ai.msgtime' => SORT_DESC])
						->limit($size)
						->all();
					if (!empty($msgContent)) {
						/** @var WorkMsgAuditInfo $msgInfo */
						foreach ($msgContent as $msgInfo) {
							$dumpData = $msgInfo->dumpData(true, true, true);
							if (!empty($dumpData['info'])) {
								array_push($content, $dumpData);
							}
						}
					}

					break;
				case 1:
					$msgContent = $msgContent->andWhere(['or', ['from_type' => self::IS_WORK_USER, 'to_type' => self::IS_EXTERNAL_USER, 'user_id' => $fromId, 'to_external_id' => $toId], ['from_type' => self::IS_EXTERNAL_USER, 'to_type' => self::IS_WORK_USER, 'external_id' => $toId, 'to_user_id' => $fromId]]);

					if (!empty($lastTime)) {
						$msgContent = $msgContent->andWhere(['<', 'msgtime', $lastTime]);
					}
					if($otherData['is_time']) {//兼容h5端日期搜索
					    $day = time();
					    $start_day =  strtotime(date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m")-7,1,date("Y")))) ;
                        $startTime  = $start_day * 1000;
                        $endTime    = $day * 1000;
                        $msgContent = $msgContent
                            ->select(["*,DATE_FORMAT(FROM_UNIXTIME( msgtime / 1000 ),'%Y-%m-%d') dates"])
                            ->andWhere(['between', 'ai.msgtime', $startTime, $endTime])
                            ->groupBy('dates')
                            ->orderBy(['msgtime' => SORT_DESC])
                            ->asArray()
                            ->all();
                        $list = array_column($msgContent, 'dates');
                        return $list;
                    }

					$msgContent = $msgContent->orderBy(['msgtime' => SORT_DESC])
						->limit($size)
						->all();
					if (!empty($msgContent)) {
						/** @var WorkMsgAuditInfo $msgInfo */
						foreach ($msgContent as $msgInfo) {
							$dumpData = $msgInfo->dumpData(true, true, true);
							if (!empty($dumpData['info'])) {
								array_push($content, $dumpData);
							}
						}
					}

					break;
				case 2:
				case 3:
					$msgContent = $msgContent->andWhere(['chat_id' => $fromId]);

					if (!empty($lastTime)) {
						$msgContent = $msgContent->andWhere(['<', 'msgtime', $lastTime]);
					}

					if (!empty($chatFromType) && !empty($chatFromId)) {
						if ($chatFromType == 1) {
							$msgContent = $msgContent->andWhere(['from_type' => 1, 'user_id' => $chatFromId]);
						} elseif ($chatFromType == 2) {
							$msgContent = $msgContent->andWhere(['from_type' => 2, 'external_id' => $chatFromId]);
						} elseif ($chatFromType == 3) {
							$msgContent = $msgContent->andWhere(['from_type' => 2, 'from' => $chatFromId]);
						}
					}
                    if($otherData['is_time']) {//兼容h5端日期搜索
                        $day = time();
                        $start_day =  strtotime(date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m")-7,1,date("Y")))) ;
                        $startTime  = $start_day * 1000;
                        $endTime    = $day * 1000;
                        $msgContent = $msgContent
                            ->select(["*,DATE_FORMAT(FROM_UNIXTIME( msgtime / 1000 ),'%Y-%m-%d') dates"])
                            ->andWhere(['between', 'ai.msgtime', $startTime, $endTime])
                            ->groupBy('dates')
                            ->orderBy(['msgtime' => SORT_DESC])
                            ->asArray()
                            ->all();
                        $list = array_column($msgContent, 'dates');
                        return $list;
                    }
					$msgContent = $msgContent->orderBy(['msgtime' => SORT_DESC])
						->limit($size)
						->all();
					if (!empty($msgContent)) {
						/** @var WorkMsgAuditInfo $msgInfo */
						foreach ($msgContent as $msgInfo) {
							$dumpData = $msgInfo->dumpData(true, true, true);
							if (!empty($dumpData['info'])) {
								array_push($content, $dumpData);
							}
						}
					}

					break;
				default:

					break;
			}
			//文本搜索词高亮
            foreach ($content as $key => $info) {
                if ($searchName !== '') {
                    if (!empty($info['info']) && !empty($info['info']['content'])) {
                        $content[$key]['info']['content'] = str_replace($searchName, "<span style='color: #1890FF;'>" . $searchName . "</span>", $info['info']['content']);
                    }
                }
                if(isset($info['info']['image_url']) && empty($info['info']['image_url'])) {
                    $content[$key]['info']['image_url'] = Yii::$app->params['site_url'].'/images/url.png';
                }
            }

			return $content;
		}

		public static function getMsgVoiceContent ($postData = [])
		{
			$content = [];

			$corpId    = !empty($postData['corp_id']) ? $postData['corp_id'] : '';
			$auditId   = !empty($postData['audit_id']) ? $postData['audit_id'] : '';
			$userId    = !empty($postData['user_id']) ? $postData['user_id'] : '';
			$lastTime  = !empty($postData['last_time']) ? $postData['last_time'] : '';
			$msgSize   = !empty($postData['msg_size']) ? $postData['msg_size'] : '25';
			$startDate = !empty($postData['start_date']) ? $postData['start_date'] : '';
			$endDate   = !empty($postData['end_date']) ? $postData['end_date'] : '';
			if (empty($corpId) || empty($auditId)) {
				return $content;
			}

			$msgContent = self::find()->alias('ai');
			$msgContent = $msgContent->innerJoin('{{%work_msg_audit_info_meeting_voice_call}} mvc', 'ai.id = mvc.audit_info_id');
			$msgContent = $msgContent->where(['ai.audit_id' => $auditId, 'ai.msgtype' => 'meeting_voice_call']);

			//搜索人
			if (!empty($userId)) {
				$workUser = WorkUser::findOne($userId);
				if (empty($workUser)) {
					return $content;
				}
				$msgContent = $msgContent->andWhere(['or', ['ai.from' => $workUser->userid], 'find_in_set("' . $workUser->userid . '",ai.tolist)']);
			}

			//时间范围
			if (!empty($startDate) && !empty($endDate)) {
				$startTime  = strtotime($startDate);
				$endTime    = strtotime($endDate . ' 23:59:59');
				$startTime  = $startTime * 1000;
				$endTime    = $endTime * 1000;
				$msgContent = $msgContent->andWhere(['between', 'ai.msgtime', $startTime, $endTime]);
			}

			if (!empty($lastTime)) {
				$msgContent = $msgContent->andWhere(['<', 'ai.msgtime', $lastTime]);
			}

			$msgContent = $msgContent->orderBy(['ai.msgtime' => SORT_DESC])
				->limit($msgSize)
				->all();
			if (!empty($msgContent)) {
				/** @var WorkMsgAuditInfo $msgInfo */
				foreach ($msgContent as $msgInfo) {
					array_push($content, $msgInfo->dumpVoiceData());
				}
			}

			return $content;
		}

		//获取音频存档信息
		public function dumpVoiceData ()
		{
			$data = [
				'id'        => $this->id,
				'msgtime'   => $this->msgtime,
				'from_name' => '未知',
			];

			if ($this->from_type == self::IS_WORK_USER) {
				$data['from_name'] = $this->user->name;
			}

			if ($this->from_type == self::IS_EXTERNAL_USER) {
				if (!empty($this->external)) {
					$data['from_name'] = $this->external->name;
				}
			}

			return $data;
		}

		//拼凑日期格式
		public static function spellDate ($startTime, $endTime)
		{
			$week      = ['周日', '周一', '周二', '周三', '周四', '周五', '周六'];
			$nowYear   = date('Y');
			$startYear = date('Y', $startTime);
			$endYear   = date('Y', $endTime);
			$startDate = date('Y年m月d日', $startTime);
			$endDate   = date('Y年m月d日', $endTime);
			$startHour = date('H:i', $startTime);
			$endHour   = date('H:i', $endTime);

			if ($nowYear != $startYear || $nowYear != $endYear) {
				$startStr = $startDate;
				$endStr   = $endDate;
			} else {
				$startStr = date('m月d日', $startTime);
				$endStr   = date('m月d日', $endTime);
			}
			$startWeek    = $endWeek = '';
			$startWeekKey = date("w", $startTime);
			$endWeekKey   = date("w", $endTime);
			if (!empty($week[$startWeekKey])) {
				$startWeek = $week[$startWeekKey];
			}
			if (!empty($week[$endWeekKey])) {
				$endWeek = $week[$endWeekKey];
			}

			if ($startDate == $endDate) {
				$dateStr = $startStr . ' ' . $startWeek . ' ' . $startHour . ' - ' . $endHour;
			} else {
				$dateStr = $startStr . ' ' . $startWeek . ' ' . $startHour . ' - ' . $endStr . ' ' . $endWeek . ' ' . $endHour;
			}

			return $dateStr;
		}

		//补充客户id
		public static function updateExternalId ($corpId, $auditInfoId)
		{
			$externalId = 0;
			if (empty($corpId) || empty($auditInfoId)) {
				return $externalId;
			}
			$auditInfo = WorkMsgAuditInfo::findOne($auditInfoId);
			if (!empty($auditInfo)) {
				if ($auditInfo->from_type == 2) {
					if (($auditInfo->to_type == 1) && empty($auditInfo->external_id)) {
						$contactInfo = WorkExternalContact::findOne(['corp_id' => $corpId, 'external_userid' => $auditInfo->from]);
						if (!empty($contactInfo)) {
							$externalId             = $contactInfo->id;
							$auditInfo->external_id = $contactInfo->id;
							$auditInfo->update();
						}
					}
				} elseif ($auditInfo->to_type == 2) {
					if (($auditInfo->from_type == 1) && empty($auditInfo->to_external_id)) {
						$contactInfo = WorkExternalContact::findOne(['corp_id' => $corpId, 'external_userid' => $auditInfo->tolist]);
						if (!empty($contactInfo)) {
							$externalId                = $contactInfo->id;
							$auditInfo->to_external_id = $contactInfo->id;
							$auditInfo->update();
						}
					}
				}
			}

			return $externalId;
		}
	}
