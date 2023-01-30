<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\queue\SyncWorkDepartmentListJob;
	use app\queue\SyncWorkExternalContactJob;
	use app\util\DateUtil;
	use app\util\SUtils;
	use app\util\WorkUtils;
	use Yii;
	use yii\db\Exception;

	/**
	 * This is the model class for table "{{%work_corp}}".
	 *
	 * @property int                                  $id
	 * @property string                               $corpid                 授权方企业微信id
	 * @property string                               $corp_name              授权方企业名称，即企业简称
	 * @property string                               $state                  企业微信唯一标识
	 * @property string                               $corp_type              授权方企业类型，认证号：verified, 注册号：unverified
	 * @property string                               $corp_square_logo_url   授权方企业方形头像
	 * @property int                                  $corp_user_max          授权方企业用户规模
	 * @property int                                  $corp_agent_max         授权方企业应用数上限
	 * @property string                               $corp_full_name         授权方企业的主体名称(仅认证或验证过的企业有)，即企业全称。
	 * @property string                               $verified_end_time      企业类型，1. 企业; 2. 政府以及事业单位; 3. 其他组织, 4.团队号
	 * @property int                                  $subject_type           认证到期时间
	 * @property string                               $corp_wxqrcode          授权企业在微工作台（原企业号）的二维码，可用于关注微工作台
	 * @property string                               $corp_scale             企业规模。当企业未设置该属性时，值为空
	 * @property string                               $corp_industry          企业所属行业。当企业未设置该属性时，值为空
	 * @property string                               $corp_sub_industry      企业所属子行业。当企业未设置该属性时，值为空
	 * @property string                               $location               企业所在地信息, 为空时表示未知
	 * @property int                                  $sync_user_time         最后一次同步通讯录时间
	 * @property int                                  $last_tag_time          最后一次同步企业微信标签
	 * @property int                                  $last_customer_tag_time 最后一次同步客户标签
	 * @property string                               $day_sum_money          单日红包额度
	 * @property int                                  $day_external_num       客户单日红包次数
	 * @property string                               $day_external_money     客户单日红包额度
	 * @property int                                  $unshare_chat           不共享所在群1是0否
	 * @property int                                  $unshare_follow         不共享跟进记录1是0否
	 * @property int                                  $unshare_line           不共享互动轨迹1是0否
	 * @property int                                  $unshare_field          不共享客户画像1是0否
	 * @property int                                  $is_return              同一客户归属多个员工跟进时，是否能退回公海池
	 * @property int                                  $is_sea_info            是否同步非企微用户画像
	 * @property int                                  $is_sea_tag             是否同步非企微用户标签
	 * @property int                                  $is_sea_follow          是否同步非企微用户跟进记录
	 * @property int                                  $is_sea_phone           是否同步非企微用户通话记录
	 * @property string                               $create_time            创建时间
	 * @property int                               $important_customer_recycle_switch       客户导入分配超时回收用户开关 0 关闭 1 开启
	 * @property int                               $important_customer_recycle_time         客户导入分配超时天数，默认7天
	 *
	 * @property AttachmentStatistic[]                $attachmentStatistics
	 * @property LimitWordMsg[]                       $limitWordMsgs
	 * @property LimitWordRemind[]                    $limitWordReminds
	 * @property LimitWordTimes[]                     $limitWordTimes
	 * @property RedPack[]                            $redPacks
	 * @property UserCorpRelation[]                   $userCorpRelations
	 * @property WorkChat[]                           $workChats
	 * @property WorkChatContactWay[]                 $workChatContactWays
	 * @property WorkChatContactWayGroup[]            $workChatContactWayGroups
	 * @property WorkChatGroup[]                      $workChatGroups
	 * @property WorkChatRemind[]                     $workChatReminds
	 * @property WorkChatRemindSend[]                 $workChatRemindSends
	 * @property WorkChatStatistic[]                  $workChatStatistics
	 * @property WorkChatWelcome[]                    $workChatWelcomes
	 * @property WorkContactWay[]                     $workContactWays
	 * @property WorkContactWayBaidu[]                $workContactWayBaidus
	 * @property WorkContactWayBaiduGroup[]           $workContactWayBaiduGroups
	 * @property WorkContactWayGroup[]                $workContactWayGroups
	 * @property WorkCorpAgent[]                      $workCorpAgents
	 * @property WorkCorpAuth[]                       $workCorpAuths
	 * @property WorkCorpBind                         $workCorpBind
	 * @property WorkDepartment[]                     $workDepartments
	 * @property WorkExternalContact[]                $workExternalContacts
	 * @property WorkExternalContactFollowStatistic[] $workExternalContactFollowStatistics
	 * @property WorkFollowMsgSending[]               $workFollowMsgSendings
	 * @property WorkFollowUser[]                     $workFollowUsers
	 * @property WorkGroupSending[]                   $workGroupSendings
	 * @property WorkMaterial[]                       $workMaterials
	 * @property WorkMomentSetting[]                  $workMomentSettings
	 * @property WorkMomentUserConfig[]               $workMomentUserConfigs
	 * @property WorkMoments[]                        $workMoments
	 * @property WorkMsgAudit                         $workMsgAudit
	 * @property WorkTag[]                            $workTags
	 * @property WorkTagGroup[]                       $workTagGroups
	 * @property WorkTagPullGroup[]                   $workTagPullGroups
	 * @property WorkUser[]                           $workUsers
	 * @property WorkUserAuthorRelation[]             $workUserAuthorRelations
	 * @property WorkWelcome[]                        $workWelcomes
	 */
	class WorkCorp extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_corp}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_square_logo_url'], 'string'],
				[['corp_user_max', 'corp_agent_max', 'subject_type', 'day_external_num', 'unshare_chat', 'unshare_follow', 'unshare_line', 'unshare_field'], 'integer'],
				[['create_time'], 'safe'],
				[['corpid'], 'string', 'max' => 64],
				[['state', 'corp_type'], 'string', 'max' => 16],
				[['corp_name', 'corp_full_name', 'corp_wxqrcode', 'corp_scale', 'corp_industry', 'corp_sub_industry', 'location'], 'string', 'max' => 255],
				[['day_sum_money', 'day_external_money'], 'number'],
				[['important_customer_recycle_switch', 'important_customer_recycle_time'], 'integer'],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'                     => Yii::t('app', 'ID'),
				'corpid'                 => Yii::t('app', '授权方企业微信id'),
				'corp_name'              => Yii::t('app', '授权方企业名称，即企业简称'),
				'state'                  => Yii::t('app', '企业微信唯一标识'),
				'corp_type'              => Yii::t('app', '授权方企业类型，认证号：verified, 注册号：unverified'),
				'corp_square_logo_url'   => Yii::t('app', '授权方企业方形头像'),
				'corp_user_max'          => Yii::t('app', '授权方企业用户规模'),
				'corp_agent_max'         => Yii::t('app', '授权方企业应用数上限'),
				'corp_full_name'         => Yii::t('app', '授权方企业的主体名称(仅认证或验证过的企业有)，即企业全称。'),
				'verified_end_time'      => Yii::t('app', '企业类型，1. 企业; 2. 政府以及事业单位; 3. 其他组织, 4.团队号'),
				'subject_type'           => Yii::t('app', '认证到期时间'),
				'corp_wxqrcode'          => Yii::t('app', '授权企业在微工作台（原企业号）的二维码，可用于关注微工作台'),
				'corp_scale'             => Yii::t('app', '企业规模。当企业未设置该属性时，值为空'),
				'corp_industry'          => Yii::t('app', '企业所属行业。当企业未设置该属性时，值为空'),
				'corp_sub_industry'      => Yii::t('app', '企业所属子行业。当企业未设置该属性时，值为空'),
				'location'               => Yii::t('app', '企业所在地信息, 为空时表示未知'),
				'sync_user_time'         => Yii::t('app', '最后一次同步通讯录时间'),
				'last_tag_time'          => Yii::t('app', '最后一次同步企业微信标签'),
				'last_customer_tag_time' => Yii::t('app', '最后一次同步客户标签'),
				'day_sum_money'          => Yii::t('app', '单日红包额度'),
				'day_external_num'       => Yii::t('app', '客户单日红包次数'),
				'day_external_money'     => Yii::t('app', '客户单日红包额度'),
				'unshare_chat'           => Yii::t('app', '不共享所在群1是0否'),
				'unshare_follow'         => Yii::t('app', '不共享跟进记录1是0否'),
				'unshare_line'           => Yii::t('app', '不共享互动轨迹1是0否'),
				'unshare_field'          => Yii::t('app', '不共享客户画像1是0否'),
				'is_return'              => Yii::t('app', '同一客户归属多个员工跟进时，是否能退回公海池'),
				'is_sea_info'            => Yii::t('app', '是否同步非企微用户画像'),
				'is_sea_tag'             => Yii::t('app', '是否同步非企微用户标签'),
				'is_sea_follow'          => Yii::t('app', '是否同步非企微用户跟进记录'),
				'is_sea_phone'           => Yii::t('app', '是否同步非企微用户通话记录'),
				'create_time'            => Yii::t('app', '创建时间'),
				'important_customer_recycle_switch' => Yii::t('app', '客户导入分配超时回收开关'),
				'important_customer_recycle_time'   => Yii::t('app', '客户导入分配超时天数'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getAttachmentStatistics ()
		{
			return $this->hasMany(AttachmentStatistic::className(), ['corp_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getLimitWordMsgs ()
		{
			return $this->hasMany(LimitWordMsg::className(), ['corp_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getLimitWordReminds ()
		{
			return $this->hasMany(LimitWordRemind::className(), ['corp_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getLimitWordTimes ()
		{
			return $this->hasMany(LimitWordTimes::className(), ['corp_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getRedPacks ()
		{
			return $this->hasMany(RedPack::className(), ['corp_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getUserCorpRelations ()
		{
			return $this->hasMany(UserCorpRelation::className(), ['corp_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkChats ()
		{
			return $this->hasMany(WorkChat::className(), ['corp_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkChatContactWays ()
		{
			return $this->hasMany(WorkChatContactWay::className(), ['corp_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkChatContactWayGroups ()
		{
			return $this->hasMany(WorkChatContactWayGroup::className(), ['corp_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkChatGroups ()
		{
			return $this->hasMany(WorkChatGroup::className(), ['corp_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkChatReminds ()
		{
			return $this->hasMany(WorkChatRemind::className(), ['corp_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkChatRemindSends ()
		{
			return $this->hasMany(WorkChatRemindSend::className(), ['corp_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkChatStatistics ()
		{
			return $this->hasMany(WorkChatStatistic::className(), ['corp_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkChatWelcomes ()
		{
			return $this->hasMany(WorkChatWelcome::className(), ['corp_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkContactWays ()
		{
			return $this->hasMany(WorkContactWay::className(), ['corp_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkContactWayBaidus ()
		{
			return $this->hasMany(WorkContactWayBaidu::className(), ['corp_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkContactWayBaiduGroups ()
		{
			return $this->hasMany(WorkContactWayBaiduGroup::className(), ['corp_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkContactWayGroups ()
		{
			return $this->hasMany(WorkContactWayGroup::className(), ['corp_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkCorpAgents ()
		{
			return $this->hasMany(WorkCorpAgent::className(), ['corp_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkCorpAuths ()
		{
			return $this->hasMany(WorkCorpAuth::className(), ['corp_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkCorpBind ()
		{
			return $this->hasOne(WorkCorpBind::className(), ['corp_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkDepartments ()
		{
			return $this->hasMany(WorkDepartment::className(), ['corp_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkExternalContacts ()
		{
			return $this->hasMany(WorkExternalContact::className(), ['corp_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkExternalContactFollowStatistics ()
		{
			return $this->hasMany(WorkExternalContactFollowStatistic::className(), ['corp_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkFollowMsgSendings ()
		{
			return $this->hasMany(WorkFollowMsgSending::className(), ['corp_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkFollowUsers ()
		{
			return $this->hasMany(WorkFollowUser::className(), ['corp_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkGroupSendings ()
		{
			return $this->hasMany(WorkGroupSending::className(), ['corp_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMaterials ()
		{
			return $this->hasMany(WorkMaterial::className(), ['corp_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMomentSettings ()
		{
			return $this->hasMany(WorkMomentSetting::className(), ['corp_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMomentUserConfigs ()
		{
			return $this->hasMany(WorkMomentUserConfig::className(), ['corp_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMoments ()
		{
			return $this->hasMany(WorkMoments::className(), ['corp_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAudit ()
		{
			return $this->hasOne(WorkMsgAudit::className(), ['corp_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkTags ()
		{
			return $this->hasMany(WorkTag::className(), ['corp_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkTagGroups ()
		{
			return $this->hasMany(WorkTagGroup::className(), ['corp_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkTagPullGroups ()
		{
			return $this->hasMany(WorkTagPullGroup::className(), ['corp_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkUsers ()
		{
			return $this->hasMany(WorkUser::className(), ['corp_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkUserAuthorRelations ()
		{
			return $this->hasMany(WorkUserAuthorRelation::className(), ['corp_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkWelcomes ()
		{
			return $this->hasMany(WorkWelcome::className(), ['corp_id' => 'id']);
		}

		/**
		 * @param bool $withConfig
		 * @param bool $withAuth
		 *
		 * @return array
		 */
		public function dumpData ($withConfig = false, $withAuth = false)
		{
			$result = [
				'corpid'               => $this->corpid,
				'corp_name'            => $this->corp_name,
				'state'                => $this->state,
				'corp_type'            => $this->corp_type,
				'corp_square_logo_url' => $this->corp_square_logo_url,
				'corp_user_max'        => $this->corp_user_max,
				'corp_agent_max'       => $this->corp_agent_max,
				'corp_full_name'       => $this->corp_full_name,
				'verified_end_time'    => $this->verified_end_time,
				'subject_type'         => $this->subject_type,
				'corp_wxqrcode'        => $this->corp_wxqrcode,
				'corp_scale'           => $this->corp_scale,
				'corp_industry'        => $this->corp_industry,
				'corp_sub_industry'    => $this->corp_sub_industry,
				'location'             => $this->location,
			];

			if ($withAuth) {
				$result['auths'] = [];
				if (!empty($this->workCorpAuths)) {
					foreach ($this->workCorpAuths as $workCorpAuth) {
						array_push($result['auths'], $workCorpAuth->dumpMiniData($withConfig));
					}
				}
			}

			return $result;
		}

		/**
		 * @param bool $withConfig
		 * @param bool $withAuth
		 *
		 * @return array
		 */
		public function dumpMiniData ($withConfig = false, $withAuth = false)
		{
			$result = [
				'corpid'    => $this->corpid,
				'corp_name' => $this->corp_name,
				'state'     => $this->state,
			];

			if ($withAuth) {
				$result['auths'] = [];
				if (!empty($this->workCorpAuths)) {
					foreach ($this->workCorpAuths as $workCorpAuth) {
						array_push($result['auths'], $workCorpAuth->dumpMiniData($withConfig));
					}
				}
			}

			return $result;
		}

		/**
		 * @param     $corpInfo
		 * @param int $suiteId
		 * @param int $uid
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 * @throws \app\components\InvalidParameterException
		 */
		public static function setCorp ($corpInfo, $suiteId = 0, $uid = 0)
		{
			$corpId = 0;

			// 事务处理
			$transaction = Yii::$app->db->beginTransaction();
			try {
				$corp = static::findOne(['corpid' => $corpInfo['auth_corp_info']['corpid']]);

				if (empty($corp)) {
					$corp              = new WorkCorp();
					$corp->state       = WorkUtils::getCorpState();
					$corp->create_time = DateUtil::getCurrentTime();
				}

				$corp->corpid               = $corpInfo['auth_corp_info']['corpid'];
				$corp->corp_name            = $corpInfo['auth_corp_info']['corp_name'];
				$corp->corp_type            = $corpInfo['auth_corp_info']['corp_type'];
				$corp->corp_square_logo_url = !empty($corpInfo['auth_corp_info']['corp_square_logo_url']) ? $corpInfo['auth_corp_info']['corp_square_logo_url'] : '';
				$corp->corp_user_max        = !empty($corpInfo['auth_corp_info']['corp_user_max']) ? $corpInfo['auth_corp_info']['corp_user_max'] : '';
				$corp->corp_agent_max       = !empty($corpInfo['auth_corp_info']['corp_agent_max']) ? $corpInfo['auth_corp_info']['corp_agent_max'] : '';
				$corp->corp_full_name       = !empty($corpInfo['auth_corp_info']['corp_full_name']) ? $corpInfo['auth_corp_info']['corp_full_name'] : '';
				$corp->verified_end_time    = !empty($corpInfo['auth_corp_info']['verified_end_time']) ? $corpInfo['auth_corp_info']['verified_end_time'] : '';
				$corp->subject_type         = !empty($corpInfo['auth_corp_info']['subject_type']) ? $corpInfo['auth_corp_info']['subject_type'] : '';
				$corp->corp_wxqrcode        = !empty($corpInfo['auth_corp_info']['corp_wxqrcode']) ? $corpInfo['auth_corp_info']['corp_wxqrcode'] : '';
				$corp->corp_scale           = !empty($corpInfo['auth_corp_info']['corp_scale']) ? $corpInfo['auth_corp_info']['corp_scale'] : '';
				$corp->corp_industry        = !empty($corpInfo['auth_corp_info']['corp_industry']) ? $corpInfo['auth_corp_info']['corp_industry'] : '';
				$corp->corp_sub_industry    = !empty($corpInfo['auth_corp_info']['corp_sub_industry']) ? $corpInfo['auth_corp_info']['corp_sub_industry'] : '';
				$corp->location             = !empty($corpInfo['auth_corp_info']['location']) ? $corpInfo['auth_corp_info']['location'] : '';

				if ($corp->dirtyAttributes) {
					if (!$corp->validate() || !$corp->save()) {
						throw new Exception(SUtils::modelError($corp));
					}
				}

				$corpId = $corp->id;

				if ($suiteId != 0) {
					WorkCorpAuth::setCorp($suiteId, $corpId, $corpInfo);
				}

				$transaction->commit();
			} catch (Exception $e) {
				$transaction->rollBack();
				throw new InvalidDataException($e->getMessage());
			}

			if (!empty($corpInfo['auth_info']) && !empty($corpInfo['auth_info']['agent'])) {
				foreach ($corpInfo['auth_info']['agent'] as $agentInfo) {
					if ($suiteId != 0) {
						$agentInfo['suite_id'] = $suiteId;
					}
					$agentInfo['agent_type'] = WorkCorpAgent::AUTH_AGENT;
					WorkCorpAgent::setCorpAgent($corpId, $agentInfo);
				}
			}

			if ($uid != 0 && !empty($corpId)) {
				WorkUtils::checkCorpLimit($uid, $corpId);
				UserCorpRelation::setRelation($uid, $corpId);
			}

			//保存最后一次同步时间
			$corp->sync_user_time = time();
			$corp->save();

			return $corpId;
		}

		//绑定
		public static function bindCorp ($bindInfo, $uid = 0)
		{
			$corpId = 0;
			// 事务处理
			$transaction = Yii::$app->db->beginTransaction();
			try {
				$corp = static::findOne(['corpid' => $bindInfo['corpid']]);

				if (empty($corp)) {
					$corp              = new WorkCorp();
					$corp->corpid      = trim($bindInfo['corpid']);
					$corp->corp_name   = trim($bindInfo['corp_name']);
					$corp->state       = WorkUtils::getCorpState();
					$corp->create_time = DateUtil::getCurrentTime();
				}

				if ($corp->dirtyAttributes) {
					if (!$corp->validate() || !$corp->save()) {
						throw new Exception(SUtils::modelError($corp));
					}
				}

				$corpId = $corp->id;

				$corpBind = WorkCorpBind::findOne(['corp_id' => $corpId]);
				if (empty($corpBind)) {
					$corpBind                 = new WorkCorpBind();
					$corpBind->corp_id        = $corpId;
					$corpBind->token          = static::getRandom();
					$corpBind->encode_aes_key = static::getRandom(43);
					$corpBind->create_time    = DateUtil::getCurrentTime();
				}
				$corpBind->book_secret     = trim($bindInfo['book_secret']);
				$corpBind->external_secret = trim($bindInfo['external_secret']);

				if (isset($bindInfo['book_status'])) {
					$corpBind->book_status = $bindInfo['book_status'];
				}

				if (isset($bindInfo['external_status'])) {
					$corpBind->external_status = $bindInfo['external_status'];
				}

				if ($corpBind->dirtyAttributes) {
					if (!$corpBind->validate() || !$corpBind->save()) {
						throw new Exception(SUtils::modelError($corpBind));
					}
				}

				if ($uid != 0 && !empty($corpId)) {
					UserCorpRelation::setRelation($uid, $corpId);
				}

				//更新企业微信授权方的认证类型
				$workCorp = WorkCorp::findOne($corpId);
				if (!empty($workCorp) && empty($workCorp->corp_type)) {
					$workCorp->corp_type = 'verified';
					$workCorp->save();
				}

				$transaction->commit();
				if ($uid != 0 && !empty($corpId)) {
					WorkContactWayGroup::setNoGroup($uid, $corpId);//设置未分组
				}

				if (!empty($corpBind->book_secret) && empty($corp->workUsers)) {
					\Yii::$app->cache->set('syncWorkDepJob_' . $corp->id, 1, 3600);
					$jobId = \Yii::$app->work->push(new SyncWorkDepartmentListJob([
						'corp'     => $corp,
						'departId' => NULL,
					]));

					//保存最后一次同步时间
					$corp->sync_user_time = time();
					$corp->save();
				}

				if (!empty($corpBind->external_secret) && empty($corp->workExternalContacts)) {
					$jobId = \Yii::$app->work->push(new SyncWorkExternalContactJob([
						'corp' => $corp,
					]));
				}
			} catch (Exception $e) {
				$transaction->rollBack();
				throw new InvalidDataException($e->getMessage());
			}

			return $corpId;
		}

		/**
		 * 生成随机字符串，数字，大小写字母随机组合
		 *
		 * @param int $length 长度
		 * @param int $type   类型，1 纯数字，2 纯小写字母，3 纯大写字母，4 数字和小写字母，5 数字和大写字母，6 大小写字母，7 数字和大小写字母
		 */
		public static function getRandom ($length = 8, $type = 7)
		{
			// 取字符集数组
			$number      = range(0, 9);
			$lowerLetter = range('a', 'z');
			$upperLetter = range('A', 'Z');
			// 根据type合并字符集
			if ($type == 1) {
				$charset = $number;
			} elseif ($type == 2) {
				$charset = $lowerLetter;
			} elseif ($type == 3) {
				$charset = $upperLetter;
			} elseif ($type == 4) {
				$charset = array_merge($number, $lowerLetter);
			} elseif ($type == 5) {
				$charset = array_merge($number, $upperLetter);
			} elseif ($type == 6) {
				$charset = array_merge($lowerLetter, $upperLetter);
			} elseif ($type == 7) {
				$charset = array_merge($number, $lowerLetter, $upperLetter);
			} else {
				$charset = $number;
			}
			$str = '';
			// 生成字符串
			for ($i = 0; $i < $length; $i++) {
				$str .= $charset[mt_rand(0, count($charset) - 1)];
				// 验证规则
				if ($type == 4 && strlen($str) >= 2) {
					if (!preg_match('/\d+/', $str) || !preg_match('/[a-z]+/', $str)) {
						$str = substr($str, 0, -1);
						$i   = $i - 1;
					}
				}
				if ($type == 5 && strlen($str) >= 2) {
					if (!preg_match('/\d+/', $str) || !preg_match('/[A-Z]+/', $str)) {
						$str = substr($str, 0, -1);
						$i   = $i - 1;
					}
				}
				if ($type == 6 && strlen($str) >= 2) {
					if (!preg_match('/[a-z]+/', $str) || !preg_match('/[A-Z]+/', $str)) {
						$str = substr($str, 0, -1);
						$i   = $i - 1;
					}
				}
				if ($type == 7 && strlen($str) >= 3) {
					if (!preg_match('/\d+/', $str) || !preg_match('/[a-z]+/', $str) || !preg_match('/[A-Z]+/', $str)) {
						$str = substr($str, 0, -2);
						$i   = $i - 2;
					}
				}
			}

			return $str;
		}

		//通过corpid获取uid
        public static function getUidByCorpid($corpid = '')
        {
            $data = static::find()->alias('a')
                ->select('b.uid uid')
                ->innerJoin(UserCorpRelation::tableName() . ' b', 'a.id=b.corp_id')
                ->where(['a.corpid'=>$corpid])
                ->asArray()
                ->one();
            return $data['uid'] ?? 0;
        }
	}
