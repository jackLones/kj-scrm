<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\components\InvalidParameterException;
	use app\util\DateUtil;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%work_chat_info}}".
	 *
	 * @property int                 $id
	 * @property int                 $chat_id     客户群列表ID
	 * @property int                 $user_id     成员用户ID
	 * @property int                 $external_id 外部联系人ID
	 * @property string              $userid      群成员id（可能是内部成员，也可能是外部联系人）
	 * @property int                 $type        成员类型。1 - 企业成员2 - 外部联系人
	 * @property string              $join_time   入群时间
	 * @property int                 $leave_time  离开时间
	 * @property int                 $join_scene  入群方式。1 - 由成员邀请入群（直接邀请入群）2 - 由成员邀请入群（通过邀请链接入群）3 - 通过扫描群二维码入群
	 * @property int                 $status      成员状态。1 - 正常；0 - 已离开
	 * @property string              $create_time 创建时间
	 *
	 * @property WorkExternalContact $external
	 * @property WorkChat            $chat
	 * @property WorkUser            $user
	 */
	class WorkChatInfo extends \yii\db\ActiveRecord
	{
		const WORK_MEMBER = 1;
		const EXTERNAL_MEMBER = 2;

		const DIRECT_INVITATION = 1;
		const LINK_INVITATION = 2;
		const QRCODE_INVITATION = 3;

		const NORMAL_MEMBER = 1;
		const LEAVE_MEMBER = 0;

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_chat_info}}';
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

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['chat_id', 'user_id', 'external_id', 'type', 'join_scene', 'status', 'leave_time'], 'integer'],
				[['create_time'], 'safe'],
				[['userid'], 'string', 'max' => 64],
				[['external_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkExternalContact::className(), 'targetAttribute' => ['external_id' => 'id']],
				[['chat_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkChat::className(), 'targetAttribute' => ['chat_id' => 'id']],
				[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkUser::className(), 'targetAttribute' => ['user_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => Yii::t('app', 'ID'),
				'chat_id'     => Yii::t('app', '客户群列表ID'),
				'user_id'     => Yii::t('app', '成员用户ID'),
				'external_id' => Yii::t('app', '外部联系人ID'),
				'userid'      => Yii::t('app', '群成员id（可能是内部成员，也可能是外部联系人）'),
				'type'        => Yii::t('app', '成员类型。1 - 企业成员2 - 外部联系人'),
				'join_time'   => Yii::t('app', '入群时间'),
				'leave_time'  => Yii::t('app', '离开时间'),
				'join_scene'  => Yii::t('app', '入群方式。1 - 由成员邀请入群（直接邀请入群）2 - 由成员邀请入群（通过邀请链接入群）3 - 通过扫描群二维码入群'),
				'status'      => Yii::t('app', '成员状态。1 - 正常；0 - 已离开'),
				'create_time' => Yii::t('app', '创建时间'),
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
		public function getChat ()
		{
			return $this->hasOne(WorkChat::className(), ['id' => 'chat_id']);
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
		 * @param bool $withChat
		 *
		 * @return array
		 *
		 */
		public function dumpData ($withUser = false, $withExternal = false, $withChat = false)
		{
			$data = [
				'id'          => $this->id,
				'chat_id'     => $this->chat_id,
				'user_id'     => $this->user_id,
				'external_id' => $this->external_id,
				'userid'      => $this->userid,
				'type'        => $this->type,
				'join_time'   => $this->join_time,
				'join_scene'  => $this->join_scene,
				'status'      => $this->status,
				'create_time' => $this->create_time,
			];

			if ($withUser) {
				$data['work_user'] = $this->user->dumpData();
			}

			if ($withExternal) {
				$data['external_user'] = $this->external->dumpData();
			}

			if ($withChat) {
				$data['chat'] = $this->chat->dumpData();
			}

			return $data;
		}

		/**
		 * @param $corpId
		 * @param $chatId
		 * @param $memberInfo
		 * @param $otherData
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 * @throws InvalidParameterException
		 * @throws \Throwable
		 */
		public static function add ($corpId, $chatId, $memberInfo, $otherData)
		{
			if (empty($corpId) || empty($chatId) || empty($memberInfo) || empty($memberInfo['userid'])) {
				throw new InvalidParameterException("参数不正确");
			}
			\Yii::error($chatId, '$chatId');
			$workChatInfo = static::findOne(['chat_id' => $chatId, 'userid' => $memberInfo['userid']]);
			if (empty($workChatInfo)) {
				$workChatInfo              = new WorkChatInfo();
				$workChatInfo->chat_id     = $chatId;
				$workChatInfo->userid      = $memberInfo['userid'];
				$workChatInfo->create_time = DateUtil::getCurrentTime();
			}

			$workChatInfo->status = self::NORMAL_MEMBER;

			if (!empty($memberInfo['type'])) {
				$workChatInfo->type = $memberInfo['type'];

				switch ($workChatInfo->type) {
					case self::WORK_MEMBER:
						$workUserId = WorkUser::getUserId($corpId, $memberInfo['userid']);
						if (!empty($workUserId)) {
							$workChatInfo->user_id = $workUserId;
						}

						break;

					case self::EXTERNAL_MEMBER:
						$externalContactId = WorkExternalContact::getExternalId($corpId, $memberInfo['userid']);
						if (!empty($externalContactId)) {
							$workChatInfo->external_id = $externalContactId;
						}

						break;
				}
			}

			if (!empty($memberInfo['join_time'])) {
				$workChatInfo->join_time = $memberInfo['join_time'];
			}

			if (!empty($memberInfo['join_scene'])) {
				$workChatInfo->join_scene = $memberInfo['join_scene'];
			}

			$workChatInfo->leave_time = 0;

			if ($workChatInfo->dirtyAttributes) {
				if (!$workChatInfo->validate() || !$workChatInfo->save()) {
					throw new InvalidDataException(SUtils::modelError($workChatInfo));
				}
			}

			//群互动轨迹
			if (($memberInfo['type'] == 1 && !empty($otherData['owner_id']) && ($otherData['owner_id'] != $workChatInfo->user_id)) || $memberInfo['type'] == 2) {
				$whereData = ['event' => 'chat_track', 'related_id' => $chatId, 'event_id' => [1, 2, 3, 8]];
				$addData   = ['uid' => 0, 'event' => 'chat_track', 'event_id' => 2, 'event_time' => $workChatInfo->join_time, 'related_id' => $chatId];
				$isAdd     = 0;
				$remark    = '';
				if ($memberInfo['type'] == 1) {
					$whereData['user_id'] = $workChatInfo->user_id;
					$addData['user_id']   = $workChatInfo->user_id;
					$userInfo             = WorkUser::findOne($workChatInfo->user_id);
					if (!empty($userInfo)) {
						$remark .= '群企业成员【' . $userInfo->name . '】';
					} else {
						$remark .= '群企业成员【' . $workChatInfo->userid . '】';
					}
				} elseif ($memberInfo['type'] == 2) {
					if (!empty($workChatInfo->external_id)) {
						$whereData['external_id'] = $workChatInfo->external_id;
						$addData['external_id']   = $workChatInfo->external_id;
						$contactInfo              = WorkExternalContact::findOne($workChatInfo->external_id);
						$remark                   .= !empty($contactInfo) ? '客户【' . $contactInfo->name . '】' : '客户';
					} else {
						$whereData['openid'] = $workChatInfo->userid;
						$addData['openid']   = $workChatInfo->userid;
						$remark              .= '未知客户【' . substr_replace($workChatInfo->userid, '****', 3, 22) . '】';
					}
				}
				$chatTimeLine = ExternalTimeLine::find()->where($whereData)->orderBy(['id' => SORT_DESC])->one();
				if (!empty($chatTimeLine)) {
					if ($chatTimeLine->event_id == 3) {
						$isAdd = 1;
					}
				} else {
					$isAdd = 1;
				}
				if (!empty($isAdd)) {
					if ($workChatInfo->join_scene == 1) {
						$remark .= '通过【直接邀请入群】';
					} elseif ($workChatInfo->join_scene == 2) {
						$remark .= '通过【邀请链接入群】';
					} elseif ($workChatInfo->join_scene == 3) {
						$remark .= '通过【扫描群二维码入群】';
					}
					$chatName          = !empty($otherData['chat_name']) ? '【' . $otherData['chat_name'] . '】' : '群';
					$remark            .= '加入' . $chatName;
					$addData['remark'] = $remark;
					\Yii::error($addData,'$addData');
					ExternalTimeLine::addExternalTimeLine($addData);
				}
			}


			\Yii::error($workChatInfo->external_id, 'external_id');
			//更改标签拉群明细入群状态
			if (!empty($workChatInfo->external_id)) {
				$pullId  = [];
				$wayList = WorkChatWayList::find()->where(['chat_id' => $chatId])->asArray()->all();
				\Yii::error($wayList, '$wayList');
				if (!empty($wayList)) {
					foreach ($wayList as $list) {
						if (!empty($list['tag_pull_id'])) {
							array_push($pullId, $list['tag_pull_id']);
						}
					}
				}
				\Yii::error($pullId, '$pullId');
				if (!empty($pullId)) {
					$pullGroup = WorkTagPullGroup::find()->where(['id' => $pullId])->asArray()->all();
					if (!empty($pullGroup)) {
						foreach ($pullGroup as $group) {
							$ids    = [];
							$others = json_decode($group['others'], true);
							if (isset($others['others']['user_ids']) && !empty($others['others']['user_ids'])) {
								$userIds = $others['others']['user_ids'];
								foreach ($userIds as $user) {
									array_push($ids, $user['id']);
								}
							}
							\Yii::error($ids, '$ids');
							if (!empty($ids)) {
								foreach ($ids as $id) {
									$followUser = WorkExternalContactFollowUser::find()->where(['user_id' => $id, 'external_userid' => $workChatInfo->external_id, 'del_type' => WorkExternalContactFollowUser::WORK_CON_EX]);
									\Yii::error($followUser, '$followUser');
									if (!empty($followUser)) {
										$sta = WorkTagGroupStatistic::findOne(['pull_id' => $group['id'], 'external_id' => $workChatInfo->external_id, 'user_id' => $id, 'chat_id' => $chatId, 'status' => 0]);
										if (!empty($sta)) {
											$sta->status = 1;
											$sta->save();

											$tagSta = WorkTagGroupUserStatistic::findOne(['pull_id' => $group['id'], 'user_id' => $id]);
											\Yii::error($tagSta, '$tagSta');
											if (!empty($tagSta)) {
												$has_num         = intval($tagSta->has_num);
												$has_num         = $has_num + 1;
												$tagSta->has_num = $has_num; //入群人数
												$tagSta->save();
											}

										}
									}
								}
							}

						}
					}
				}

			}
			//更改自动拉群群码状态
			$chat = WorkChatWayList::find()->where(['chat_id' => $chatId])->orderBy(['id' => SORT_ASC])->all();
			if (!empty($chat)) {
				$count = static::find()->where(['chat_id' => $chatId, 'status' => self::NORMAL_MEMBER])->count();

				/** @var WorkChatWayList $chatWayListInfo */
				foreach ($chat as $chatWayListInfo) {
					if ($chatWayListInfo->chat_status != WorkchatWayList::CLOSE_WAY && $chatWayListInfo->limit <= $count) {
						$chatWayListInfo->chat_status = WorkChatWayList::CLOSE_WAY;

						$chatWayListInfo->save();
					}
				}

			}

			return $workChatInfo->id;
		}

		/**
		 * 添加内部群成员数据
		 *
		 * @param $corpId
		 * @param $chatId
		 * @param $memberInfo
		 *
		 * @return  int
		 *
		 * @throws InvalidDataException
		 * @throws InvalidParameterException
		 */
		public static function addUser ($corpId, $chatId, $memberInfo)
		{
			if (empty($corpId) || empty($chatId) || empty($memberInfo) || empty($memberInfo['memberid'])) {
				throw new InvalidParameterException("参数不正确");
			}

			$workChatInfo = static::findOne(['chat_id' => $chatId, 'userid' => $memberInfo['memberid']]);
			if (empty($workChatInfo)) {
				$workChatInfo              = new WorkChatInfo();
				$workChatInfo->chat_id     = $chatId;
				$workChatInfo->userid      = $memberInfo['memberid'];
				$workChatInfo->create_time = DateUtil::getCurrentTime();
			}
			$workChatInfo->status = self::NORMAL_MEMBER;
			$workChatInfo->type   = 1;

			$workUserId = WorkUser::getUserId($corpId, $memberInfo['memberid']);
			if (!empty($workUserId)) {
				$workChatInfo->user_id = $workUserId;
			}

			if (!empty($memberInfo['jointime'])) {
				$workChatInfo->join_time = $memberInfo['jointime'];
			}
			$workChatInfo->leave_time = 0;

			if ($workChatInfo->dirtyAttributes) {
				if (!$workChatInfo->validate() || !$workChatInfo->save()) {
					throw new InvalidDataException(SUtils::modelError($workChatInfo));
				}
			}
			return $workChatInfo->id;
		}

		/**
		 * 获取单位时间内新增及离开成员数、截止时间群成员数及退群人数
		 *
		 * @param $chatId
		 * @param $stime
		 * @param $etime
		 *
		 * @return array
		 *
		 * @throws InvalidDataException
		 * @throws InvalidParameterException
		 * @throws \Throwable
		 */
		public static function getChatMemberByTime ($chatId, $stime, $etime)
		{
			$result              = [];
			$result['add_num']   = static::find()->andWhere(['chat_id' => $chatId])->andFilterWhere(['between', 'join_time', strtotime($stime), strtotime($etime . ' 23:59:59')])->count();
			$result['leave_num'] = static::find()->andWhere(['chat_id' => $chatId, 'status' => 0])->andFilterWhere(['between', 'leave_time', strtotime($stime), strtotime($etime . ' 23:59:59')])->count();
			//$result['member_snum'] = static::find()->andWhere(['chat_id' => $chatId])->andWhere(['or', ['and', ['`status`' => 1], ['<', 'join_time', strtotime($etime . ' 23:59:59')]], ['and', ['`status`' => 0], ['<', 'join_time', strtotime($etime . ' 23:59:59')], ['>', 'leave_time', strtotime($etime . ' 23:59:59')]]])->count();
			//$result['member_snum'] = static::find()->andWhere(['chat_id' => $chatId, 'status' => 1])->andWhere(['<', 'join_time', strtotime($etime . ' 23:59:59')])->count();
			$result['leave_snum']  = static::find()->andWhere(['chat_id' => $chatId, 'status' => 0])->andWhere(['<', 'leave_time', strtotime($etime . ' 23:59:59')])->count();
			$member_snum           = static::find()->andWhere(['chat_id' => $chatId])->andWhere(['<', 'join_time', strtotime($etime . ' 23:59:59')])->count();
			$result['member_snum'] = $member_snum > $result['leave_snum'] ? ($member_snum - $result['leave_snum']) : 0;

			return $result;
		}

		/*
		 * 根据参数获取用户所在群
		 * $type 1、企业成员，2、外部联系人
		 * $user_external_id 外部联系人id
		 * $chat_id 群id，有值时返回值排除该群名称
		 * $unshare_chat 不分享所在群
		 * $user_id 员工id（含集合）
		 */
		public static function getChatList ($type, $user_external_id, $chat_id = '', $unshare_chat = 0, $user_id = '')
		{
			$chatName = [];
			if (empty($user_external_id)) {
				return $chatName;
			}
			$chatInfo = static::find()->alias('wci');
			$chatInfo = $chatInfo->leftJoin('{{%work_chat}} wc', '`wci`.`chat_id` = `wc`.`id`');
			$chatInfo = $chatInfo->where(['wci.status' => 1]);
			if ($type == 1) {
				$chatInfo = $chatInfo->andWhere(['wci.user_id' => $user_external_id]);
			} elseif ($type == 2) {
				$chatInfo = $chatInfo->andWhere(['wci.external_id' => $user_external_id]);
				if ($unshare_chat == 1) {
					//不共享客户群
					$commonChatId = static::getCommonChat($user_id, $user_external_id);
					$chatInfo     = $chatInfo->andWhere(['wci.chat_id' => $commonChatId]);
				}
			} elseif ($type == 3) {
				$chatInfo = $chatInfo->andWhere(['wc.owner_id' => $user_external_id]);
			}
			$chatInfo = $chatInfo->select('wci.join_time,wc.id,wc.name')->asArray()->all();
			if (!empty($chatInfo)) {
				foreach ($chatInfo as $chat) {
					if ($chat_id == $chat['id']) {
						continue;
					}
					if (empty($chat['name'])) {
						$name = WorkChat::getChatName($chat['id']);
					} else {
						$name = $chat['name'];
					}
					$chatName[] = ['name' => $name, 'join_time' => date('Y-m-d H:i:s', $chat['join_time'])];
				}
			}

			return $chatName;
		}

		/**
		 * 获取员工和客户公共的群
		 */
		public function getCommonChat ($user_id, $external_id)
		{
			$userChatId     = [];
			$externalChatId = [];

			$userChat = static::find()->where(['type' => 1, 'status' => 1])->andWhere(['user_id' => $user_id])->select('chat_id')->all();
			foreach ($userChat as $v) {
				array_push($userChatId, $v->chat_id);
			}
			$externalChat = static::find()->where(['type' => 2, 'external_id' => $external_id, 'status' => 1])->select('chat_id')->all();
			foreach ($externalChat as $v) {
				array_push($externalChatId, $v->chat_id);
			}

			$commonChatId = array_intersect($userChatId, $externalChatId);
			$commonChatId = !empty($commonChatId) ? $commonChatId : 0;

			return $commonChatId;
		}

	}
