<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\queue\WorkUserStatisticJob;
	use app\util\DateUtil;
	use app\util\SUtils;
	use app\util\WorkUtils;
	use Yii;
	use yii\helpers\Json;

	/**
	 * This is the model class for table "{{%work_user}}".
	 *
	 * @property int                                  $id
	 * @property int                                  $corp_id                   授权的企业ID
	 * @property string                               $userid                    成员UserID。对应管理端的帐号，企业内必须唯一。不区分大小写，长度为1~64个字节
	 * @property string                               $name                      成员名称，此字段从2019年12月30日起，对新创建第三方应用不再返回，2020年6月30日起，对所有历史第三方应用不再返回，后续第三方仅通讯录应用可获取，第三方页面需要通过通讯录展示组件来展示名字
	 * @property string                               $department                成员所属部门id列表，仅返回该应用有查看权限的部门id
	 * @property string                               $order                     部门内的排序值，默认为0。数量必须和department一致，数值越大排序越前面。值范围是[0, 2^32)
	 * @property string                               $position                  职务信息；第三方仅通讯录应用可获取
	 * @property string                               $mobile                    手机号码，第三方仅通讯录应用可获取
	 * @property string                               $gender                    性别。0表示未定义，1表示男性，2表示女性
	 * @property string                               $email                     邮箱，第三方仅通讯录应用可获取
	 * @property string                               $is_leader_in_dept         表示在所在的部门内是否为上级。；第三方仅通讯录应用可获取
	 * @property string                               $avatar                    头像url。 第三方仅通讯录应用可获取
	 * @property string                               $thumb_avatar              头像缩略图url。第三方仅通讯录应用可获取
	 * @property string                               $telephone                 座机。第三方仅通讯录应用可获取
	 * @property int                                  $enable                    成员启用状态。1表示启用的成员，0表示被禁用。注意，服务商调用接口不会返回此字段
	 * @property string                               $alias                     别名；第三方仅通讯录应用可获取
	 * @property string                               $address                   地址
	 * @property string                               $extattr                   扩展属性，第三方仅通讯录应用可获取
	 * @property int                                  $status                    激活状态: 1=已激活，2=已禁用，4=未激活。已激活代表已激活企业微信或已关注微工作台（原企业号）。未激活代表既未激活企业微信又未关注微工作台（原企业号）
	 * @property string                               $qr_code                   员工个人二维码，扫描可添加为外部联系人(注意返回的是一个url，可在浏览器上打开该url以展示二维码)；第三方仅通讯录应用可获取
	 * @property int                                  $is_del                    0：未删除；1：已删除
	 * @property int                                  $is_external               是否具有外部联系人权限 1有 0没有
	 * @property int                                  $new_apply_cnt             发起申请数
	 * @property int                                  $new_contact_cnt           新增客户数
	 * @property int                                  $negative_feedback_cnt     删除/拉黑成员的客户数
	 * @property int                                  $chat_cnt                  聊天总数
	 * @property int                                  $message_cnt               发送消息数
	 * @property string                               $reply_percentage          已回复聊天占比
	 * @property string                               $avg_reply_time            平均首次回复时长(分钟)
	 * @property string                               $openid                    成员openid
	 * @property int                                  $day_user_num              员工单日红包发送次数限制
	 * @property string                               $day_user_money            员工单日红包发送额度限制
	 * @property int                                  $can_send_money            是否可发红包1是0否
	 * @property int                                  $dimission_time            离职时间
	 * @property int                                  $sop_msg_status            SOP消息免打扰是否开启1是0否
	 * @property int                                  $sop_chat_msg_status       SOP群消息免打扰是否开启1是0否
	 *
	 * @property AttachmentStatistic[]                $attachmentStatistics
	 * @property WorkChat[]                           $workChats
	 * @property WorkChatInfo[]                       $workChatInfos
	 * @property WorkChatStatistic[]                  $workChatStatistics
	 * @property WorkContactWayLine[]                 $workContactWayLines
	 * @property WorkContactWayUser[]                 $workContactWayUsers
	 * @property WorkExternalContactFollowStatistic[] $workExternalContactFollowStatistics
	 * @property WorkExternalContactFollowUser[]      $workExternalContactFollowUsers
	 * @property WorkFollowUser[]                     $workFollowUsers
	 * @property WorkMomentGoods[]                    $workMomentGoods
	 * @property WorkMomentReply[]                    $workMomentReplies
	 * @property WorkMomentUserConfig[]               $workMomentUserConfigs
	 * @property WorkMoments[]                        $workMoments
	 * @property WorkMsgAuditAgree[]                  $workMsgAuditAgrees
	 * @property WorkMsgAuditInfo[]                   $workMsgAuditInfos
	 * @property WorkMsgAuditInfo[]                   $workMsgAuditInfosTo
	 * @property WorkMsgAuditInfoAgree[]              $workMsgAuditInfoAgrees
	 * @property WorkMsgAuditInfoCalendar[]           $workMsgAuditInfoCalendars
	 * @property WorkMsgAuditInfoCalendarAttendee[]   $workMsgAuditInfoCalendarAttendees
	 * @property WorkMsgAuditInfoCard[]               $workMsgAuditInfoCards
	 * @property WorkMsgAuditInfoDocmsg[]             $workMsgAuditInfoDocmsgs
	 * @property WorkMsgAuditInfoToInfo[]             $workMsgAuditInfoToInfos
	 * @property WorkMsgAuditNoticeRuleInfo[]         $workMsgAuditNoticeRuleInfos
	 * @property WorkMsgAuditUser[]                   $workMsgAuditUsers
	 * @property WorkTagGroupStatistic[]              $workTagGroupStatistics
	 * @property WorkTagGroupUserStatistic[]          $workTagGroupUserStatistics
	 * @property WorkTagUser[]                        $workTagUsers
	 * @property WorkCorp                             $corp
	 * @property WorkUserAuthorRelation[]             $workUserAuthorRelations
	 * @property WorkUserExternalProfile[]            $workUserExternalProfiles
	 * @property WorkWelcome[]                        $workWelcomes
	 */
	class WorkUser extends \yii\db\ActiveRecord
	{
		const CREATE_USER = 'create_user';
		const UPDATE_USER = 'update_user';
		const DELETE_USER = 'delete_user';

		const USER_NO_DEL = 0;
		const USER_IS_DEL = 1;
		const IS_EXTERNAL = 0;

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_user}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_id', 'enable', 'status', 'is_del', 'is_external', 'new_apply_cnt', 'new_contact_cnt', 'negative_feedback_cnt', 'chat_cnt', 'message_cnt', 'day_user_num', 'can_send_money', 'dimission_time', 'sop_msg_status', 'sop_chat_msg_status'], 'integer'],
				[['department', 'order', 'position', 'extattr'], 'string'],
				[['day_user_money'], 'number'],
				[['userid', 'name', 'mobile', 'gender', 'email', 'is_leader_in_dept', 'telephone', 'alias', 'openid'], 'string', 'max' => 64],
				[['avatar', 'thumb_avatar', 'address', 'qr_code'], 'string', 'max' => 255],
				[['reply_percentage'], 'string', 'max' => 8],
				[['avg_reply_time'], 'string', 'max' => 16],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'                    => Yii::t('app', 'ID'),
				'corp_id'               => Yii::t('app', '授权的企业ID'),
				'userid'                => Yii::t('app', '成员UserID。对应管理端的帐号，企业内必须唯一。不区分大小写，长度为1~64个字节'),
				'name'                  => Yii::t('app', '成员名称，此字段从2019年12月30日起，对新创建第三方应用不再返回，2020年6月30日起，对所有历史第三方应用不再返回，后续第三方仅通讯录应用可获取，第三方页面需要通过通讯录展示组件来展示名字'),
				'department'            => Yii::t('app', '成员所属部门id列表，仅返回该应用有查看权限的部门id'),
				'order'                 => Yii::t('app', '部门内的排序值，默认为0。数量必须和department一致，数值越大排序越前面。值范围是[0, 2^32)'),
				'position'              => Yii::t('app', '职务信息；第三方仅通讯录应用可获取'),
				'mobile'                => Yii::t('app', '手机号码，第三方仅通讯录应用可获取'),
				'gender'                => Yii::t('app', '性别。0表示未定义，1表示男性，2表示女性'),
				'email'                 => Yii::t('app', '邮箱，第三方仅通讯录应用可获取'),
				'is_leader_in_dept'     => Yii::t('app', '表示在所在的部门内是否为上级。；第三方仅通讯录应用可获取'),
				'avatar'                => Yii::t('app', '头像url。 第三方仅通讯录应用可获取'),
				'thumb_avatar'          => Yii::t('app', '头像缩略图url。第三方仅通讯录应用可获取'),
				'telephone'             => Yii::t('app', '座机。第三方仅通讯录应用可获取'),
				'enable'                => Yii::t('app', '成员启用状态。1表示启用的成员，0表示被禁用。注意，服务商调用接口不会返回此字段'),
				'alias'                 => Yii::t('app', '别名；第三方仅通讯录应用可获取'),
				'address'               => Yii::t('app', '地址'),
				'extattr'               => Yii::t('app', '扩展属性，第三方仅通讯录应用可获取'),
				'status'                => Yii::t('app', '激活状态: 1=已激活，2=已禁用，4=未激活。已激活代表已激活企业微信或已关注微工作台（原企业号）。未激活代表既未激活企业微信又未关注微工作台（原企业号）'),
				'qr_code'               => Yii::t('app', '员工个人二维码，扫描可添加为外部联系人(注意返回的是一个url，可在浏览器上打开该url以展示二维码)；第三方仅通讯录应用可获取'),
				'is_del'                => Yii::t('app', '0：未删除；1：已删除'),
				'is_external'           => Yii::t('app', '是否具有外部联系人权限 1有 0没有'),
				'new_apply_cnt'         => Yii::t('app', '发起申请数'),
				'new_contact_cnt'       => Yii::t('app', '新增客户数'),
				'negative_feedback_cnt' => Yii::t('app', '删除/拉黑成员的客户数'),
				'chat_cnt'              => Yii::t('app', '聊天总数'),
				'message_cnt'           => Yii::t('app', '发送消息数'),
				'reply_percentage'      => Yii::t('app', '已回复聊天占比'),
				'avg_reply_time'        => Yii::t('app', '平均首次回复时长(分钟)'),
				'openid'                => Yii::t('app', '成员openid'),
				'day_user_num'          => Yii::t('app', '员工单日红包发送次数限制'),
				'day_user_money'        => Yii::t('app', '员工单日红包发送额度限制'),
				'can_send_money'        => Yii::t('app', '是否可发红包1是0否'),
				'dimission_time'        => Yii::t('app', '离职时间'),
				'sop_msg_status'        => Yii::t('app', 'SOP消息免打扰是否开启1是0否'),
				'sop_chat_msg_status'   => Yii::t('app', 'SOP群消息免打扰是否开启1是0否'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getAttachmentStatistics ()
		{
			return $this->hasMany(AttachmentStatistic::className(), ['user_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkChats ()
		{
			return $this->hasMany(WorkChat::className(), ['owner_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkChatInfos ()
		{
			return $this->hasMany(WorkChatInfo::className(), ['user_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkChatStatistics ()
		{
			return $this->hasMany(WorkChatStatistic::className(), ['owner_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkContactWayLines ()
		{
			return $this->hasMany(WorkContactWayLine::className(), ['user_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkContactWayUsers ()
		{
			return $this->hasMany(WorkContactWayUser::className(), ['user_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkExternalContactFollowStatistics ()
		{
			return $this->hasMany(WorkExternalContactFollowStatistic::className(), ['user_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkExternalContactFollowUsers ()
		{
			return $this->hasMany(WorkExternalContactFollowUser::className(), ['user_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkFollowUsers ()
		{
			return $this->hasMany(WorkFollowUser::className(), ['user_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMomentGoods ()
		{
			return $this->hasMany(WorkMomentGoods::className(), ['user_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMomentReplies ()
		{
			return $this->hasMany(WorkMomentReply::className(), ['user_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMomentUserConfigs ()
		{
			return $this->hasMany(WorkMomentUserConfig::className(), ['user_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMoments ()
		{
			return $this->hasMany(WorkMoments::className(), ['user_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditAgrees ()
		{
			return $this->hasMany(WorkMsgAuditAgree::className(), ['user_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfos ()
		{
			return $this->hasMany(WorkMsgAuditInfo::className(), ['user_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfosTo ()
		{
			return $this->hasMany(WorkMsgAuditInfo::className(), ['to_user_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoAgrees ()
		{
			return $this->hasMany(WorkMsgAuditInfoAgree::className(), ['user_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoCalendars ()
		{
			return $this->hasMany(WorkMsgAuditInfoCalendar::className(), ['user_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoCalendarAttendees ()
		{
			return $this->hasMany(WorkMsgAuditInfoCalendarAttendee::className(), ['user_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoCards ()
		{
			return $this->hasMany(WorkMsgAuditInfoCard::className(), ['user_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoDocmsgs ()
		{
			return $this->hasMany(WorkMsgAuditInfoDocmsg::className(), ['user_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoToInfos ()
		{
			return $this->hasMany(WorkMsgAuditInfoToInfo::className(), ['user_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditNoticeRuleInfos ()
		{
			return $this->hasMany(WorkMsgAuditNoticeRuleInfo::className(), ['user_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditUsers ()
		{
			return $this->hasMany(WorkMsgAuditUser::className(), ['user_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkTagGroupStatistics ()
		{
			return $this->hasMany(WorkTagGroupStatistic::className(), ['user_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkTagGroupUserStatistics ()
		{
			return $this->hasMany(WorkTagGroupUserStatistic::className(), ['user_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkTagUsers ()
		{
			return $this->hasMany(WorkTagUser::className(), ['user_id' => 'id']);
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
		public function getWorkUserAuthorRelations ()
		{
			return $this->hasMany(WorkUserAuthorRelation::className(), ['user_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkUserExternalProfiles ()
		{
			return $this->hasMany(WorkUserExternalProfile::className(), ['user_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkWelcomes ()
		{
			return $this->hasMany(WorkWelcome::className(), ['user_id' => 'id']);
		}

        /**
         * @return \yii\db\ActiveQuery
         */
        public function getViolation ()
        {
            return $this->hasMany(InspectionViolation::className(), ['quality_id' => 'id']);
        }

		/**
		 * @param bool $withDepartment
		 *
		 * @return array
		 */
		public function dumpData ($withDepartment = false, $withDepartmmentName = true, $withTagsName = true)
		{
			$result = [
				'id'                => $this->id,
				'key'               => $this->id,
				'corp_id'           => $this->corp_id,
				'userid'            => $this->userid,
				'name'              => $this->name,
				'department'        => $this->department,
				'order'             => $this->order,
				'position'          => $this->position,
				'mobile'            => $this->mobile,
				'gender'            => $this->gender,
				'email'             => $this->email,
				'is_leader_in_dept' => $this->is_leader_in_dept,
				'avatar'            => $this->avatar,
				'thumb_avatar'      => $this->thumb_avatar,
				'telephone'         => $this->telephone,
				'enable'            => $this->enable,
				'alias'             => $this->alias,
				'address'           => $this->address,
				'extattr'           => $this->extattr,
				'status'            => $this->status,
				'qr_code'           => $this->qr_code,
				'is_del'            => $this->is_del,
				'openid'            => $this->openid,
				'is_external'       => $this->is_external,
			];
			if (empty($result['is_external'])) {
				$result['is_external'] = '无';
			} else {
				$result['is_external'] = '有';
			}
			$assign_custom = 0;
			$assign_chat   = 0;
//			if ($this->is_del == 1) {
//				//离职未分配
//				$followUser = WorkExternalContactFollowUser::find()->alias('f')->leftJoin('{{%work_external_contact}} c', '`f`.`external_userid` = `c`.`id`');
//				$followUser = $followUser->where(['f.user_id' => $this->id, 'c.corp_id' => $this->corp_id])->one();
//				if (!empty($followUser)) {
//					$assign_custom = 1;
//				}
//				$workChat = WorkChat::findOne(['owner_id' => $this->id, 'corp_id' => $this->corp_id]);
//				if (!empty($workChat)) {
//					$assign_chat = 1;
//				}
//			}
			$result['assign_custom']       = $assign_custom;
			$result['assign_chat']         = $assign_chat;
			$result['apply_num']           = $this->new_apply_cnt;
			$result['new_customer']        = $this->new_contact_cnt;
			$result['chat_num']            = $this->chat_cnt;
			$result['message_num']         = $this->message_cnt;
			$result['replyed_per']         = !empty($this->reply_percentage) ? ($this->reply_percentage * 100) . '%' : '--';
			$result['first_reply_time']    = !empty($this->avg_reply_time) ? $this->avg_reply_time . '分钟' : '--';
			$result['delete_customer_num'] = $this->negative_feedback_cnt;
			if ($withDepartmmentName) {
				$departName = WorkDepartment::getDepartNameByUserId($this->department, $this->corp_id);
			} else {
				$departName = '';
			}
			$result['department_name'] = $departName;

			if ($withTagsName) {
				$tagName = WorkTagContact::getTagNameByContactId($this->id, 1, 0, [], $this->corp_id);
			} else {
				$tagName = [];
			}
			$result['tag_name'] = $tagName;

			if ($withDepartment) {
				$result['department_info'] = [];
				if (!empty($this->department)) {
					$departmentIdData = explode(',', $this->department);
					if (!empty($departmentIdData)) {
						foreach ($departmentIdData as $departmentId) {
							$department = WorkDepartment::findOne(['corp_id' => $this->corp_id, 'department_id' => $departmentId]);

							if (!empty($department)) {
								array_push($result['department_info'], $department->dumpData());
							}
						}
					}
				}
			}

			return $result;
		}

		/**
		 * @return array
		 */
		public function dumpMiniData ()
		{
			return [
				'name'         => $this->name,
				'mobile'       => $this->mobile,
				'gender'       => $this->gender,
				'email'        => $this->email,
				'avatar'       => $this->avatar,
				'thumb_avatar' => $this->thumb_avatar,
			];
		}

		/**
		 * @param $authCorpId
		 * @param $userId
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function getUserSuite ($authCorpId, $userId)
		{
			$authCorp = WorkCorp::findOne($authCorpId);

			if (empty($authCorp)) {
				throw new InvalidDataException('参数不正确。');
			}

			$workApi    = WorkUtils::getWorkApi($authCorpId);
			$workUserId = 0;

			if (!empty($workApi)) {
				$userInfo     = $workApi->userGet($userId);
				$userInfoData = SUtils::Object2Array($userInfo);
				$workUserId   = static::setUser($authCorp->id, $userInfoData);
			}

			return $workUserId;
		}

		/**
		 * @param bool $refresh
		 */
		public function getUserOpenid ($refresh = false)
		{
			if ($refresh || empty($this->openid)) {
				try {
					$workApi = WorkUtils::getWorkApi($this->corp_id);
				} catch (\Exception $e) {
					Yii::error($this->userid, 'getUserOpenid-userid');
					Yii::error($e->getMessage(), 'getUserOpenid-getWorkApi');
				}

				if (!empty($workApi)) {
					try {
						$workApi->userConvertToOpenid($this->userid, $openid);

						if ($this->openid != $openid) {
							$this->openid = $openid;

							$this->save();
						}
					} catch (\Exception $e) {
						Yii::error($this->userid, 'getUserOpenid-userid');
						Yii::error($e->getMessage(), 'getUserOpenid-getWorkApi');
					}
				}
			}
		}

		/**
		 * @param $corpId
		 * @param $userInfo
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function setUser ($corpId, $userInfo)
		{
			$workUser = static::findOne(['corp_id' => $corpId, 'userid' => $userInfo['userid']]);

			if (empty($workUser)) {
				$workUser          = new WorkUser();
				$workUser->corp_id = $corpId;
				$workUser->is_del  = self::USER_NO_DEL;
			}

			$workUser->userid = isset($userInfo['newuserid']) ? $userInfo['newuserid'] : $userInfo['userid'];

			if (isset($userInfo['name'])) {
				$workUser->name = !empty($userInfo['name']) ? $userInfo['name'] : '';
			}

			if (isset($userInfo['department'])) {
				$workUser->department = !empty($userInfo['department']) ? (is_array($userInfo['department']) ? implode(',', $userInfo['department']) : $userInfo['department']) : '';
			}

			if (isset($userInfo['order'])) {
				$workUser->order = is_array($userInfo['order']) ? implode(',', $userInfo['order']) : $userInfo['order'];
			}

			if (isset($userInfo['position'])) {
				$workUser->position = !empty($userInfo['position']) ? $userInfo['position'] : '';
			}

			if (isset($userInfo['mobile'])) {
				$workUser->mobile = !empty($userInfo['mobile']) ? $userInfo['mobile'] : '';
			}

			if (isset($userInfo['gender'])) {
				$workUser->gender = $userInfo['gender'];
			}

			if (isset($userInfo['email'])) {
				$workUser->email = !empty($userInfo['email']) ? $userInfo['email'] : '';
			}

			if (isset($userInfo['is_leader_in_dept'])) {
				$workUser->is_leader_in_dept = !empty($userInfo['is_leader_in_dept']) ? (is_array($userInfo['is_leader_in_dept']) ? implode(',', $userInfo['is_leader_in_dept']) : $userInfo['is_leader_in_dept']) : '';
			}

			if (isset($userInfo['isleaderindept'])) {
				$workUser->is_leader_in_dept = !empty($userInfo['isleaderindept']) ? (is_array($userInfo['isleaderindept']) ? implode(',', $userInfo['isleaderindept']) : $userInfo['isleaderindept']) : '';
			}

			if (isset($userInfo['avatar'])) {
				$workUser->avatar = !empty($userInfo['avatar']) ? $userInfo['avatar'] : '';
			}

			if (isset($userInfo['thumb_avatar'])) {
				$workUser->thumb_avatar = !empty($userInfo['thumb_avatar']) ? $userInfo['thumb_avatar'] : '';
			}

			if (isset($userInfo['telephone'])) {
				$workUser->telephone = !empty($userInfo['telephone']) ? $userInfo['telephone'] : '';
			}

			if (isset($userInfo['enable'])) {
				$workUser->enable = $userInfo['enable'];
			}

			if (isset($userInfo['alias'])) {
				$workUser->alias = !empty($userInfo['alias']) ? $userInfo['alias'] : '';
			}

			if (isset($userInfo['address'])) {
				$workUser->address = !empty($userInfo['address']) ? $userInfo['address'] : '';
			}

			if (isset($userInfo['extattr']) && isset($userInfo['extattr']['item'])) {
				$workUser->extattr = Json::encode($userInfo['extattr']['item']);
			}

			if (isset($userInfo['extattr']) && isset($userInfo['extattr']['attrs'])) {
				$workUser->extattr = Json::encode($userInfo['extattr']['attrs']);
			}

			if (isset($userInfo['status'])) {
				$workUser->status = $userInfo['status'];
			}

			if (isset($userInfo['qr_code'])) {
				$workUser->qr_code = !empty($userInfo['qr_code']) ? $userInfo['qr_code'] : '';
			}

			if ($workUser->dirtyAttributes) {
				if (!$workUser->validate() || !$workUser->save()) {
					throw new InvalidDataException(SUtils::modelError($workUser));
				}
			}

			if (isset($userInfo['external_position']) || isset($userInfo['external_profile'])) {
				$externalProfile = [];

				if (isset($userInfo['external_profile'])) {
					$externalProfile = $userInfo['external_profile'];
				}

				if (isset($userInfo['external_position'])) {
					$externalProfile['external_position'] = $userInfo['external_position'];
				}

				WorkUserExternalProfile::setExternalProfile($workUser->id, $externalProfile);
			}

			//添加到子账户中
			$sub_user['work_uid'] = $workUser->id;

			if (!empty($workUser->mobile)) {
				$sub_user['account'] = $workUser->mobile;
			}

			if (!empty($workUser->name)) {
				$sub_user['name'] = $workUser->name;
			}

			if (!empty($workUser->gender)) {
				$sub_user['sex'] = $workUser->gender;
			}

			SubUser::add($sub_user, 1);

			return $workUser->id;
		}

		/**
		 * 获取联系客户统计数据 todo 后面会存单独的表中
		 *
		 * @param int   $corpId
		 * @param array $user_id 如果多个user_id则返回的各个参数的总和
		 * @param array $party_id
		 * @param int   $start_time
		 * @param int   $end_time
		 *
		 * @return boolean
		 *
		 * @throws InvalidDataException
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function getStatistic ($corpId, array $user_id, array $party_id, $start_time, $end_time)
		{
			if (empty($user_id) && empty($party_id)) {
				throw new InvalidDataException('参数不正确。');
			}

			$authCorp = WorkCorp::findOne($corpId);

			if (empty($authCorp)) {
				throw new InvalidDataException('参数不正确。');
			}

			$workApi = WorkUtils::getWorkApi($corpId, WorkUtils::EXTERNAL_API);

			if (!empty($workApi)) {
				$behavior['userid']     = $user_id;
				$behavior['partyid']    = $party_id;
				$behavior['start_time'] = $start_time;
				$behavior['end_time']   = $end_time;
				$statisticData          = $workApi->ECGetUserBeheviorData($behavior);
				$data                   = SUtils::Object2Array($statisticData);
				\Yii::error($data, '$data');

				return true;
			} else {
				return false;
			}
		}

		/**
		 * 获取成员客户统计数据
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
		public static function getUserDayStatistic ($type = 0)
		{
			//企业微信号
			$work_corp = WorkCorp::find()->select('id,corpid')->where('corpid != \'\' AND corp_type != \'\'')->asArray()->all();
			if ($type == 1) {
				$etime = strtotime(date('Y-m-d')) - 86400;
				$stime = $etime - 29 * 86400;
			} else {
				$etime = strtotime(date('Y-m-d'));
				$stime = $etime - 86400;
			}

			/*$work_user_statistic = WorkUserStatistic::findOne(['time' => $stime]);
			if (!empty($work_user_statistic)) {
				throw new InvalidDataException('重复请求。');
			}*/
			foreach ($work_corp as $k => $v) {
				$data           = [];
				$data['corpId'] = $v['id'];
				$data['sTime']  = $stime;
				$data['eTime']  = $etime;
				$data['type']   = $type;
				\Yii::error($data, '$data');
				\Yii::$app->queue->push(new WorkUserStatisticJob($data));
			}

			return true;
		}

		public static function getOldData ()
		{
			ini_set('memory_limit', '4096M');
			set_time_limit(0);
			//11 12 13 14 15
			for ($i = 1; $i <= 5; $i++) {
				$sTime  = '';
				$eTime  = '';
				$corpId = [];
				switch ($i) {
					case 1;
						$sTime  = '1605024000';
						$eTime  = '1605110400';
						$corpId = [885, 892, 893, 931, 948, 950, 953, 961, 962, 963, 965, 969, 972, 975];
						break;
					case 2:
						$sTime  = '1605110400';
						$eTime  = '1605196800';
						$corpId = [885, 895, 931, 932, 950, 953, 962, 963, 964, 975, 976, 985, 987];
						break;
					case 3:
						$sTime  = '1605196800';
						$eTime  = '1605283200';
						$corpId = [913, 931, 953, 962, 963, 964, 972, 986, 987, 992, 1001, 1002];
						break;
					case 4:
						$sTime  = '1605283200';
						$eTime  = '1605369600';
						$corpId = [964, 983, 987, 1002];
						break;
					case 5:
						$sTime  = '1605369600';
						$eTime  = '1605456000';
						$corpId = [871, 876, 885, 931, 953, 987, 1002];
						break;
				}
				if (!empty($corpId)) {
					foreach ($corpId as $val) {

						$workUserData = static::find()->alias('w');
						$workUserData = $workUserData->leftJoin('{{%work_follow_user}} wf', 'w.id=wf.user_id');
						$workUserData = $workUserData->andWhere(['w.corp_id' => $val, 'w.status' => 1, 'wf.status' => 1]);
						$workUser     = $workUserData->select('w.id,w.userid,w.new_apply_cnt,w.new_contact_cnt,w.negative_feedback_cnt,w.chat_cnt,w.message_cnt,w.reply_percentage,w.avg_reply_time')->orderBy(['w.id' => SORT_ASC])->all();
						if (!empty($workUser)) {
							try {
								$workApi = WorkUtils::getWorkApi($val, WorkUtils::EXTERNAL_API);
							} catch (\Exception $e) {
								Yii::error($e->getMessage(), 'work-user-getApi');
							}
							$stime = $sTime;
							$etime = $eTime;
							foreach ($workUser as $kk => $vv) {
								if (!empty($workApi)) {
									try {
										$behavior               = [];
										$behavior['userid'][]   = $vv->userid;
										$behavior['start_time'] = $stime;
										$behavior['end_time']   = $etime;

										$statisticData = $workApi->ECGetUserBeheviorData($behavior);
										$sData         = SUtils::Object2Array($statisticData);
										if (!empty($sData)) {
											foreach ($sData as $yData) {
												if ((!empty($yData['chat_cnt']) || !empty($yData['message_cnt']) || !empty($yData['reply_percentage']) || !empty($yData['avg_reply_time']) || !empty($yData['negative_feedback_cnt']) || !empty($yData['new_apply_cnt']) || !empty($yData['new_contact_cnt'])) && $yData['stat_time'] == $sTime) {
													$workUserStatistic = WorkUserStatistic::findOne(['corp_id' => $val, 'userid' => $vv->userid, 'time' => $stime]);
													if (empty($workUserStatistic)) {
														$workUserStatistic                        = new WorkUserStatistic();
														$workUserStatistic->corp_id               = $val;
														$workUserStatistic->userid                = $vv->userid;
														$workUserStatistic->new_apply_cnt         = $yData['new_apply_cnt'];
														$workUserStatistic->new_contact_cnt       = $yData['new_contact_cnt'];
														$workUserStatistic->negative_feedback_cnt = $yData['negative_feedback_cnt'];
														$workUserStatistic->chat_cnt              = $yData['chat_cnt'];
														$workUserStatistic->message_cnt           = $yData['message_cnt'];
														$workUserStatistic->reply_percentage      = strval($yData['reply_percentage']);
														$workUserStatistic->avg_reply_time        = strval($yData['avg_reply_time']);
														$workUserStatistic->time                  = $yData['stat_time'];
														$workUserStatistic->data_time             = date('Y-m-d', $yData['stat_time']);
														$workUserStatistic->create_time           = date('Y-m-d H:i:s', $eTime);

														if (!$workUserStatistic->save()) {
															\Yii::error(SUtils::modelError($workUserStatistic), 'workUserStatistic_error');
														} else {
															\Yii::error($workUserStatistic->id, 'workUserStatistic');
														}

														if (!empty($yData['new_apply_cnt'])) {
															$new_apply_cnt     = $vv->new_apply_cnt;
															$vv->new_apply_cnt = $new_apply_cnt + $yData['new_apply_cnt'];
														}
														if (!empty($yData['new_contact_cnt'])) {
															$new_contact_cnt     = $vv->new_contact_cnt;
															$vv->new_contact_cnt = $new_contact_cnt + $yData['new_contact_cnt'];
														}
														if (!empty($yData['negative_feedback_cnt'])) {
															$negative_feedback_cnt     = $vv->negative_feedback_cnt;
															$vv->negative_feedback_cnt = $negative_feedback_cnt + $yData['negative_feedback_cnt'];
														}
														if (!empty($yData['chat_cnt'])) {
															$chat_cnt     = $vv->chat_cnt;
															$vv->chat_cnt = $chat_cnt + $yData['chat_cnt'];
														}
														if (!empty($yData['message_cnt'])) {
															$message_cnt     = $vv->message_cnt;
															$vv->message_cnt = $message_cnt + $yData['message_cnt'];
														}

														if (!empty($yData['reply_percentage'])) {
															$reply_percentage     = WorkUserStatistic::getReplyPercentage($corpId, $vv->userid, 1);
															$vv->reply_percentage = strval($reply_percentage);
														}

														if (!empty($yData['avg_reply_time'])) {
															$avg_reply_time     = WorkUserStatistic::getReplyPercentage($corpId, $vv->userid, 2);
															$vv->avg_reply_time = strval($avg_reply_time);
														}

														$vv->save();
													}

												}

											}

										}
									} catch (\Exception $e) {
										Yii::error($e->getMessage(), 'workUser-exception');
									}
								}
							}
						}


					}
					\Yii::error($sTime, '$sTime');
					\Yii::error($eTime, '$eTime');
					\Yii::error($i, '$i');
				}
			}
		}

		/**
		 * @param $corpId
		 * @param $userId
		 *
		 * @return int
		 */
		public static function getUserId ($corpId, $userId)
		{
			$workUserId = 0;

			$workUser = WorkUser::findOne(['corp_id' => $corpId, 'userid' => $userId, 'is_del' => WorkUser::USER_NO_DEL]);
			if (empty($workUser)) {
				try {
					$workUserId = WorkUser::getUserSuite($corpId, $userId);
				} catch (\Exception $e) {
					\Yii::error($e->getMessage(), __CLASS__ . '-' . __FUNCTION__);
				}
			} else {
				$workUserId = $workUser->id;
				$workUser->getUserOpenid();
			}

			return $workUserId;
		}

		//获取成员所在部门的负责人
		public static function getLeaderUserId ($userId)
		{
			$leaderUserIdArr = [];
			$workUser        = WorkUser::findOne($userId);
			if (empty($workUser)) {
				return $leaderUserIdArr;
			}
			$departArr = explode(',', $workUser->department);
			$leadArr   = explode(',', $workUser->is_leader_in_dept);
			$restPart  = [];//剩余部门
			foreach ($departArr as $key => $depart) {
				if (!empty($leadArr[$key])) {
					array_push($leaderUserIdArr, $workUser->id);
				} else {
					array_push($restPart, $depart);
				}
			}
			if (empty($restPart)) {
				return $leaderUserIdArr;
			}

			foreach ($restPart as $part) {
				$userList = WorkUser::find()->where(['corp_id' => $workUser->corp_id])->andWhere("find_in_set ($part,department)")->andWhere("find_in_set (1,is_leader_in_dept)")->andWhere(['!=', 'id', $userId])->all();
				/**@var WorkUser $user * */
				foreach ($userList as $user) {
					\Yii::error('1', 'user');
					$tempDep = explode(',', $user->department);
					$temLead = explode(',', $user->is_leader_in_dept);
					$tempKey = array_search($part, $tempDep);
					if (!empty($temLead[$tempKey])) {
						array_push($leaderUserIdArr, $user->id);
					}
				}
			}

			return array_unique($leaderUserIdArr);
		}

		/**
		 * @param int $parentId
		 * @param     $corpId
		 * @param     $name
		 * @param     $departments
		 * @param     $userId
		 * @param     $showDepart 1不返回部门
		 * @param     $otherData  其他参数
		 *
		 * @return array
		 *
		 */
		public static function getAllDepartmentUser ($parentId = 0, $corpId, $name = '', $departments = [], $userId = [], $showDepart = 0, $otherData = [])
		{
			$letterA    = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
			$letterB    = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '#'];
			$letterData = [];
			foreach ($letterB as $key => $let) {
				$letterData[$key]['letter'] = $let;
				$letterData[$key]['data']   = '';
			}
			$department = [];
			if (empty($showDepart)) {
				$parents = WorkDepartment::findOne(['parentid' => NULL, 'is_del' => 0, 'corp_id' => $corpId]);
				if (empty($parentId)) {
					$parentId = $parents->department_id;
				} else {
					$departId = explode('-', $parentId);
					$parentId = $departId[1];
				}
				$parents = WorkDepartment::find()->where(['parentid' => $parentId, 'is_del' => 0, 'corp_id' => $corpId]);
				if (!empty($name)) {
					$parents = $parents->andWhere('name like \'%' . $name . '%\'');
				}
				$parents = $parents->all();
				if (!empty($parents)) {
					$i = 0;
					/** @var WorkDepartment $depart */
					foreach ($parents as $key => $depart) {
						if (!empty($departments) && !in_array($depart['department_id'], $departments)) {
							continue;
						}
						$department[$i]['id']     = strval('d-' . $depart->department_id);
						$department[$i]['name']   = $depart->name;
						$department[$i]['depart'] = 1;
						$department[$i]['avatar'] = \Yii::$app->params['site_url'] . '/static/image/department.png';
						$i++;
					}
				}
				//$workDepart   = WorkDepartment::findOne(['department_id'=>$parentId]);
				$departmentId = $parentId;
			} else {
				$parents      = WorkDepartment::findOne(['parentid' => NULL, 'is_del' => 0, 'corp_id' => $corpId]);
				$departmentId = $parents->department_id;
			}
			$users = WorkUser::find()->andWhere("find_in_set ($departmentId,department)")->andWhere(['corp_id' => $corpId, 'is_del' => 0]);
			if (!empty($otherData['is_external'])) {
				$users = $users->andWhere(['is_external' => 1]);
			}
			if (!empty($name)) {
				$users = WorkUser::find()->where(['corp_id' => $corpId, 'is_del' => 0]);
				$users = $users->andWhere('name like \'%' . $name . '%\'');
			} else {
				$users = WorkUser::find()->where("find_in_set ($departmentId,department)")->andWhere(['corp_id' => $corpId, 'is_del' => 0]);
			}
			$users = $users->andWhere(["!=", "status", 4]);
			$users = $users->select('id,name,avatar')->asArray()->all();
			if (!empty($users)) {
				foreach ($users as $key => $val) {
					if (!empty($userId) && !in_array($val['id'], $userId)) {
						continue;
					}
					$char = SUtils::getFirstChar(trim($val['name']));
					if (in_array($char, $letterA)) {
						foreach ($letterData as $le => $lb) {
							$sData = [];
							if ($lb['letter'] == $char) {
								$sData[0]['id']     = strval($val['id']);
								$sData[0]['name']   = $val['name'];
								$sData[0]['avatar'] = $val['avatar'];
								$sData[0]['depart'] = 0;
							}
							if (!empty($sData)) {
								if (empty($letterData[$le]['data'])) {
									$letterData[$le]['data'] = $sData;
								} else {
									$cc                           = count($letterData[$le]['data']);
									$letterData[$le]['data'][$cc] = [
										'id'     => strval($sData[0]['id']),
										'name'   => $sData[0]['name'],
										'avatar' => $sData[0]['avatar'],
										'depart' => 0,
									];
								}
							}
						}

					} else {
						$jData              = [];
						$jData[0]['id']     = strval($val['id']);
						$jData[0]['name']   = $val['name'];
						$jData[0]['avatar'] = $val['avatar'];
						$jData[0]['depart'] = 0;
						if (!empty($jData)) {
							if (empty($letterData[26]['data'])) {
								$letterData[26]['data'] = $jData;
							} else {
								$cc                          = count($letterData[26]['data']);
								$letterData[26]['data'][$cc] = [
									'id'     => strval($jData[0]['id']),
									'name'   => $jData[0]['name'],
									'avatar' => $jData[0]['avatar'],
									'depart' => 0,
								];
							}
						}
					}
				}
			}
			$userData = [];
			if (!empty($letterData)) {
				if (!empty($department)) {
					$userData[0]['letter'] = '部门';
					$userData[0]['data']   = $department;
					$i                     = 1;
				} else {
					$i = 0;
				}
				foreach ($letterData as $data) {
					if (!empty($data['data'])) {
						$userData[$i]['letter'] = $data['letter'];
						$userData[$i]['data']   = $data['data'];
						$i++;
					}
				}
			}

			return [
				'users' => $userData,
			];

		}

		/**
		 * 获取当前选择部门的成员
		 *
		 * @param $corpId
		 * @param $userIds
		 *
		 * @return array
		 *
		 */
		public static function getDepartUser ($corpId, $userIds)
		{
			$uIds = [];
			foreach ($userIds as $uid) {
				if (strpos($uid, 'd') !== false) {
					$arr    = explode('-', $uid);
					$result = WorkDepartment::getSubDepart($arr[1], $corpId, []);
					if (!empty($result)) {
						//有子部门
						array_push($result, $arr[1]);
						foreach ($result as $val) {
							$workUser = WorkUser::find()->where(['corp_id' => $corpId, 'is_del' => 0])->andWhere(["!=","status",4])->andWhere("find_in_set ('" . $val . "',department)")->select('id')->asArray()->all();
							if (!empty($workUser)) {
								foreach ($workUser as $val) {
									array_push($uIds, $val['id']);
								}
							}
						}
					}
					$depart = WorkDepartment::findOne(['department_id' => $arr[1]]);
					if (!empty($depart)) {
						$workUser = WorkUser::find()->where(['corp_id' => $corpId, 'is_del' => 0])->andWhere(["!=","status",4])->andWhere("find_in_set ('" . $depart->department_id . "',department)")->select('id')->asArray()->all();
						if (!empty($workUser)) {
							foreach ($workUser as $val) {
								array_push($uIds, $val['id']);
							}
						}
					}

				} else {
					array_push($uIds, $uid);
				}
			}

			return $uIds;
		}

	}
