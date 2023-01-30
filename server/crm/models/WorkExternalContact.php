<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\queue\SyncMsgAuditJob;
	use app\queue\WorkExternalContactBatchGetJob;
	use app\util\SUtils;
	use app\util\WorkUtils;
	use Yii;
	use yii\queue\redis\Queue;

	/**
	 * This is the model class for table "{{%work_external_contact}}".
	 *
	 * @property int                                  $id
	 * @property int                                  $corp_id         授权的企业ID
	 * @property string                               $way_id          联系我配置ID
	 * @property string                               $chat_way_id     群活码配置ID
	 * @property string                               $external_userid 外部联系人的userid
	 * @property string                               $name            外部联系人的姓名或别名
	 * @property string                               $name_convert    外部联系人的姓名或别名（解码后）
	 * @property string                               $position        外部联系人的职位，如果外部企业或用户选择隐藏职位，则不返回，仅当联系人类型是企业微信用户时有此字段
	 * @property string                               $avatar          外部联系人头像，第三方不可获取
	 * @property string                               $corp_name       外部联系人所在企业的简称，仅当联系人类型是企业微信用户时有此字段
	 * @property string                               $corp_full_name  外部联系人所在企业的主体名称，仅当联系人类型是企业微信用户时有此字段
	 * @property int                                  $type            外部联系人的类型，1表示该外部联系人是微信用户，2表示该外部联系人是企业微信用户
	 * @property int                                  $gender          外部联系人性别 0-未知 1-男性 2-女性
	 * @property string                               $unionid         外部联系人在微信开放平台的唯一身份标识（微信unionid），通过此字段企业可将外部联系人与公众号/小程序用户关联起来。仅当联系人类型是微信用户，且企业绑定了微信开发者ID有此字段。查看绑定方法
	 * @property string                               $is_fans         0：不是粉丝；1：是粉丝
	 * @property string                               $openid          外部联系人openid
	 * @property string                               $nickname        设置的用户昵称
	 * @property string                               $des             设置的用户描述
	 * @property int                                  $close_rate      预计成交率
	 * @property int                                  $follow_status   跟进状态
	 * @property int                                  $follow_id       跟进id
	 *
	 * @property AttachmentStatistic[]                $attachmentStatistics
	 * @property WorkChatInfo[]                       $workChatInfos
	 * @property WorkContactWayLine[]                 $workContactWayLines
	 * @property WorkCorp                             $corp
	 * @property Follow                               $follow
	 * @property WorkExternalContactExternalProfile[] $workExternalContactExternalProfiles
	 * @property WorkExternalContactFollowUser[]      $workExternalContactFollowUsers
	 * @property WorkExternalContactMember[]          $workExternalContactMembers
	 * @property WorkMomentGoods[]                    $workMomentGoods
	 * @property WorkMomentReply[]                    $workMomentReplies
	 * @property WorkMsgAuditAgree[]                  $workMsgAuditAgrees
	 * @property WorkMsgAuditInfo[]                   $workMsgAuditInfos
	 * @property WorkMsgAuditInfo[]                   $workMsgAuditInfosTo
	 * @property WorkMsgAuditInfoAgree[]              $workMsgAuditInfoAgrees
	 * @property WorkMsgAuditInfoCalendar[]           $workMsgAuditInfoCalendars
	 * @property WorkMsgAuditInfoCalendarAttendee[]   $workMsgAuditInfoCalendarAttendees
	 * @property WorkMsgAuditInfoCard[]               $workMsgAuditInfoCards
	 * @property WorkMsgAuditInfoDocmsg[]             $workMsgAuditInfoDocmsgs
	 * @property WorkMsgAuditInfoToInfo[]             $workMsgAuditInfoToInfos
	 * @property WorkTagContact[]                     $workTagContacts
	 */
	class WorkExternalContact extends \yii\db\ActiveRecord
	{
		const ADD_EXTERNAL_CONTACT = 'add_external_contact';
		const ADD_HALF_EXTERNAL_CONTACT = 'add_half_external_contact';
		const DEL_EXTERNAL_CONTACT = 'del_external_contact';
		const DEL_FOLLOW_USER = 'del_follow_user';
		const EDIT_EXTERNAL_CONTACT = 'edit_external_contact';
		const TRANSFER_FAIL = 'transfer_fail'; //客户接替失败

		const EVENT_EXTERNAL_CONTACT = 1;//通过事件过来的客户

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_external_contact}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_id', 'type', 'gender', 'close_rate', 'follow_status', 'follow_id', 'is_fans'], 'integer'],
				[['way_id', 'external_userid', 'position', 'corp_name', 'corp_full_name', 'unionid', 'openid', 'nickname'], 'string', 'max' => 64],
				[['name_convert', 'avatar', 'des'], 'string', 'max' => 255],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
				[['follow_id'], 'exist', 'skipOnError' => true, 'targetClass' => Follow::className(), 'targetAttribute' => ['follow_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'              => Yii::t('app', 'ID'),
				'corp_id'         => Yii::t('app', '授权的企业ID'),
				'way_id'          => Yii::t('app', '联系我配置ID'),
				'external_userid' => Yii::t('app', '外部联系人的userid'),
				'name'            => Yii::t('app', '外部联系人的姓名或别名'),
				'name_convert'    => Yii::t('app', '外部联系人的姓名或别名（解码后）'),
				'position'        => Yii::t('app', '外部联系人的职位，如果外部企业或用户选择隐藏职位，则不返回，仅当联系人类型是企业微信用户时有此字段'),
				'avatar'          => Yii::t('app', '外部联系人头像，第三方不可获取'),
				'corp_name'       => Yii::t('app', '外部联系人所在企业的简称，仅当联系人类型是企业微信用户时有此字段'),
				'corp_full_name'  => Yii::t('app', '外部联系人所在企业的主体名称，仅当联系人类型是企业微信用户时有此字段'),
				'type'            => Yii::t('app', '外部联系人的类型，1表示该外部联系人是微信用户，2表示该外部联系人是企业微信用户'),
				'gender'          => Yii::t('app', '外部联系人性别 0-未知 1-男性 2-女性'),
				'unionid'         => Yii::t('app', '外部联系人在微信开放平台的唯一身份标识（微信unionid），通过此字段企业可将外部联系人与公众号/小程序用户关联起来。仅当联系人类型是微信用户，且企业绑定了微信开发者ID有此字段。查看绑定方法'),
				'is_fans'         => Yii::t('app', '0：不是粉丝；1：是粉丝'),
				'openid'          => Yii::t('app', '外部联系人openid'),
				'nickname'        => Yii::t('app', '设置的用户昵称'),
				'des'             => Yii::t('app', '设置的用户描述'),
				'close_rate'      => Yii::t('app', '预计成交率'),
				'follow_status'   => Yii::t('app', '跟进状态'),
				'follow_id'       => Yii::t('app', '跟进状态id'),
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

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getAttachmentStatistics ()
		{
			return $this->hasMany(AttachmentStatistic::className(), ['external_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkChatInfos ()
		{
			return $this->hasMany(WorkChatInfo::className(), ['external_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkContactWayLines ()
		{
			return $this->hasMany(WorkContactWayLine::className(), ['external_userid' => 'id']);
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
		public function getFollow ()
		{
			return $this->hasOne(Follow::className(), ['id' => 'follow_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkExternalContactExternalProfiles ()
		{
			return $this->hasMany(WorkExternalContactExternalProfile::className(), ['external_userid' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkExternalContactFollowUsers ()
		{
			return $this->hasMany(WorkExternalContactFollowUser::className(), ['external_userid' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkExternalContactMembers ()
		{
			return $this->hasMany(WorkExternalContactMember::className(), ['external_userid' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMomentGoods ()
		{
			return $this->hasMany(WorkMomentGoods::className(), ['external_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMomentReplies ()
		{
			return $this->hasMany(WorkMomentReply::className(), ['external_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditAgrees ()
		{
			return $this->hasMany(WorkMsgAuditAgree::className(), ['external_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfos ()
		{
			return $this->hasMany(WorkMsgAuditInfo::className(), ['external_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfosTo ()
		{
			return $this->hasMany(WorkMsgAuditInfo::className(), ['to_external_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoAgrees ()
		{
			return $this->hasMany(WorkMsgAuditInfoAgree::className(), ['external_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoCalendars ()
		{
			return $this->hasMany(WorkMsgAuditInfoCalendar::className(), ['external_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoCalendarAttendees ()
		{
			return $this->hasMany(WorkMsgAuditInfoCalendarAttendee::className(), ['external_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoCards ()
		{
			return $this->hasMany(WorkMsgAuditInfoCard::className(), ['external_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoDocmsgs ()
		{
			return $this->hasMany(WorkMsgAuditInfoDocmsg::className(), ['external_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoToInfos ()
		{
			return $this->hasMany(WorkMsgAuditInfoToInfo::className(), ['external_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkTagContacts ()
		{
			return $this->hasMany(WorkTagContact::className(), ['contact_id' => 'id']);
		}

		public function beforeSave ($insert)
		{
			if (!empty($this->name)) {
				$this->name = rawurlencode($this->name);
			}

			return parent::beforeSave($insert); // TODO: Change the autogenerated stub
		}

		public function afterFind ()
		{
			if (!empty($this->name)) {
				$this->name = rawurldecode($this->name);
			}

			parent::afterFind(); // TODO: Change the autogenerated stub
		}

		/**
		 * @param int  $wayId
		 * @param bool $withFollowUser
		 *
		 * @return array
		 */
		public function dumpData ($wayId = 0, $withFollowUser = false)
		{
			$result = [
				'id'              => $this->id,
				'external_userid' => $this->external_userid,
				'name'            => $this->name,
				'name_convert'    => $this->name_convert,
				'position'        => $this->position,
				'avatar'          => $this->avatar,
				'corp_name'       => $this->corp_name,
				'corp_full_name'  => $this->corp_full_name,
				'type'            => $this->type,
				'gender'          => $this->gender,
				'unionid'         => $this->unionid,
				'openid'          => $this->openid,
				'nickname'        => $this->nickname,
				'des'             => $this->des,
				'close_rate'      => $this->close_rate,
				'follow_status'   => $this->follow_status,
				'follow_id'       => $this->follow_id,
			];
			/** @var \yii\web\User $appSubUser */
			$appSubUser = Yii::$app->subUser;
			if (!empty($appSubUser->identity)) {
				/** @var SubUser $subUser */
				$subUser = $appSubUser->identity;
				if (!empty($subUser)) {
					$mobile            = $subUser->account;
					$userCorpRelations = $subUser->u->userCorpRelations;
					if (!empty($userCorpRelations)) {
						$corpId = $userCorpRelations[0]->corp_id;

						$workUser = WorkUser::findOne(['corp_id' => $corpId, 'mobile' => $mobile]);
						if (!empty($workUser)) {
							$workExternalContactFollowUser = WorkExternalContactFollowUser::findOne(['external_userid' => $this->id, 'user_id' => $workUser->id]);
							if (!empty($workExternalContactFollowUser) && !empty($workExternalContactFollowUser->remark)) {
								$result['name'] = $workExternalContactFollowUser->remark . "（" . $this->name . "）";
							}
						}
					}
				}
			}

			if ($withFollowUser) {
				$result['follow_user'] = [];
				if (!empty($this->workExternalContactFollowUsers)) {
					foreach ($this->workExternalContactFollowUsers as $followUser) {
						$followUserInfo            = $followUser->dumpData(true, true);
						$followUserInfo['is_lock'] = false;

						if (!empty($wayId)) {
							$contactWay = WorkContactWay::findOne($wayId);
							if (!empty($contactWay) && $contactWay->state == $followUserInfo['state']) {
								$followUserInfo['is_lock'] = true;
							}
						}

						array_push($result['follow_user'], $followUserInfo);
					}
				}
			}

			return $result;
		}

		public function dumpMiniData ()
		{
			return [
				'name'           => $this->name,
				'name_convert'   => $this->name_convert,
				'avatar'         => $this->avatar,
				'corp_name'      => $this->corp_name,
				'corp_full_name' => $this->corp_full_name,
			];
		}

		/**
		 * @param        $authCorpId
		 * @param        $externalUserId
		 * @param int    $type
		 * @param string $followUserId
		 * @param string $state
		 * @param string $fromEvent //1通过事件过来 空同步客户非事件
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \Throwable
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function getUserSuite ($authCorpId, $externalUserId, $type = WorkUtils::FROM_BIND, $followUserId = '', $state = '', $fromEvent = '')
		{
			$authCorp = WorkCorp::findOne($authCorpId);

			if (empty($authCorp)) {
				throw new InvalidDataException('参数不正确。');
			}

			$workApi    = WorkUtils::getWorkApi($authCorpId, WorkUtils::EXTERNAL_API, $type);
			$externalId = 0;

			if (!empty($workApi)) {
				try {
					$externalUserInfo    = $workApi->ECGet($externalUserId);
					$externalContactInfo = SUtils::Object2Array($externalUserInfo);
					$externalId          = static::setUser($authCorp->id, $externalContactInfo, $fromEvent, $followUserId, $state);
//					WorkPublicActivityFansUser::corpPublicWelcomeSend($authCorp->id,$followUserId,$externalId,false);
				} catch (\Exception $e) {
					Yii::error($e->getMessage(), 'workExternalContactGet');
				}

			}

			return $externalId;
		}

		/**
		 * 获取微信客户的 openid
		 *
		 * @param bool $refresh
		 */
		public function getExternalOpenid ($refresh = false)
		{
			if (SUtils::getExternalType($this->external_userid) == SUtils::IS_WX_EXTERNAL) {
				if ($refresh || empty($this->openid)) {
					try {
						$workApi = WorkUtils::getWorkApi($this->corp_id, WorkUtils::EXTERNAL_API);
					} catch (\Exception $e) {
						Yii::error($this->external_userid, 'getExternalOpenid-external-userid');
						Yii::error($e->getMessage(), 'getExternalOpenid-getWorkApi');
					}

					if (!empty($workApi)) {
						try {
							$workApi->externalConvertToOpenid($this->external_userid, $openid);

							if ($this->openid != $openid) {
								$this->openid = $openid;

								$this->save();
							}
						} catch (\Exception $e) {
							Yii::error($this->external_userid, 'getExternalOpenid-external-userid');
							Yii::error($e->getMessage(), 'getExternalOpenid');
						}
					}
				}
			}
		}

		/**
		 * @param            $corpId
		 * @param            $externalContactInfo
		 * @param string|int $fromEvent
		 * @param string     $followUserId
		 * @param string     $state
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \Throwable
		 * @throws \yii\base\InvalidConfigException
		 * @throws \yii\db\StaleObjectException
		 */
		public static function setUser ($corpId, $externalContactInfo, $fromEvent = '', $followUserId = '', $state = '')
		{
			$externalContact = static::findOne(['corp_id' => $corpId, 'external_userid' => $externalContactInfo['external_userid']]);

			if (empty($externalContact)) {
				$externalContact          = new WorkExternalContact();
				$externalContact->corp_id = $corpId;
			}

			$externalContact->external_userid = $externalContactInfo['external_userid'];
			$externalContact->name            = $externalContactInfo['name'];
			$externalContact->name_convert    = $externalContactInfo['name'];

			if (!empty($externalContactInfo['position'])) {
				$externalContact->position = $externalContactInfo['position'];
			}

			if (!empty($externalContactInfo['avatar'])) {
				$externalContact->avatar = $externalContactInfo['avatar'];
			}

			if (!empty($externalContactInfo['corp_name'])) {
				$externalContact->corp_name = $externalContactInfo['corp_name'];
			}

			if (!empty($externalContactInfo['corp_full_name'])) {
				$externalContact->corp_full_name = $externalContactInfo['corp_full_name'];
			}

			if (isset($externalContactInfo['type'])) {
				$externalContact->type = $externalContactInfo['type'];
			}

			if (isset($externalContactInfo['gender'])) {
				$externalContact->gender = $externalContactInfo['gender'];
			}

			if (!empty($externalContactInfo['unionid'])) {
				$externalContact->unionid = $externalContactInfo['unionid'];
				$fans                     = Fans::findOne(['unionid' => $externalContactInfo['unionid']]);
				if (!empty($fans) && $fans->subscribe == 1) {
					$externalContact->is_fans = 1;
				} else {
					$externalContact->is_fans = 0;
				}
			}

			if (empty($externalContact->follow_id)) {
				$follow_id = Follow::getFollowIdByCorpId($corpId);
				if (!empty($follow_id)) {
					$externalContact->follow_id = $follow_id;
				}
			}

			if ($externalContact->dirtyAttributes) {
				if (!$externalContact->validate() || !$externalContact->save()) {
					throw new InvalidDataException(SUtils::modelError($externalContact));
				}
			}
			// 防止二次加密
			$externalContact->name = rawurldecode($externalContact->name);

			$externalContact->getExternalOpenid();

			if (!empty($externalContactInfo['external_profile'])) {
				WorkExternalContactExternalProfile::setExternalProfile($externalContact->id, $externalContactInfo['external_profile']);
			}
			if (!empty($externalContactInfo['follow_user'])) {
				foreach ($externalContactInfo['follow_user'] as $followUser) {
					$externalContact = WorkExternalContact::findOne($externalContact->id);
					$userId          = $followUser['userid'];
					$workUserId      = WorkUser::getUserId($corpId, $userId);
					if ($workUserId != 0) {
						if (!empty($followUser['state']) && ($followUser['state'] == $state)) {
							$stateArr = explode('_', $followUser['state']);
							if (count($stateArr) > 1 && $stateArr[0] == WorkContactWay::DEFAULT_WAY_PRE) {
								$contactWay = WorkContactWay::findOne(['id' => $stateArr[1], 'is_del' => WorkContactWay::WAY_NOT_DEL]);
							} else {
								$contactWay = WorkContactWay::findOne(['corp_id' => $corpId, 'state' => $followUser['state'], 'is_del' => WorkContactWay::WAY_NOT_DEL]);
							}
							Yii::error($stateArr, "sym-------");
							if ($stateArr[0] == WorkPublicActivity::STATE_NAME) {
								$followUser["activity_id"] = $stateArr[1];
							}

							if ($stateArr[0] != 'fission' && !empty($contactWay)) {
								$exFollowUser         = WorkExternalContactFollowUser::findOne(['way_id' => $contactWay->id, 'external_userid' => $externalContact->id, 'user_id' => $workUserId]);
								$followUser['way_id'] = $contactWay->id;
								$wayId                = '';
								if (empty($externalContact->way_id)) {
									$wayId = $contactWay->id;
								} else {
									$wayIds = explode(',', $externalContact->way_id);

									if (!in_array($contactWay->id, $wayIds)) {
										array_push($wayIds, $contactWay->id);
									}

									$wayId = implode(',', $wayIds);
								}

								static::updateAll(['way_id' => $wayId], ['id' => $externalContact->id]);

								if (empty($exFollowUser)) {
									$contactWay->add_num++;

									$contactWay->update();
								}
							} elseif ($stateArr[0] == Fission::FISSION_HEAD && !empty($stateArr[1])) {
								$fission = Fission::findOne(['corp_id' => $corpId, 'id' => $stateArr[1], 'status' => 2]);
								if (!empty($fission)) {
									//引流
									try {
										FissionJoin::setJoinData($externalContact->id, $stateArr);
									} catch (\Exception $e) {
										\Yii::error($e->getMessage(), 'setJoin');
									}
									$followUser['fission_id'] = $fission->id;
								}
							} elseif ($stateArr[0] == AwardsActivity::AWARD_HEAD && !empty($stateArr[1])) {
								$award = AwardsActivity::findOne(['corp_id' => $corpId, 'id' => $stateArr[1], 'status' => 1, 'is_del' => 0]);
								if (!empty($award)) {
									try {
										AwardsJoin::setJoinData($externalContact->id, $stateArr);
									} catch (\Exception $e) {
										\Yii::error($e->getMessage(), 'awardJoin');
									}
									$followUser['award_id'] = $award->id;
								}
							} elseif ($stateArr[0] == RedPack::RED_HEAD && !empty($stateArr[1])) {
								$redPack = RedPack::findOne(['corp_id' => $corpId, 'id' => $stateArr[1], 'status' => 2]);
								if (!empty($redPack)) {
									try {
										RedPackJoin::dealData($externalContact->id, $stateArr);
									} catch (\Exception $e) {
										\Yii::error($e->getMessage(), 'redPackJoin');
									}
									$followUser['red_pack_id'] = $redPack->id;
								}
							} elseif ($stateArr[0] == WorkChatContactWay::CHAT_HEAD && !empty($stateArr[1])) {
								$chatWay = WorkChatContactWay::findOne(['corp_id' => $corpId, 'state' => $followUser['state']]);
								if (!empty($chatWay)) {
									$followUser['chat_way_id'] = $chatWay->id;
									if (empty($externalContact->chat_way_id)) {
										$chatWayId = $chatWay->id;
									} else {
										$wayIds = explode(',', $externalContact->chat_way_id);

										if (!in_array($chatWay->id, $wayIds)) {
											array_push($wayIds, $chatWay->id);
										}

										$chatWayId = implode(',', $wayIds);
									}

									static::updateAll(['chat_way_id' => $chatWayId], ['id' => $externalContact->id]);
								}
							} elseif ($stateArr[0] == WorkContactWayBaiduCode::BAIDU_HEAD && !empty($stateArr[1]) && ($followUser['state'] == $state)) {
								$baiduInfo = WorkContactWayBaidu::findOne(['corp_id' => $corpId, 'id' => $stateArr[1], 'is_del' => 0]);
								if (!empty($baiduInfo)) {
									$baiduCode = WorkContactWayBaiduCode::findOne(['way_id' => $stateArr[1], 'state' => $followUser['state']]);
									if (!empty($baiduCode)) {
										$exFollowUser = WorkExternalContactFollowUser::findOne(['baidu_way_id' => $baiduInfo->id, 'external_userid' => $externalContact->id, 'user_id' => $workUserId]);
										if (empty($exFollowUser)) {
											$baiduCode->add_num++;
											$baiduCode->update();
											$baiduInfo->add_num++;
											$baiduInfo->update();
											//回传百度
											WorkContactWayBaiduCode::sendConvertData($baiduCode);
										}
									}
									$followUser['baidu_way_id'] = $baiduInfo->id;
								}
							} elseif ($stateArr[0] == WorkContactWayRedpacket::REDPACKET_WAY) {
								$wayRedpacket = WorkContactWayRedpacket::findOne(['corp_id' => $corpId, 'state' => $followUser['state'], 'redpacket_status' => [1, 2, 3]]);
								if (!empty($wayRedpacket)) {
									$followUser['way_redpack_id'] = $wayRedpacket->id;

									$exFollowUser = WorkExternalContactFollowUser::findOne(['way_redpack_id' => $wayRedpacket->id, 'external_userid' => $externalContact->id, 'user_id' => $workUserId]);
									if (empty($exFollowUser)) {
										$wayRedpacket->add_num++;
										$wayRedpacket->update();
									}
								}
							} elseif ($stateArr[0] == WorkGroupClockActivity::NAME) {
								$clock = WorkGroupClockActivity::findOne(['corp_id' => $corpId, 'id' => $stateArr[1], 'status' => 1]);
								if (!empty($clock)) {
									$followInfo['punch_id'] = $clock->id;
								}
							} elseif ($stateArr[0] == AuthStore::STORE_NAME) {
								$AuthStore = AuthStore::findOne($stateArr[1]);
								if (!empty($AuthStore)) {
									$followUser['auth_store'] = $AuthStore->id;
								}
							}
						}
						Yii::error($followUser, "sym-------");

						WorkExternalContactFollowUser::setFollowUser($workUserId, $externalContact->id, $followUser, $corpId, $followUserId, $state, $fromEvent);

						//补充会话存档客户id
						/*\Yii::$app->work->push(new SyncMsgAuditJob([
							'corpId' => $corpId,
							'userId' => $workUserId,
						]));*/
					}
				}
			}

			//根据openid把群打卡活动参与表中换成external_id
			WorkGroupClockJoin::updateJoinExternalId($externalContact->corp_id, $externalContact->id, $externalContact->openid);
			
			//同步高级属性性别
			$customField = CustomFieldValue::findOne(['type' => 1, 'fieldid' => 3, 'cid' => $externalContact->id]);
			if (empty($customField)) {
				if ($externalContact->gender == 1) {
					$sex = '男';
				} elseif ($externalContact->gender == 2) {
					$sex = '女';
				} else {
					$sex = '未知';
				}
				$uid = 0;
				$userCorp = UserCorpRelation::findOne(['corp_id' => $corpId]);
				if (!empty($userCorp)) {
					$uid      = $userCorp->uid;
				}
				$customField          = new CustomFieldValue();
				$customField->type    = 1;
				$customField->uid     = $uid;
				$customField->cid     = $externalContact->id;
				$customField->fieldid = 3;
				$customField->value   = $sex;
				$customField->time    = time();

				if (!$customField->validate() || !$customField->save()) {
					throw new InvalidDataException(SUtils::modelError($customField));
				}
			}

			return $externalContact->id;
		}

		/**
		 * @param $corpId
		 *
		 * @return boolean
		 *
		 * @throws InvalidDataException
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \Throwable
		 * @throws \yii\base\InvalidConfigException
		 * @throws \yii\db\StaleObjectException
		 */
		public static function getExternalContactList ($corpId)
		{
			$workApi = WorkUtils::getWorkApi($corpId, WorkUtils::EXTERNAL_API);
			if (!empty($workApi)) {
				$workUser = WorkUser::find()
					->where(['corp_id' => $corpId, 'is_external' => 1])
					->select('userid')
					->all();
				if (!empty($workUser)) {
					/** @var Queue $workQueue */
					$workQueue = Yii::$app->work;
					/** @var WorkUser $user */
					foreach ($workUser as $user) {
						$jobId = $workQueue->push(new WorkExternalContactBatchGetJob([
							'corp_id' => $corpId,
							'user_id' => $user->userid,
						]));
					}
				}
			}

			return true;
		}

		/**
		 * @param      $corpId
		 * @param      $externalUserId
		 * @param bool $need
		 *
		 * @return int
		 *
		 * @throws \Throwable
		 */
		public static function getExternalId ($corpId, $externalUserId, $need = false)
		{
			$externalContactId = 0;

			$externalContact = WorkExternalContact::findOne(['corp_id' => $corpId, 'external_userid' => $externalUserId]);
			if (empty($externalContact) && $need) {
				try {
					$externalContactId = WorkExternalContact::getUserSuite($corpId, $externalUserId);
				} catch (\Exception $e) {
					\Yii::error($e->getMessage(), __CLASS__ . '-' . __FUNCTION__);
				}
			} elseif (!empty($externalContact)) {
				$externalContactId = $externalContact->id;
				$externalContact->getExternalOpenid();
			}

			return $externalContactId;
		}
	}
