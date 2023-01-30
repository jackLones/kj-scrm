<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\components\InvalidParameterException;
	use app\queue\WorkExternalChatJob;
	use app\util\DateUtil;
	use app\util\SUtils;
	use app\util\WorkUtils;
	use dovechen\yii2\weWork\Work;
	use Yii;
	use yii\db\Expression;

	/**
	 * This is the model class for table "{{%work_chat}}".
	 *
	 * @property int                   $id
	 * @property int                   $corp_id      企业ID
	 * @property string                $chat_id      客户群ID
	 * @property string                $name         群名
	 * @property int                   $owner_id     群主用户ID
	 * @property string                $owner        群主ID
	 * @property string                $create_time  群的创建时间
	 * @property string                $notice       群公告
	 * @property int                   $group_id     所属分组id
	 * @property int                   $status       客户群状态 0-正常 1-跟进人离职 2-离职继承中 3-离职继承完成
	 * @property int                   $group_chat   群组类型：0：外部；1：内部
	 * @property int                   $follow_id    跟进状态ID
	 * @property int                   $update_time  最后一次跟进状态时间
	 * @property string                $remark       备注
	 * @property string                $des          描述
	 * @property string                $close_rate   预计成交率
	 * @property string                $follow_num   跟进次数
	 *
	 * @property AttachmentStatistic[] $attachmentStatistics
	 * @property WorkUser              $user
	 * @property WorkCorp              $corp
	 * @property WorkChatInfo[]        $workChatInfos
	 * @property WorkMsgAuditAgree[]   $workMsgAuditAgrees
	 * @property WorkMsgAuditInfo[]    $workMsgAuditInfos
	 */
	class WorkChat extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_chat}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_id', 'owner_id', 'group_id', 'status', 'update_time', 'close_rate', 'follow_num', 'follow_id'], 'integer'],
				[['chat_id'], 'required'],
				[['chat_id', 'name', 'owner'], 'string', 'max' => 64],
				[['notice', 'remark', 'des'], 'string'],
				[['owner_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkUser::className(), 'targetAttribute' => ['owner_id' => 'id']],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => Yii::t('app', 'ID'),
				'corp_id'     => Yii::t('app', '企业ID'),
				'chat_id'     => Yii::t('app', '客户群ID'),
				'name'        => Yii::t('app', '群名'),
				'owner_id'    => Yii::t('app', '群主用户ID'),
				'owner'       => Yii::t('app', '群主ID'),
				'create_time' => Yii::t('app', '群的创建时间'),
				'notice'      => Yii::t('app', '群公告'),
				'group_id'    => Yii::t('app', '所属分组id'),
				'status'      => Yii::t('app', '群公告'),
				'group_chat'  => Yii::t('app', '群组类型：0：外部；1：内部'),
				'follow_id'   => Yii::t('app', '跟进状态ID'),
				'update_time' => Yii::t('app', '最后一次跟进状态时间'),
				'remark'      => Yii::t('app', '备注'),
				'des'         => Yii::t('app', '描述'),
				'close_rate'  => Yii::t('app', '预计成交率'),
				'follow_num'  => Yii::t('app', '跟进次数'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getAttachmentStatistics ()
		{
			return $this->hasMany(AttachmentStatistic::className(), ['chat_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getUser ()
		{
			return $this->hasOne(WorkUser::className(), ['id' => 'owner_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getCorp ()
		{
			return $this->hasOne(WorkCorp::className(), ['id' => 'corp_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkChatInfos ()
		{
			return $this->hasMany(WorkChatInfo::className(), ['chat_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditAgrees ()
		{
			return $this->hasMany(WorkMsgAuditAgree::className(), ['chat_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfos ()
		{
			return $this->hasMany(WorkMsgAuditInfo::className(), ['chat_id' => 'id']);

		}

		/**
		 * @return object|\yii\db\Connection|null
		 *
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function getDb ()
		{
			return Yii::$app->get('mdb');
		}

		/**
		 * @param bool $withInfo
		 * @param bool $withUser
		 * @param bool $withExternal
		 * @param bool $withCorp
		 *
		 * @return array
		 *
		 */
		public function dumpData ($withInfo = false, $withUser = false, $withExternal = false, $withCorp = false)
		{
			$data = [
				'id'          => $this->id,
				'corp_id'     => $this->corp_id,
				'chat_id'     => $this->chat_id,
				'name'        => $this->name,
				'owner_id'    => $this->owner_id,
				'owner'       => $this->owner,
				'create_time' => $this->create_time,
				'notice'      => $this->notice,
				'group_id'    => $this->group_id,
				'status'      => $this->status,
			];

			if ($withCorp) {
				$data['corp'] = $this->corp->dumpData();
			}

			if ($withInfo) {
				$data['chat_info'] = [];

				foreach ($this->workChatInfos as $chatInfo) {
					array_push($data['chat_info'], $chatInfo->dumpData($withUser, $withExternal));
				}
			}

			return $data;
		}

		/**
		 * @param       $corpId
		 * @param int   $offset
		 * @param int   $limit
		 * @param int   $statusFilter
		 * @param array $ownerFilter
		 *
		 * @return array
		 *
		 * @throws InvalidDataException
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \Throwable
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function getChatList ($corpId, $offset = 0, $limit = 100, $statusFilter = 0, $ownerFilter = [])
		{
			$authCorp = WorkCorp::findOne($corpId);

			if (empty($authCorp)) {
				throw new InvalidDataException('参数不正确。');
			}

			/** @var Work $workApi */
			$workApi  = WorkUtils::getWorkApi($corpId, WorkUtils::EXTERNAL_API);
			$complete = true;
			if (!empty($workApi)) {
				try {
					$groupChatListData = $workApi->ECGroupChatList($offset, $limit, $statusFilter, $ownerFilter);
					if (!empty($groupChatListData['group_chat_list'])) {
						$complete = false;

						foreach ($groupChatListData['group_chat_list'] as $groupChat) {
							Yii::$app->work->push(new WorkExternalChatJob([
								'corp_id' => $authCorp->corpid,
								'chat_id' => $groupChat['chat_id'],
								'from'    => 2
							]));
//							static::getChatInfo($corpId, $groupChat['chat_id']);
						}
					}
				} catch (\Exception $e) {
					\Yii::error($e->getMessage(), __CLASS__ . '-' . __FUNCTION__ . ':ECGroupChatGet');
				}
			}

			return [
				'offset'   => $offset,
				'complete' => $complete,
			];
		}

		/**
		 * @param $corpId
		 * @param $chatId
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \Throwable
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function getChatInfo ($corpId, $chatId)
		{
			$authCorp = WorkCorp::findOne($corpId);

			if (empty($authCorp)) {
				throw new InvalidDataException('参数不正确。');
			}

			/** @var Work $workApi */
			$workApi    = WorkUtils::getWorkApi($corpId, WorkUtils::EXTERNAL_API);
			$workChatId = 0;

			if (!empty($workApi)) {
				try {
					$chat          = $workApi->ECGroupChatGet($chatId);
					$groupChatInfo = SUtils::Object2Array($chat);

					$workChatId = static::setChat($corpId, $groupChatInfo);
				} catch (\Exception $e) {
					\Yii::error($e->getMessage(), __CLASS__ . '-' . __FUNCTION__ . ':ECGroupChatGet');
					try {
						$workChatId = WorkChat::getUserChatInfo($corpId, $chatId);
					} catch (\Exception $e) {
						\Yii::error($e->getMessage(), 'getUserChatInfo' . $chatId);
					}
				}
			}

			return $workChatId;
		}

		/**
		 * 获取企业微信内部群数据
		 *
		 * @param $corpId
		 * @param $chatId
		 *
		 * @return  int
		 *
		 * @throws InvalidDataException
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function getUserChatInfo ($corpId, $chatId)
		{
			$authCorp = WorkCorp::findOne($corpId);

			if (empty($authCorp)) {
				throw new InvalidDataException('参数不正确。');
			}
			/** @var Work $workApi */
			$workApi    = WorkUtils::getMsgAuditApi($corpId);
			$workChatId = 0;
			if (!empty($workApi)) {
				try {
					$chat = $workApi->GroupChatGet($chatId);
					if (isset($chat['errcode']) && $chat['errcode'] == 0) {
						$workChatId = static::setUserChat($corpId, $chatId, $chat);
					}
				} catch (\Exception $e) {
					\Yii::error($e->getMessage(), __CLASS__ . '-' . __FUNCTION__ . ':GroupChatGet');
				}
			}

			return $workChatId;
		}

		/**
		 * @param $corpId
		 * @param $chat
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 * @throws InvalidParameterException
		 * @throws \Throwable
		 */
		public static function setChat ($corpId, $chat)
		{
			if (empty($corpId) || empty($chat) || empty($chat['chat_id'])) {
				throw new InvalidParameterException("参数不正确");
			}

			$workChat = static::findOne(['corp_id' => $corpId, 'chat_id' => $chat['chat_id']]);
			//离职群分配
			if (!empty($workChat)) {
				if ($chat['owner'] != $workChat->owner) {
					$workUserId = WorkUser::getUserId($corpId, $chat['owner']);
					$user       = WorkUser::findOne(['userid' => $workChat->owner, 'corp_id' => $corpId]);
					if (!empty($user) && $user->is_del == WorkUser::USER_IS_DEL) {
						$dismissDetail = WorkDismissUserDetail::findOne(['user_id' => $user->id, 'chat_id' => $workChat->id]);
						if (!empty($dismissDetail)) {
							$dismissDetail->allocate_time    = time();
							$dismissDetail->allocate_user_id = $workUserId;
							$dismissDetail->status           = WorkDismissUserDetail::IS_ASSIGN;
							$dismissDetail->save();
						}
					}
				}
			}

			$isAdd = 0;
			if (empty($workChat)) {
				$isAdd             = 1;
				$workChat          = new WorkChat();
				$workChat->corp_id = $corpId;
				$workChat->chat_id = $chat['chat_id'];
			} else {
				$oldChatId     = $workChat->id;
				$oldChatName   = $workChat->name;
				$oldChatNotice = $workChat->notice;
			}

			if (!empty($chat['name'])) {
				$workChat->name = $chat['name'];
			}

			if (!empty($chat['owner'])) {
				$oldOwner   = !empty($workChat) ? $workChat->owner : 0;
				$oldOwnerId = !empty($workChat) ? $workChat->owner_id : 0;
				$workUserId = WorkUser::getUserId($corpId, $chat['owner']);

				if (!empty($workUserId)) {
					$workChat->owner_id = $workUserId;
				}
				$workChat->owner = $chat['owner'];

				if ($isAdd == 1 && empty($workUserId)){
					$externalContact = WorkExternalContact::findOne(['corp_id' => $corpId, 'external_userid' => $chat['owner']]);
					if (empty($externalContact)){
						return true;//群主非员工、非外部联系人的群不同步
					}
				}

			}

			if (empty($workChat->follow_id)) {
				$relation = UserCorpRelation::findOne(['corp_id' => $corpId]);
				if (!empty($relation)) {
					$follow = Follow::findOne(['uid' => $relation->uid, 'status' => 1]);
					if (!empty($follow)) {
						$workChat->follow_id = $follow->id;
					}
				}

			}

			if (!empty($chat['create_time'])) {
				$workChat->create_time = $chat['create_time'];
			}
			$workChat->group_chat = 0;
			$workChat->notice = !empty($chat['notice']) ? $chat['notice'] : '';

			if ($workChat->dirtyAttributes) {
				if (!$workChat->validate() || !$workChat->save()) {
					throw new InvalidDataException(SUtils::modelError($workChat));
				}
			}

			//群名称
			if (!empty($workChat->name)) {
				$workChatName = $workChat->name;
			} else {
				if (!empty($chat['member_list'])) {
					$nameArr = [];
					foreach ($chat['member_list'] as $memberInfo) {
						if ($memberInfo['type'] == 1) {
							$userInfo = WorkUser::findOne(['corp_id' => $corpId, 'userid' => $memberInfo['userid']]);
							if (!empty($userInfo)) {
								array_push($nameArr, $userInfo->name);
							} else {
								array_push($nameArr, $memberInfo['userid']);
							}
						} elseif ($memberInfo['type'] == 2) {
							$contactInfo = WorkExternalContact::findOne(['corp_id' => $corpId, 'external_userid' => $memberInfo['userid']]);
							if (!empty($contactInfo)) {
								array_push($nameArr, $contactInfo->name);
							} else {
								array_push($nameArr, $memberInfo['userid']);
							}
						}
					}
					$workChatName = implode('、', $nameArr);
				} else {
					$workChatName = '';
				}
			}

			//群创建变更互动轨迹
			$isAdd  = 0;
			$remark = '';
			if (empty($oldOwnerId)) {//创建群
				$userInfo  = WorkUser::findOne($workChat->owner_id);
				$ownerName = !empty($userInfo) ? '【' . $userInfo->name . '】' : '';
				if (!empty($workChatName)) {
					$remark = '群主' . $ownerName . '创建群【' . $workChatName . '】';
				} else {
					$remark = '群主' . $ownerName . '创建群';
				}
				$isAdd     = 1;
				$eventId   = 1;
				$eventTime = $workChat->create_time;
			} elseif (!empty($workUserId) && $oldOwnerId != $workUserId) {//更换群主
				$oldUserInfo  = WorkUser::findOne($oldOwnerId);
				$userInfo     = WorkUser::findOne($workUserId);
				$oldOwnerName = !empty($oldUserInfo) ? $oldUserInfo->name : $oldOwner;
				$ownerName    = !empty($userInfo) ? $userInfo->name : $workChat->owner;
				$remark       = '群主【' . $oldOwnerName . '】变更为【' . $ownerName . '】成为新群主';
				$isAdd        = 1;
				$eventId      = 8;
				$eventTime    = '';
			} else {
				$whereData    = ['event' => 'chat_track', 'related_id' => $workChat->id, 'user_id' => $workChat->owner_id, 'event_id' => [1, 8]];
				$chatTimeLine = ExternalTimeLine::find()->where($whereData)->orderBy(['id' => SORT_DESC])->one();
				if (empty($chatTimeLine)) {
					$isAdd     = 1;
					$eventId   = 1;
					$eventTime = $workChat->create_time;
					$userInfo  = WorkUser::findOne($workChat->owner_id);
					$ownerName = !empty($userInfo) ? '【' . $userInfo->name . '】' : '';
					if (!empty($workChatName)) {
						$remark = '群主' . $ownerName . '创建群【' . $workChatName . '】';
					} else {
						$remark = '群主' . $ownerName . '创建群';
					}
				}
			}
			if (!empty($isAdd)) {
				ExternalTimeLine::addExternalTimeLine(['uid' => 0, 'user_id' => $workChat->owner_id, 'event' => 'chat_track', 'related_id' => $workChat->id, 'event_id' => $eventId, 'event_time' => $eventTime, 'remark' => $remark]);
			}

			//更改群名群公告轨迹
			if (!empty($oldChatId)) {
				//群名
				$remark = '';
				if (!empty($oldChatName)) {
					if ($oldChatName != $workChat->name) {
						$remark = '群名称由【' . $oldChatName . '】变更为【' . $workChat->name . '】';
					}
				} elseif (!empty($workChat->name)) {
					$remark = '群名称变更为【' . $workChat->name . '】';
				}
				if (!empty($remark)) {
					ExternalTimeLine::addExternalTimeLine(['uid' => 0, 'event' => 'chat_track', 'related_id' => $workChat->id, 'event_id' => 6, 'remark' => $remark]);
				}
				//群公告
				$remarkNotice = '';
				if (!empty($oldChatNotice)) {
					if ($oldChatNotice != $workChat->notice) {
						if (!empty($workChat->notice)) {
							$remarkNotice = "群公告由\n【" . $oldChatNotice . "】\n变更为\n【" . $workChat->notice . '】';
						} else {
							$remarkNotice = "群公告由\n【" . $oldChatNotice . "】\n变更为空";
						}
					}
				} elseif (!empty($workChat->notice)) {
					$remarkNotice = "群公告变更为\n【" . $workChat->notice . '】';
				}
				if (!empty($remarkNotice)) {
					ExternalTimeLine::addExternalTimeLine(['uid' => 0, 'event' => 'chat_track', 'related_id' => $workChat->id, 'event_id' => 7, 'remark' => $remarkNotice]);
				}
			}

			if (!empty($chat['member_list'])) {
				$oldChatInfo     = WorkChatInfo::find()->where(['chat_id' => $workChat->id, 'status' => WorkChatInfo::NORMAL_MEMBER])->select('id,user_id,userid,external_id,type')->asArray()->all();
				$oldChatInfoData = [];
				if (!empty($oldChatInfo)) {
					foreach ($oldChatInfo as $oldInfo) {
						$oldChatInfoData[$oldInfo['id']] = $oldInfo;
					}
				}

				WorkChatInfo::updateAll(['status' => WorkChatInfo::LEAVE_MEMBER, 'leave_time' => time()], ['chat_id' => $workChat->id, 'status' => WorkChatInfo::NORMAL_MEMBER]);

				foreach ($chat['member_list'] as $member) {
					try {
						$workChatInfoId = WorkChatInfo::add($corpId, $workChat->id, $member, ['owner_id' => $workChat->owner_id, 'chat_name' => $workChatName]);
						if (isset($oldChatInfoData[$workChatInfoId])) {
							unset($oldChatInfoData[$workChatInfoId]);
						}
						\Yii::error($workChatInfoId, '$workChatInfoId');
					} catch (\Exception $e) {
						\Yii::error($member, __CLASS__ . '-' . __FUNCTION__ . ':setChatMemberList');
						\Yii::error($e->getMessage(), __CLASS__ . '-' . __FUNCTION__ . ':setChatMemberList');
					}
				}
				\Yii::error($oldChatInfoData, 'oldChatInfoData');
				//群离开轨迹
				if (!empty($oldChatInfoData)) {
					foreach ($oldChatInfoData as $chatInfo) {
						$addData = ['uid' => 0, 'event' => 'chat_track', 'event_id' => 3, 'related_id' => $workChat->id];
						$remark  = '';
						if ($chatInfo['type'] == 1) {
							$addData['user_id'] = $chatInfo['user_id'];
							$userInfo           = WorkUser::findOne($chatInfo['user_id']);
							if (!empty($userInfo)) {
								$remark .= '成员【' . $userInfo->name . '】';
							} else {
								$remark .= '成员【' . $chatInfo['userid'] . '】';
							}
						} elseif ($chatInfo['type'] == 2) {
							if (!empty($chatInfo['external_id'])) {
								$addData['external_id'] = $chatInfo['external_id'];
								$contactInfo            = WorkExternalContact::findOne($chatInfo['external_id']);
								$remark                 .= !empty($contactInfo) ? '客户【' . $contactInfo->name . '】' : '客户';;
							} else {
								$addData['openid'] = $chatInfo['userid'];
								$remark            .= '未知客户【' . $chatInfo['userid'] . '】';
							}
						}
						if (!empty($remark)) {
							$chatName          = !empty($workChatName) ? '【' . $workChatName . '】' : '';
							$remark            .= '退群' . $chatName;
							$addData['remark'] = $remark;
							ExternalTimeLine::addExternalTimeLine($addData);
						}
					}
				}
			} else {
				WorkChatInfo::updateAll(['status' => WorkChatInfo::LEAVE_MEMBER, 'leave_time' => time()], ['chat_id' => $workChat->id, 'status' => WorkChatInfo::NORMAL_MEMBER]);
				$workChat->status = 4;
				$workChat->update();
				//轨迹
				$whereData    = ['uid' => 0, 'event' => 'chat_track', 'related_id' => $workChat->id, 'event_id' => 9];
				$chatTimeLine = ExternalTimeLine::find()->where($whereData)->orderBy(['id' => SORT_DESC])->one();
				if (empty($chatTimeLine)) {
					ExternalTimeLine::addExternalTimeLine(['uid' => 0, 'event' => 'chat_track', 'related_id' => $workChat->id, 'event_id' => 9, 'remark' => '群已解散']);
				}
			}

			//更新会话存档中的群id
			WorkMsgAuditInfo::updateAll(['chat_id' => $workChat->id], ['roomid' => $workChat->chat_id, 'chat_id' => NULL]);

			return $workChat->id;
		}

		/**
		 * 添加内部群
		 *
		 * @param $corpId
		 * @param $chatId
		 * @param $chat
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 * @throws InvalidParameterException
		 */
		public static function setUserChat ($corpId, $chatId, $chat)
		{
			if (empty($corpId) || empty($chat) || empty($chatId)) {
				throw new InvalidParameterException("参数不正确");
			}
			$workChat = static::findOne(['corp_id' => $corpId, 'chat_id' => $chatId]);
			if (empty($workChat)) {
				$workChat             = new WorkChat();
				$workChat->corp_id    = $corpId;
				$workChat->chat_id    = $chatId;
				$workChat->group_chat = 1;
				if (!empty($chat['room_create_time'])) {
					$workChat->create_time = $chat['room_create_time'];
				}
			}
			$workChat->name = isset($chat['roomname']) ? $chat['roomname'] : '';

			if (empty($workChat->follow_id)) {
				$relation = UserCorpRelation::findOne(['corp_id' => $corpId]);
				if (!empty($relation)) {
					$follow = Follow::findOne(['uid' => $relation->uid, 'status' => 1]);
					if (!empty($follow)) {
						$workChat->follow_id = $follow->id;
					}
				}
			}

			if (!empty($chat['creator'])) {
				$workUserId = WorkUser::getUserId($corpId, $chat['creator']);

				if (!empty($workUserId)) {
					$workChat->owner_id = $workUserId;
				}
				$workChat->owner = $chat['creator'];
			}

			$workChat->notice = !empty($chat['notice']) ? $chat['notice'] : '';

			if ($workChat->dirtyAttributes) {
				if (!$workChat->validate() || !$workChat->save()) {
					throw new InvalidDataException(SUtils::modelError($workChat));
				}
			}
			//群成员
			if (!empty($chat['members'])) {
				WorkChatInfo::updateAll(['status' => WorkChatInfo::LEAVE_MEMBER, 'leave_time' => time()], ['chat_id' => $workChat->id, 'status' => WorkChatInfo::NORMAL_MEMBER]);
				foreach ($chat['members'] as $member) {
					try {
						$workChatInfoId = WorkChatInfo::addUser($corpId, $workChat->id, $member);
						\Yii::error($workChatInfoId, '$workChatInfoId');
					} catch (\Exception $e) {
						\Yii::error($member, __CLASS__ . '-' . __FUNCTION__ . ':setChatMemberList');
						\Yii::error($e->getMessage(), __CLASS__ . '-' . __FUNCTION__ . ':setChatMemberList');
					}
				}
			} else {
				WorkChatInfo::updateAll(['status' => WorkChatInfo::LEAVE_MEMBER, 'leave_time' => time()], ['chat_id' => $workChat->id, 'status' => WorkChatInfo::NORMAL_MEMBER]);
				$workChat->status = 4;
				$workChat->update();
			}

			//更新会话存档中的群id
			WorkMsgAuditInfo::updateAll(['chat_id' => $workChat->id], ['roomid' => $workChat->chat_id, 'chat_id' => NULL]);

			return $workChat->id;
		}

		/**
		 * @param $corpId
		 * @param $chatId
		 * @param $userId
		 *
		 * @return int
		 *
		 * @throws \Throwable
		 */
		public static function getChatId ($corpId, $chatId, $userId = '')
		{
			$workChatId = 0;
			$workChat   = WorkChat::findOne(['corp_id' => $corpId, 'chat_id' => $chatId]);
			if (empty($workChat)) {
				try {
					$workChatId = WorkChat::getChatInfo($corpId, $chatId);
				} catch (\Exception $e) {
					\Yii::error($e->getMessage(), __CLASS__ . '-' . __FUNCTION__);
				}
			} else {
				$workChatId = $workChat->id;
				//针对内部群做数据更新
				if (!empty($userId) && $workChat->group_chat == 1) {
					$workChatInfo = WorkChatInfo::findOne(['chat_id' => $workChatId, 'user_id' => $userId]);
					if (empty($workChatInfo)) {
						try {
							$workChatId = WorkChat::getUserChatInfo($corpId, $chatId);
						} catch (\Exception $e) {
							\Yii::error($e->getMessage(), 'getUserChatInfo' . $chatId);
						}
					} elseif ($workChatInfo->status == 0) {
						$workChatInfo->status     = 1;
						$workChatInfo->leave_time = 0;
						$workChatInfo->update();
					}
				}
			}

			return $workChatId;
		}

		/**
		 * 更新群信息
		 *
		 * @param $corpId
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function updateChatNotice ($corpId)
		{
			$workChat = static::find()->andWhere(['corp_id' => $corpId])->all();

			if (empty($workChat)) {
				throw new InvalidDataException('当前无客户群');
			}

			/** @var Work $workApi */
			$workApi = WorkUtils::getWorkApi($corpId, WorkUtils::EXTERNAL_API);

			if (!empty($workApi)) {
				try {
					foreach ($workChat as $chatInfo) {
						$chat          = $workApi->ECGroupChatGet($chatInfo->chat_id);
						$groupChatInfo = SUtils::Object2Array($chat);

						static::setChat($corpId, $groupChatInfo);
					}
				} catch (\Exception $e) {
					\Yii::error($e->getMessage(), __CLASS__ . '-' . __FUNCTION__ . ':ECGroupChatGet');
				}
			}

			return true;
		}

		/**
		 * 获取客户群
		 *
		 * @return boolean
		 *
		 * @throws InvalidDataException
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function getChatFirst ()
		{
			ini_set('memory_limit', '1024M');
			set_time_limit(0);
			//企业微信号
			$work_corp = WorkCorp::find()->select('id,corpid')->where('corpid != \'\' AND corp_type != \'\'')->asArray()->all();

			try {
				foreach ($work_corp as $k => $v) {
					try {
						static::getChatList($v['id'], 0, 500);
					} catch (\Exception $e) {
						Yii::error($e->getMessage(), 'work-corp-list');
					}
				}
			} catch (\Exception $e) {
				Yii::error($e->getMessage(), 'work-chat-getApi');
			}

			return true;
		}

		/**
		 * 获取客户群统计数据
		 *
		 * @param type  int 0每日统计 1首次统计
		 *
		 * @return boolean
		 *
		 * @throws InvalidDataException
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function getChatDayStatistic ($type = 0)
		{
			ini_set('memory_limit', '1024M');
			set_time_limit(0);
			//企业微信号
			$work_corp = WorkCorp::find()->select('id,corpid')->where('corpid != \'\' AND corp_type != \'\'')->asArray()->all();
			$etime     = strtotime(date('Y-m-d')) - 1;
			if ($type == 1) {
				$stime = $etime - 59 * 86400;
				$sdate = date('Y-m-d', $stime);
				$edate = date('Y-m-d', $etime);
			} else {
				$sdate = $edate = date('Y-m-d', $etime);
			}

			foreach ($work_corp as $k => $v) {
				try {
					$corpId  = $v['id'];
					$workApi = WorkUtils::getWorkApi($corpId, WorkUtils::EXTERNAL_API);

					if (!empty($workApi)) {
						$dayData = DateUtil::getDateFromRange($sdate, $edate);
						foreach ($dayData as $day) {
							$behavior                   = [];
							$behavior['day_begin_time'] = strtotime($day);
							$behavior['order_by']       = 2;
							$behavior['limit']          = 1000;

							$sData = $workApi->ECGroupChatStatistic($behavior);

							if ($sData['errcode'] == 0 && $sData['errmsg'] == 'ok') {
								foreach ($sData['items'] as $yData) {
									$owner    = $yData['owner'];
									$chatData = $yData['data'];
									if ($chatData['new_chat_cnt'] || $chatData['chat_total'] || $chatData['chat_has_msg'] || $chatData['new_member_cnt'] || $chatData['member_total'] || $chatData['member_has_msg'] || $chatData['msg_total']) {
										$workUser = WorkUser::findOne(['corp_id' => $corpId, 'userid' => $owner]);
										if (!empty($workUser)) {
											$owner_id = $workUser->id;
										} else {
											continue;
										}
										$workChatStatistic = WorkChatStatistic::findOne(['corp_id' => $corpId, 'owner_id' => $owner_id, 'time' => $behavior['day_begin_time']]);
										if (empty($workChatStatistic)) {
											$workChatStatistic           = new WorkChatStatistic();
											$workChatStatistic->corp_id  = $corpId;
											$workChatStatistic->owner_id = $owner_id;
											$workChatStatistic->owner    = $owner;
										}

										$workChatStatistic->new_chat_cnt   = $chatData['new_chat_cnt'];
										$workChatStatistic->chat_total     = $chatData['chat_total'];
										$workChatStatistic->chat_has_msg   = $chatData['chat_has_msg'];
										$workChatStatistic->new_member_cnt = $chatData['new_member_cnt'];
										$workChatStatistic->member_total   = $chatData['member_total'];
										$workChatStatistic->member_has_msg = $chatData['member_has_msg'];
										$workChatStatistic->msg_total      = $chatData['msg_total'];
										$workChatStatistic->time           = $behavior['day_begin_time'];
										$workChatStatistic->create_time    = time();

										if (!$workChatStatistic->save()) {
											\Yii::error(SUtils::modelError($workChatStatistic), 'workChatStatistic_error');
										}
									}
								}
							}
						}
					}
				} catch (\Exception $e) {
					Yii::error($e->getMessage(), 'work-chat-getApi');
				}
			}

			return true;
		}

		/**
		 * 根据群获取群top数据
		 * $corp_id
		 * $data_type 1新增群成员数2退群人数
		 * $s_date    开始时间
		 * $e_date    结束时间
		 * $group_id  群分组id
		 */
		public static function getChatTopByChat ($corp_id, $data_type, $s_date, $e_date, $group_id = 0, $user_ids)
		{
			$xData   = [];//X轴
			$newData = [];//Y轴数据
			$allData = [];//详细数据

			$workChat = static::find()->andWhere(['corp_id' => $corp_id]);
			if (is_array($user_ids)) {
				$workChat = $workChat->andWhere(['in', 'owner_id', $user_ids]);
			}
			if (!empty($group_id)) {
				$workChat = $workChat->andWhere(['group_id' => $group_id]);
			}
			$workChat = $workChat->asArray()->all();

			if (!empty($workChat)) {
				//群分组
				$groupInfo = [];
				if (!empty($group_id)) {
					$chatGroup                 = WorkChatGroup::findOne($group_id);
					$groupInfo[$chatGroup->id] = $chatGroup->group_name;
				} else {
					$chatGroup = WorkChatGroup::find()->andWhere(['corp_id' => $corp_id, 'status' => 1])->asArray()->all();
					foreach ($chatGroup as $v) {
						$groupInfo[$v['id']] = $v['group_name'];
					}
				}

				$chatIdInfo    = [];
				$chatNameInfo  = [];
				$chatGroupInfo = [];
				foreach ($workChat as $k => $v) {
					$chatIdInfo[]            = $v['id'];
					$chatNameInfo[$v['id']]  = static::getChatName($v['id']);
					$chatGroupInfo[$v['id']] = isset($groupInfo[$v['group_id']]) ? $groupInfo[$v['group_id']] : '';
				}

				$chatInfo = WorkChatInfo::find()->andWhere(['in', 'chat_id', $chatIdInfo]);
				if ($data_type == 1) {
					$chatInfo->andWhere(['status' => 1])->andFilterWhere(['between', 'join_time', strtotime($s_date), strtotime($e_date . ' 23:59:59')]);
				} else {
					$chatInfo->andWhere(['status' => 0])->andFilterWhere(['between', 'leave_time', strtotime($s_date), strtotime($e_date . ' 23:59:59')]);
				}

				$chatInfo = $chatInfo->select('chat_id, count(id) all_num')->groupBy('chat_id')->orderBy(['all_num' => SORT_DESC])->asArray()->all();

				//top10数据
				$chatData10 = array_slice($chatInfo, 0, 10);
				foreach ($chatData10 as $k => $v) {
					$chatName = isset($chatNameInfo[$v['chat_id']]) ? $chatNameInfo[$v['chat_id']] : '';
					array_push($xData, $chatName);
					array_push($newData, $v['all_num']);
				}
				//列表数据
				$sort = 1;
				foreach ($chatInfo as $k => $v) {
					if ($v['all_num'] > 0) {
						$allD               = [];
						$allD['sort']       = $sort;
						$allD['name']       = isset($chatNameInfo[$v['chat_id']]) ? $chatNameInfo[$v['chat_id']] : '';
						$allD['group_name'] = isset($chatGroupInfo[$v['chat_id']]) ? $chatGroupInfo[$v['chat_id']] : '';
						$allD['all_num']    = $v['all_num'];
						$allData[]          = $allD;
						$sort++;
					}
				}
			}

			$info               = [];
			$info['xData']      = $xData;
			$info['seriesData'] = $newData;
			$info['data']       = $allData;

			return $info;
		}

		/**
		 * 根据群分组获取群top数据
		 * $corp_id
		 * $data_type 1新增群成员数2退群人数
		 * $s_date    开始时间
		 * $e_date    结束时间
		 */
		public static function getChatTopByGroup ($corp_id, $data_type, $s_date, $e_date)
		{
			$xData   = [];//X轴
			$newData = [];//Y轴数据
			$allData = [];//详细数据

			$chatData = WorkChat::find()->alias('a');
			$chatData = $chatData->andWhere(['a.corp_id' => $corp_id, 'a.group_chat' => 0]);
			$chatData = $chatData->leftJoin('{{%work_chat_info}} b', '`b`.`chat_id` = `a`.`id`');
			if ($data_type == 1) {
				$chatData->andWhere(['b.`status`' => 1])->andFilterWhere(['between', 'b.`join_time`', strtotime($s_date), strtotime($e_date . ' 23:59:59')]);
			} else {
				$chatData->andWhere(['b.`status`' => 0])->andFilterWhere(['between', 'b.`leave_time`', strtotime($s_date), strtotime($e_date . ' 23:59:59')]);
			}
			$chatData = $chatData->select('a.`group_id`, count(b.`id`) all_num')->groupBy('a.`group_id`')->orderBy(['all_num' => SORT_DESC])->asArray()->all();

			if (!empty($chatData)) {
				//群信息
				$groupInfo = [];
				$chatGroup = WorkChatGroup::find()->andWhere(['corp_id' => $corp_id, 'status' => 1])->asArray()->all();
				foreach ($chatGroup as $v) {
					$groupInfo[$v['id']] = $v['group_name'];
				}

				//top10数据
				$chatData10 = array_slice($chatData, 0, 10);
				foreach ($chatData10 as $k => $v) {
					$groupName = isset($groupInfo[$v['group_id']]) ? $groupInfo[$v['group_id']] : '';
					array_push($xData, $groupName);
					array_push($newData, $v['all_num']);
				}
				//列表数据
				$sort = 1;
				foreach ($chatData as $k => $v) {
					if ($v['all_num'] > 0) {
						$allD               = [];
						$allD['sort']       = $sort;
						$allD['group_name'] = isset($groupInfo[$v['group_id']]) ? $groupInfo[$v['group_id']] : '';
						$allD['all_num']    = $v['all_num'];
						$allData[]          = $allD;
						$sort++;
					}
				}
			}

			$info               = [];
			$info['xData']      = $xData;
			$info['seriesData'] = $newData;
			$info['data']       = $allData;

			return $info;
		}

		/**
		 * 根据群主获取群top数据
		 * $corp_id
		 * $data_type 1新增群成员数2退群人数3群聊消息总数
		 * $s_date    开始时间
		 * $e_date    结束时间
		 * $user_ids  群主id集合
		 */
		public static function getChatTopByOwner ($corp_id, $data_type, $s_date, $e_date, $user_ids)
		{
			$xData   = [];//X轴
			$newData = [];//Y轴数据
			$allData = [];//详细数据

			if ($data_type == 1 || $data_type == 2) {
				$chatData = WorkChat::find()->alias('a');
				$chatData = $chatData->leftJoin('{{%work_chat_info}} b', '`b`.`chat_id` = `a`.`id`');
				$chatData = $chatData->andWhere(['a.corp_id' => $corp_id, 'a.group_chat' => 0]);
				if (!empty($user_ids) && is_array($user_ids)) {
					$chatData = $chatData->andWhere(['in', '`a`.`owner_id`', $user_ids]);
				}
				if ($data_type == 1) {
					$chatData->andWhere(['b.`status`' => 1])->andFilterWhere(['between', 'b.`join_time`', strtotime($s_date), strtotime($e_date . ' 23:59:59')]);
				} else {
					$chatData->andWhere(['b.`status`' => 0])->andFilterWhere(['between', 'b.`leave_time`', strtotime($s_date), strtotime($e_date . ' 23:59:59')]);
				}
				$chatData = $chatData->select('a.`owner_id`, count(b.`id`) all_num')->groupBy('a.`owner_id`')->orderBy(['all_num' => SORT_DESC])->asArray()->all();
			} else {
				$stime    = strtotime($s_date);
				$etime    = strtotime($e_date);
				$field    = '`owner_id`, SUM(`msg_total`) all_num';
				$chatData = WorkChatStatistic::find()->where(['corp_id' => $corp_id])->andFilterWhere(['between', '`time`', $stime, $etime]);
				if (!empty($user_ids) && is_array($user_ids)) {
					$chatData = $chatData->andWhere(['in', 'owner_id', $user_ids]);
				}
				$chatData = $chatData->select($field)->groupBy('`owner_id`')->orderBy(['all_num' => SORT_DESC])->asArray()->all();
			}

			if (!empty($chatData)) {
				//群主信息
				$ownerInfo = [];
				$chatOwner = WorkUser::find();
				if (!empty($user_ids) && is_array($user_ids)) {
					$chatOwner = $chatOwner->andWhere(['in', 'id', $user_ids]);
				}
				$chatOwner = $chatOwner->asArray()->all();
				foreach ($chatOwner as $v) {
					$ownerInfo[$v['id']] = $v['name'];
				}

				//top10数据
				$chatData10 = array_slice($chatData, 0, 10);
				foreach ($chatData10 as $k => $v) {
					$ownerName = isset($ownerInfo[$v['owner_id']]) ? $ownerInfo[$v['owner_id']] : '';
					array_push($xData, $ownerName);
					array_push($newData, $v['all_num']);
				}
				unset($chatData10);
				//列表数据
				$sort = 1;
				foreach ($chatData as $k => $v) {
					if ($v['all_num'] > 0) {
						$allD            = [];
						$allD['sort']    = $sort;
						$allD['name']    = isset($ownerInfo[$v['owner_id']]) ? $ownerInfo[$v['owner_id']] : '';
						$allD['all_num'] = $v['all_num'];
						$allData[]       = $allD;
						$sort++;
					}
				}
				unset($ownerInfo);
				unset($chatData);
			}

			$info               = [];
			$info['xData']      = $xData;
			$info['seriesData'] = $newData;
			$info['data']       = $allData;

			return $info;
		}

		/**
		 * 根据群/群主获取群单位时间内数据
		 * $corp_id
		 * $data_type 1新增群成员数2退群人数5群成员总数3群聊消息总数($user_ids不为空时)4新增群
		 * $s_date    开始时间
		 * $e_date    结束时间
		 * $group_id  群分组id
		 */
		public static function getChatIncrease ($corp_id, $data_type, $type, $s_date, $e_date, $s_week, $group_id = 0, $user_ids = [])
		{
			$xData   = [];//X轴
			$newData = [];//新增
			$addNum  = 0; //新增成员
			$result  = [];
			switch ($type) {
				case 1:
					//按天
					$data = DateUtil::getDateFromRange($s_date, $e_date);
					foreach ($data as $k => $v) {
						if ($data_type == 4) {
							$add_num = static::getChatNewAdd($corp_id, $v, $v, $user_ids);
						} else {
							if (!empty($user_ids) && $group_id == 0) {
								$add_num = static::getChatStatisticsByOwner($corp_id, $user_ids, $data_type, $v, $v);
							} else {
								$add_num = static::getChatStatisticsByChat($corp_id, $group_id, $data_type, $v, $v, $user_ids);
							}
						}
						$result[$k]['add_num'] = $add_num;
						$result[$k]['time']    = $v;
						array_push($newData, intval($add_num));
						$addNum += $add_num;
					}
					$xData = $data;
					break;
				case 2:
					//按周
					$data    = DateUtil::getDateFromRange($s_date, $e_date);
					$data    = DateUtil::getWeekFromRange($data);
					$s_date1 = $data['s_date'];
					$e_date1 = $data['e_date'];
					foreach ($s_date1 as $k => $v) {
						foreach ($e_date1 as $kk => $vv) {
							if ($k == $kk) {
								if ($s_week == 53) {
									$s_week = 1;
								}
								if ($data_type == 4) {
									$add_num = static::getChatNewAdd($corp_id, $v, $vv, $user_ids);
								} else {
									if (!empty($user_ids) && $group_id == 0) {
										$add_num = static::getChatStatisticsByOwner($corp_id, $user_ids, $data_type, $v, $vv);
									} else {
										$add_num = static::getChatStatisticsByChat($corp_id, $group_id, $data_type, $v, $vv, $user_ids);
									}
								}
								$result[$k]['add_num'] = $add_num;
								$result[$k]['time']    = $v . '~' . $vv . '(' . $s_week . '周)';
								array_push($xData, $result[$k]['time']);
								array_push($newData, intval($add_num));
								$addNum += $add_num;
								$s_week++;
							}
						}
					}
					break;
				case 3:
					//按月
					$date = DateUtil::getLastMonth();
					foreach ($date as $k => $v) {
						if ($data_type == 4) {
							$add_num = static::getChatNewAdd($corp_id, $v['firstday'], $v['lastday'], $user_ids);
						} else {
							if (!empty($user_ids) && $group_id == 0) {
								$add_num = static::getChatStatisticsByOwner($corp_id, $user_ids, $data_type, $v['firstday'], $v['lastday']);
							} else {
								$add_num = static::getChatStatisticsByChat($corp_id, $group_id, $data_type, $v['firstday'], $v['lastday'], $user_ids);
							}
						}
						$result[$k]['add_num'] = $add_num;
						$result[$k]['time']    = $v['time'];
						array_push($xData, $v['time']);
						array_push($newData, intval($add_num));
						$addNum += $add_num;
					}

					break;

			}

			switch ($data_type) {
				case 2:
					$name = '退群人数';
					break;
				case 3:
					$name = '群聊消息总数';
					break;
				case 4:
					$name = '新增群数';
					break;
				case 5:
					$name = '群成员总数';
					break;
				default:
				case 1:
					$name = '新增群成员数';
					break;
			}
			$info['addNum']     = $addNum;
			$info['data']       = $result;
			$info['xData']      = $xData;
			$seriesData         = [
				[
					'name'   => $name,
					'type'   => 'line',
					'smooth' => true,
					'data'   => $newData,
				]
			];
			$info['seriesData'] = $seriesData;

			return $info;
		}

		/**
		 * 根据群主获取群单位时间内数据
		 * $corp_id
		 * $data_type 1群聊总数2新增群聊数3有过消息的群聊数4群成员总数5新增群成员数6发过消息的群成员数7群聊消息总数
		 * $type    1按天2按周3按月
		 * $s_date    开始时间
		 * $e_date    结束时间
		 * $s_week    周
		 * $user_ids  群主id集合
		 */
		public static function getUserChatStatistic ($corp_id, $data_type, $type, $s_date, $e_date, $s_week, $user_ids)
		{
			$xData   = [];//X轴
			$newData = [];//新增
			$result  = [];
			switch ($type) {
				case 1:
					//按天
					$data = DateUtil::getDateFromRange($s_date, $e_date);
					foreach ($data as $k => $v) {
						$add_num               = WorkChatStatistic::getChatStatisticsByDataType($corp_id, $user_ids, $data_type, $v, $v);
						$result[$k]['add_num'] = $add_num;
						$result[$k]['time']    = $v;
						array_push($newData, intval($add_num));
					}
					$xData = $data;
					break;
				case 2:
					//按周
					$data    = DateUtil::getDateFromRange($s_date, $e_date);
					$data    = DateUtil::getWeekFromRange($data);
					$s_date1 = $data['s_date'];
					$e_date1 = $data['e_date'];
					foreach ($s_date1 as $k => $v) {
						foreach ($e_date1 as $kk => $vv) {
							if ($k == $kk) {
								if ($s_week == 53) {
									$s_week = 1;
								}
								$add_num               = WorkChatStatistic::getChatStatisticsByDataType($corp_id, $user_ids, $data_type, $v, $vv);
								$result[$k]['add_num'] = $add_num;
								$result[$k]['time']    = $v . '~' . $vv . '(' . $s_week . '周)';
								array_push($xData, $result[$k]['time']);
								array_push($newData, intval($add_num));
								$s_week++;
							}
						}
					}
					break;
				case 3:
					//按月
					$date = DateUtil::getLastMonth();
					foreach ($date as $k => $v) {
						$add_num               = WorkChatStatistic::getChatStatisticsByDataType($corp_id, $user_ids, $data_type, $v['firstday'], $v['lastday']);
						$result[$k]['add_num'] = $add_num;
						$result[$k]['time']    = $v['time'];
						array_push($xData, $v['time']);
						array_push($newData, intval($add_num));
					}

					break;

			}

			switch ($data_type) {
				case 2:
					$name = '新增群聊数';
					break;
				case 3:
					$name = '有过消息的群聊数';
					break;
				case 4:
					$name = '群成员总数';
					break;
				case 5:
					$name = '新增群成员数';
					break;
				case 6:
					$name = '发过消息的群成员数';
					break;
				case 7:
					$name = '群聊消息总数';
					break;
				case 8:
					$name = '退群人数';
					break;
				default:
				case 1:
					$name = '群聊总数';
					break;
			}
			$info['data']       = $result;
			$info['xData']      = $xData;
			$seriesData         = [
				[
					'name'   => $name,
					'type'   => 'line',
					'smooth' => true,
					'data'   => $newData,
				]
			];
			$info['seriesData'] = $seriesData;

			return $info;
		}

		//根据群获取群单位时间内数据
		private function getChatStatisticsByChat ($corp_id, $group_id, $data_type, $stime, $etime, $user_ids)
		{
			$chatData = WorkChat::find()->alias('a');
			$chatData = $chatData->andWhere(['a.corp_id' => $corp_id, 'a.group_chat' => 0]);
			if (!empty($group_id)) {
				$chatData = $chatData->andWhere(['a.group_id' => $group_id]);
			}
			if (!empty($user_ids)) {
				$chatData = $chatData->andWhere(["in", 'a.owner_id', $user_ids]);
			}
			$chatData = $chatData->leftJoin('{{%work_chat_info}} b', '`b`.`chat_id` = `a`.`id`');
			if ($data_type == 1) {
				$chatData->andWhere(['b.`status`' => 1])->andFilterWhere(['between', 'b.`join_time`', strtotime($stime), strtotime($etime . ' 23:59:59')]);
			} elseif ($data_type == 2) {
				$chatData->andWhere(['b.`status`' => 0])->andFilterWhere(['between', 'b.`leave_time`', strtotime($stime), strtotime($etime . ' 23:59:59')]);
			} elseif ($data_type == 5) {
				$chatData->andWhere(['or', ['and', ['b.`status`' => 1], ['<', 'b.join_time', strtotime($etime . ' 23:59:59')]], ['and', ['b.`status`' => 0], ['<', 'b.join_time', strtotime($etime . ' 23:59:59')], ['>', 'b.leave_time', strtotime($etime . ' 23:59:59')]]]);
			}

			$count = $chatData->select('b.`id`')->all();
			$count = count($count);

			return $count;
		}

		//根据群主获取群单位时间内数据
		private function getChatStatisticsByOwner ($corp_id, $user_ids, $data_type, $stime, $etime)
		{
			if ($data_type != 1 && $data_type != 3) {
				$chatData = WorkChat::find()->alias('a');
				$chatData = $chatData->andWhere(['a.corp_id' => $corp_id, 'a.group_chat' => 0]);
				if (is_array($user_ids) && !empty($user_ids)) {
					$chatData = $chatData->andWhere(['in', 'a.owner_id', $user_ids]);
				}
				$chatData = $chatData->leftJoin('{{%work_chat_info}} b', '`b`.`chat_id` = `a`.`id`');
				if ($data_type == 2) {
					$chatData->andWhere(['b.`status`' => 0])->andFilterWhere(['between', 'b.`leave_time`', strtotime($stime), strtotime($etime . ' 23:59:59')]);
				} elseif ($data_type == 5) {
					$chatData->andWhere(['or', ['and', ['b.`status`' => 1], ['<', 'b.join_time', strtotime($etime . ' 23:59:59')]], ['and', ['b.`status`' => 0], ['<', 'b.join_time', strtotime($etime . ' 23:59:59')], ['>', 'b.leave_time', strtotime($etime . ' 23:59:59')]]]);
				}

				$count = $chatData->select('b.`id`')->all();
				$count = count($count);
			} else {
				if ($data_type == 1) {
					$field = 'SUM(`new_member_cnt`) msg_total_num';
				} elseif ($data_type == 3) {
					$field = 'SUM(`msg_total`) msg_total_num';
				}

				$stime    = strtotime($stime);
				$etime    = strtotime($etime);
				$chatData = WorkChatStatistic::find()->where(['corp_id' => $corp_id])->andFilterWhere(['between', '`time`', $stime, $etime]);
				if (!empty($user_ids) && is_array($user_ids)) {
					$chatData = $chatData->andWhere(['in', 'owner_id', $user_ids]);
				}
				$chatData = $chatData->select($field)->asArray()->all();
				$count    = isset($chatData[0]['msg_total_num']) ? $chatData[0]['msg_total_num'] : 0;
			}

			return $count;
		}

		//根据单位时间内新增群数据
		private function getChatNewAdd ($corp_id, $stime, $etime, $user_ids)
		{
			$count = WorkChat::find()->andWhere(['corp_id' => $corp_id, 'group_chat' => 0])->andWhere(["owner_id" => $user_ids])->andFilterWhere(['between', 'create_time', strtotime($stime), strtotime($etime . ' 23:59:59')])->count();

			return $count;
		}

		//获取群名称（针对群名称为空的，取群成员名称当做群名称）
		public static function getChatName ($chat_id)
		{
			$workChat = static::findOne($chat_id);
			if (!empty($workChat['name'])) {
				$chatName = $workChat['name'];
			} else {
				$nameArr  = [];
				$chatInfo = WorkChatInfo::find()->alias('wci');
				$chatInfo = $chatInfo->leftJoin('{{%work_user}} wu', '`wci`.`user_id` = `wu`.`id`');
				$chatInfo = $chatInfo->leftJoin('{{%work_external_contact}} wec', '`wci`.`external_id` = `wec`.`id`');
				$chatInfo = $chatInfo->where(['wci.chat_id' => $chat_id, 'wci.status' => 1]);
				$chatInfo = $chatInfo->select('wu.name user_name,wec.name,wci.userid,wci.type')->asArray()->all();
				if (!empty($chatInfo)) {
					foreach ($chatInfo as $chat) {
						if ($chat['type'] == 1) {
							$name = rawurldecode($chat['user_name']);
						} else {
							$name = rawurldecode($chat['name']);
						}
						if (!empty($name)) {
							array_push($nameArr, $name);
						}
					}
				}
				$chatName = implode('、', $nameArr);
			}

			return $chatName;
		}

		//获取群内人员轨迹
		public static function getChatTrack ($chat_id, $page = 1, $pageSize = 15)
		{
			$page   = ($page > 0) ? $page : 1;
			$offset = ($page - 1) * $pageSize;
			$sql1   = 'select id,"" as attachment_id,"" as search,"" as open_time,"" as leave_time,"" as openid,user_id,external_id,"11" as type,event_time,event_id,remark from {{%external_time_line}} where related_id = ' . $chat_id . ' and event in (\'chat_track\', \'chat_track_money\')';
			$sql2   = 'select id,attachment_id,search,open_time,leave_time,openid,user_id,external_id,type,UNIX_TIMESTAMP(create_time) AS event_time,"" as event_id,"" as remark from {{%attachment_statistic}} where chat_id = ' . $chat_id;
			//总数
			$sqlCount  = 'select count(*) count from ((' . $sql1 . ') UNION ALL (' . $sql2 . ' )) con ';
			$LineCount = ExternalTimeLine::findBySql($sqlCount)->asArray()->all();
			$count     = !empty($LineCount) ? $LineCount[0]['count'] : 0;
			//列表
			$sql        = 'select * from ((' . $sql1 . ') UNION ALL (' . $sql2 . ') ) con order by event_time desc,id desc limit ' . $offset . ',' . $pageSize;
			$LineList   = ExternalTimeLine::findBySql($sql)->asArray()->all();
			$workChat   = WorkChat::findOne($chat_id);
			$returnData = static::returnData($LineList, $workChat);

			return ['count' => $count, 'list' => $returnData];
		}

		public static function returnData ($LineList, $workChat)
		{
			$returnData = [];
			foreach ($LineList as $info) {
				$infoData               = [];
				$icon                   = 19;
				$infoData['event_time'] = !empty($info['event_time']) ? date('Y-m-d H:i:s', $info['event_time']) : '';
				$type                   = $info['type'];
				if ($type == 11) {
					$eventId = $info['event_id'];
					switch ($eventId) {
						case 1://创建群
							$icon = 16;
							break;
						case 2://加入群
							$icon = 15;
							break;
						case 3://退出群
							$icon = 17;
							break;
						case 4://打标签
							$icon = 1;
							break;
						case 5://移出标签
							$icon = 2;
							break;
						case 6://修改群名
							$icon = 10;
							break;
						case 7://修改群公告
							$icon = 13;
							break;
						case 8://变更群主
							$icon = 14;
							break;
						case 10://群主发红包
						case 11://客户领红包
							$icon = 13;
							break;
						case 12://更新群画像
							$icon = 10;
							break;
						case 13://跟进记录
							$icon = 11;
							break;
					}
                    $name = "";
                    if (!empty($info['user_id'])) {
                        $workUser = WorkUser::findOne($info['user_id']);
                        !empty($workUser) && $name = $workUser->name;
                    }
                    $content = $eventId == 12 ? $name . '完善群信息：' : '';

                    $remark = json_decode($info['remark'],true);
                    if(!empty($info['remark']) && is_array($remark)){
                        array_walk($remark,function(&$val,$key){
                            if($val['key'] == 'image'){
                                $val['old_value'] = json_decode($val['old_value'],true);
                                $val['value'] = json_decode($val['value'],true);
                            }
                        });
                        $content = ['remark'=>$content,'info'=>$remark];
                    } else {
                        $content .= $info['remark'];
                    }
				} else {
					if ($type == 2) {
						$icon = 11;
					} elseif ($type == 3) {
						$icon = 18;
					}
					$attachment = Attachment::findOne($info['attachment_id']);
					if (empty($attachment)) {
						continue;
					}
					switch ($info['type']) {
						case AttachmentStatistic::ATTACHMENT_SEARCH:
							if (!empty($info['user_id'])) {
								$userInfo = WorkUser::findOne($info['user_id']);
								if ($workChat->owner_id == $userInfo->id) {
									$tempName = '群主【' . $userInfo->userid . '】';
								} else {
									$tempName = '群企业成员【' . $userInfo->userid . '】';
								}
								$content = "{$tempName}通过关键词 『{$info['search']}』检索到了{$attachment->getAttachmentTypeName()}素材『{$attachment->file_name}』";
							} elseif (!empty($info['external_id'])) {
								$externalInfo = WorkExternalContact::findOne($info['external_id']);
								$content      = "客户【{$externalInfo->name}】通过关键词 『{$info['search']}』检索到了{$attachment->getAttachmentTypeName()}素材『{$attachment->file_name}』";
							} else {
								$content = "未知用户【{$info['openid']}】通过关键词 『{$info['search']}』检索到了{$attachment->getAttachmentTypeName()}素材『{$attachment->file_name}』";
							}
							break;
						case AttachmentStatistic::ATTACHMENT_SEND:
							if (!empty($info['user_id'])) {
								$userInfo = WorkUser::findOne($info['user_id']);
								if ($workChat->owner_id == $userInfo->id) {
									$tempName = '群主【' . $userInfo->name . '】';
								} else {
									$tempName = '群企业成员【' . $userInfo->name . '】';
								}
								$content = "{$tempName}向群里发送了{$attachment->getAttachmentTypeName()}素材『{$attachment->file_name}』";
							} elseif (!empty($info['external_id'])) {
								$externalInfo = WorkExternalContact::findOne($info['external_id']);
								$content      = "客户【{$externalInfo->name}】发送了{$attachment->getAttachmentTypeName()}素材『{$attachment->file_name}』";
							} else {
								$content = "未知用户【{$info['openid']}】发送了{$attachment->getAttachmentTypeName()}素材『{$attachment->file_name}』";
							}
							break;
						case AttachmentStatistic::ATTACHMENT_OPEN:
							$leaveString = '';
							if (!empty($info['leave_time']) && !empty($info['open_time'])) {
								$times = RedPackJoin::sec2Time(DateUtil::dateDiffSeconds(strtotime($info['open_time']), strtotime($info['leave_time'])));
								if (!empty($times)) {
									$leaveString = " 停留了 " . $times;
								}
							}
							if (!empty($info['user_id'])) {
								$userInfo = WorkUser::findOne($info['user_id']);
								if ($workChat->owner_id == $userInfo->id) {
									$tempName = '群主【' . $userInfo->name . '】';
								} else {
									$tempName = '群企业成员【' . $userInfo->name . '】';
								}
								$content = "{$tempName} 打开了{$attachment->getAttachmentTypeName()}素材『{$attachment->file_name}』" . $leaveString;
							} elseif (!empty($info['external_id'])) {
								$externalInfo = WorkExternalContact::findOne($info['external_id']);
								$content      = "客户【{$externalInfo->name}】打开了{$attachment->getAttachmentTypeName()}素材『{$attachment->file_name}』" . $leaveString;
							} else {
								$content      = "未知用户【{$info['openid']}】打开了{$attachment->getAttachmentTypeName()}素材『{$attachment->file_name}』" . $leaveString;
								$externalInfo = WorkExternalContact::findOne(['openid' => $info['openid']]);
								if (!empty($externalInfo)) {
									$content = "流失客户【{$externalInfo->name}】打开了{$attachment->getAttachmentTypeName()}素材『{$attachment->file_name}』" . $leaveString;
								}
							}
					}
				}
				$infoData['icon']    = $icon;
				$infoData['content'] = $content;
				$returnData[]        = $infoData;
			}

			return $returnData;
		}

		//获取群详情
		public static function getChatDetail ($uid, $workChat)
		{
			/**@var WorkChat $workChat * */
			$result           = [];
			$result['name']   = WorkChat::getChatName($workChat->id);
			$result['notice'] = $workChat->notice;
			$result['status'] = $workChat->status;

			//跟进状态
			if (!empty($workChat->follow_id)) {
				$followInfo = Follow::findOne($workChat->follow_id);
			} else {
				$followInfo = Follow::findOne(['uid' => $uid, 'status' => 1]);
			}
			$result['follow_status'] = !empty($followInfo) ? $followInfo->status : 0;
			$result['follow_id']     = !empty($followInfo) ? $followInfo->id : 0;
			$result['follow_title']  = !empty($followInfo) ? $followInfo->title : '';
			$result['is_follow_del'] = empty($result['follow_status']) ? 1 : 0;

			//群标签
			$workTagContact = WorkTagChat::find()->alias('w');
			$workTagContact = $workTagContact->leftJoin('{{%work_tag}} t', '`t`.`id` = `w`.`tag_id`')->andWhere(['t.is_del' => 0, 't.type' => 2, 'w.status' => 1, 'w.chat_id' => $workChat->id]);
			$workTagContact = $workTagContact->select('t.*');
			$contactTag     = $workTagContact->asArray()->all();
			$tagName        = [];
			foreach ($contactTag as $k => $v) {
				$tagName[] = ['id' =>  $v['id'], 'tagname' => $v['tagname']];
			}
			$result['tag_name'] = $tagName;

			//群成员总数
			$result['all_sum']         = WorkChatInfo::find()->where(['chat_id' => $workChat->id, 'status' => 1])->count();
			$result['user_sum']        = WorkChatInfo::find()->where(['chat_id' => $workChat->id, 'status' => 1, 'type' => 1])->count();
			$result['external_sum']    = WorkChatInfo::find()->where(['chat_id' => $workChat->id, 'status' => 1, 'type' => 2])->andWhere(['>', 'external_id', 0])->count();
			$result['no_external_sum'] = WorkChatInfo::find()->where(['chat_id' => $workChat->id, 'status' => 1, 'type' => 2])->andWhere(['external_id' => NULL])->count();
			$result['avatarData']      = WorkChat::getChatAvatar($workChat->id, $workChat->status);

			//创建时间
			$result['create_time'] = date('Y-m-d H:i', $workChat->create_time);
			//今日数据
			$todayTime                 = strtotime(date('Y-m-d'));
			$result['today_join_sum']  = WorkChatInfo::find()->where(['chat_id' => $workChat->id, 'status' => 1])->andWhere(['>', 'join_time', $todayTime])->count();
			$result['today_leave_sum'] = WorkChatInfo::find()->where(['chat_id' => $workChat->id, 'status' => 0])->andWhere(['>', 'leave_time', $todayTime])->count();
			//是否有会话存档
			$auditInfo               = static::chatAuditInfo($workChat->corp_id, $workChat->id);
			$result['isAudit']       = $auditInfo['isAudit'];
			$result['todayAuditNum'] = $auditInfo['todayAuditNum'];

			//群自定义属性
			$fieldList           = CustomField::getCustomField($uid, $workChat->id, 3);
			$result['fieldList'] = $fieldList;

			return $result;
		}

		//群会话存档信息
		public static function chatAuditInfo ($corp_id, $chat_id, $selectNum = 1)
		{
			$res           = [];
			$isAudit       = 0;
			$todayAuditNum = 0;//今日群活跃人数

			$workAudit = WorkMsgAudit::findOne(['corp_id' => $corp_id, 'status' => 1]);
			if (!empty($workAudit)) {
				//群成员是否有开启会话存档
				$workChatInfo = WorkChatInfo::find()->andWhere(['chat_id' => $chat_id, 'type' => 1, 'status' => 1])->all();
				if (!empty($workChatInfo)) {
					$userIds = [];
					foreach ($workChatInfo as $v) {
						array_push($userIds, $v->user_id);
					}
					if (!empty($userIds)) {
						$userHasAudit = WorkMsgAuditUser::find()->where(['audit_id' => $workAudit->id, 'status' => 1])->andWhere(['user_id' => $userIds])->one();
						if (!empty($userHasAudit)) {
							$isAudit = 1;

							if ($selectNum == 1) {
								$smicrotime    = strtotime(date('Y-m-d')) * 1000;
								$field         = new Expression('from_type,CASE WHEN from_type = 1 THEN user_id ELSE external_id END as member_id,count(id) num');
								$chatAudit     = WorkMsgAuditInfo::find()->where(['chat_id' => $chat_id])->andWhere(['>', 'msgtime', $smicrotime])->andWhere(['from_type' => [1, 2]]);
								$chatAudit     = $chatAudit->select($field)->groupBy('member_id,from_type');
								$todayAuditNum = $chatAudit->count();
							}
						}
					}
				}
			}

			$res['isAudit']       = $isAudit;
			$res['todayAuditNum'] = $todayAuditNum;

			return $res;
		}

		//获取群内成员的头像
		public static function getChatAvatar ($chatId, $status = 0)
		{
			$avatarData = [];
			if (empty($chatId) || $status == 4) {
				return $avatarData;
			}
			$chatCount = WorkChatInfo::find()->where(['chat_id' => $chatId, 'status' => 1])->count();
			if ($chatCount > 9) {
				$chatCount = 9;
			}

			$chatInfoList = WorkChatInfo::find()->alias('wci');
			$chatInfoList = $chatInfoList->leftJoin('{{%work_user}} wu', 'wci.user_id = wu.id and wci.type=1');
			$chatInfoList = $chatInfoList->leftJoin('{{%work_external_contact}} wec', 'wci.external_id = wec.id and wci.type=2 and wec.avatar !=""');
			$chatInfoList = $chatInfoList->where(['wci.chat_id' => $chatId, 'wci.status' => 1]);
			$chatInfoList = $chatInfoList->andWhere(['or', ['!=', 'wec.avatar', ''], ['!=', 'wu.avatar', '']]);
			$chatInfoList = $chatInfoList->select('wci.type,wci.user_id,wci.external_id,wu.avatar,wec.avatar as avatar1')->limit(9)->asArray()->all();
			if (empty($chatInfoList)) {
				return $avatarData;
			}
			/**@var $chatInfo WorkChatInfo* */
			foreach ($chatInfoList as $chatInfo) {
				if (!empty($chatInfo['avatar'])) {
					array_push($avatarData, $chatInfo['avatar']);
				} elseif (!empty($chatInfo['avatar1'])) {
					array_push($avatarData, $chatInfo['avatar1']);
				}
			}
			$count = count($avatarData);
			if ($count < $chatCount) {
				$avatarData = array_pad($avatarData, $chatCount, '');
			}

			return $avatarData;
		}
	}
