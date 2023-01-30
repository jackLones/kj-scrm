<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\components\InvalidParameterException;
	use app\queue\ChangeUserAndChangeTagsJob;
	use app\queue\WaitUserTaskJob;
	use app\util\DateUtil;
	use app\util\SUtils;
	use app\util\WebsocketUtil;
	use app\util\WorkUtils;
	use dovechen\yii2\weWork\src\dataStructure\EContactGetTransferResult;
	use dovechen\yii2\weWork\src\dataStructure\ExternalContactWay;
	use Yii;
	use yii\db\Expression;
	use yii\helpers\Json;

	/**
	 * This is the model class for table "{{%work_external_contact_follow_user}}".
	 *
	 * @property int                         $id
	 * @property int                         $external_userid  外部联系人ID
	 * @property int                         $user_id          成员ID
	 * @property string                      $userid           添加了此外部联系人的企业成员userid
	 * @property string                      $remark           该成员对此外部联系人的备注
	 * @property string                      $description      该成员对此外部联系人的描述
	 * @property string                      $createtime       该成员添加此外部联系人的时间
	 * @property string                      $tags             该成员添加此外部联系人所打标签的分组名称（标签功能需要企业微信升级到2.7.5及以上版本）
	 * @property string                      $remark_corp_name 该成员对此客户备注的企业名称
	 * @property string                      $remark_mobiles   该成员对此客户备注的手机号码，第三方不可获取
	 * @property string                      $state            该成员添加此客户的渠道，由用户通过创建「联系我」方式指定
	 * @property int                         $way_id           联系我配置ID
	 * @property int                         $baidu_way_id     百度联系我配置ID
	 * @property int                         $way_redpack_id   红包活动活码ID
	 * @property int                         $add_way          添加客户的来源：0、未知来源；1、 扫描二维码；2、搜索手机号；3、名片分享；4、群聊；5、手机通讯录；6、微信联系人；7、来自微信的添加好友申请；8、安装第三方应用时自动添加的客服人员；9、搜索邮箱；201、内部成员共享；202、管理员/负责人分配
	 * @property string                      $oper_userid      发起添加的userid，如果成员主动添加，为成员的userid；如果是客户主动添加，则为客户的外部联系人userid；如果是内部成员共享/管理员分配，则为对应的成员/管理员userid
	 * @property int                         $chat_way_id      群活码联系我配置ID
	 * @property int                         $fission_id       裂变任务id
	 * @property int                         $award_id         抽奖任务id
	 * @property int                         $red_pack_id      红包裂变id
	 * @property int                         $follow_id        状态id
	 * @property int                         $del_type         0：未删除；1：成员删除外部联系人；2：外部联系人删除成员
	 * @property string                      $del_time         删除时间
	 * @property int                         $delete_type      0：未删除；1：成员删除外部联系人；2：外部联系人删除成员
	 * @property int                         $repeat_type      0已删除1再次添加
	 * @property int                         $update_time      最后一次跟进状态时间
	 * @property int                         $is_chat          沟通状态 0一直未沟通 1已沟通
	 * @property string                      $nickname         设置的用户昵称备注
	 * @property string                      $des              设置的用户描述
	 * @property string                      $close_rate       预计成交率
	 * @property int                         $follow_num       跟进次数
	 * @property int                         $activity_id      任务宝id
	 * @property int                         $is_reclaim       是否已回收：0未回收、1已回收
	 * @property int                         $punch_id         群打卡ID
	 * @property int                         $is_protect       是否客户保护：0否、1是
	 * @property int                         $other_way        添加客户的其它来源：1公海池
	 * @property int                         $store_id         门店id
	 *
	 * @property WorkExternalContact         $externalUser
	 * @property WorkUser                    $user
	 * @property WorkContactWay              $way
	 * @property WorkExternalContactMember[] $workExternalContactMembers
	 */
	class WorkExternalContactFollowUser extends \yii\db\ActiveRecord
	{

		const HAS_CHAT = 1;
		const NO_CHAT  = 0;

		const WORK_CON_EX = 0; //未删除
		const WORK_DEL_EX = 1; //成员删除外部联系人
		const EX_DEL_WORK = 2; //外部联系人删除成员
		const NO_ASSIGN   = 3; //离职未分配
		const HAS_ASSIGN  = 4; //离职已分配
		const DEL_REPEAT  = 0;
		const IS_REPEAT   = 1;

		const UNKNOW_ADD_WAY               = 0;
		const QRCODE_ADD_WAY               = 1;
		const SEARCH_PHONE_ADD_WAY         = 2;
		const CARD_SHARE_ADD_WAY           = 3;
		const CHAT_ADD_WAY                 = 4;
		const CALL_HISTORY_ADD_WAY         = 5;
		const WX_CONTACT_ADD_WAY           = 6;
		const WX_ADD_APPLY_ADD_WAY         = 7;
		const OTHER_AGENT_AUTO_ADD_ADD_WAY = 8;
		const SEARCH_EMAIL_ADD_WAY         = 9;
		const USER_SHARE_ADD_WAY           = 201;
		const ADMIN_ASSIGN_ADD_WAY         = 202;

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_external_contact_follow_user}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['external_userid', 'user_id', 'way_id','store_id', 'way_redpack_id', 'del_type', 'delete_type', 'repeat_type', 'follow_id', 'update_time', 'is_chat', 'follow_num', 'activity_id'], 'integer'],
				[['userid', 'remark', 'remark_corp_name', 'nickname'], 'string', 'max' => 64],
				[['description', 'remark_mobiles', 'state'], 'string', 'max' => 255],
				[['external_userid'], 'exist', 'skipOnError' => true, 'targetClass' => WorkExternalContact::className(), 'targetAttribute' => ['external_userid' => 'id']],
				[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkUser::className(), 'targetAttribute' => ['user_id' => 'id']],
				[['way_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkContactWay::className(), 'targetAttribute' => ['way_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'               => Yii::t('app', 'ID'),
				'external_userid'  => Yii::t('app', '外部联系人ID'),
				'user_id'          => Yii::t('app', '成员ID'),
				'userid'           => Yii::t('app', '添加了此外部联系人的企业成员userid'),
				'remark'           => Yii::t('app', '该成员对此外部联系人的备注'),
				'description'      => Yii::t('app', '该成员对此外部联系人的描述'),
				'createtime'       => Yii::t('app', '该成员添加此外部联系人的时间'),
				'tags'             => Yii::t('app', '该成员添加此外部联系人所打标签的分组名称（标签功能需要企业微信升级到2.7.5及以上版本）'),
				'remark_corp_name' => Yii::t('app', '该成员对此客户备注的企业名称'),
				'remark_mobiles'   => Yii::t('app', '该成员对此客户备注的手机号码，第三方不可获取'),
				'state'            => Yii::t('app', '该成员添加此客户的渠道，由用户通过创建「联系我」方式指定'),
				'way_id'           => Yii::t('app', '联系我配置ID'),
				'baidu_way_id'     => Yii::t('app', '百度联系我配置ID'),
				'way_redpack_id'   => Yii::t('app', '红包活动活码ID'),
				'add_way'          => Yii::t('app', '添加客户的来源：0、未知来源；1、 扫描二维码；2、搜索手机号；3、名片分享；4、群聊；5、手机通讯录；6、微信联系人；7、来自微信的添加好友申请；8、安装第三方应用时自动添加的客服人员；9、搜索邮箱；201、内部成员共享；202、管理员/负责人分配'),
				'oper_userid'      => Yii::t('app', '发起添加的userid，如果成员主动添加，为成员的userid；如果是客户主动添加，则为客户的外部联系人userid；如果是内部成员共享/管理员分配，则为对应的成员/管理员userid'),
				'chat_way_id'      => Yii::t('app', '群活码联系我配置ID'),
				'fission_id'       => Yii::t('app', '裂变任务id'),
				'award_id'         => Yii::t('app', '抽奖任务id'),
				'red_pack_id'      => Yii::t('app', '红包裂变id'),
				'follow_id'        => Yii::t('app', '状态id'),
				'del_type'         => Yii::t('app', '0：未删除；1：成员删除外部联系人；2：外部联系人删除成员'),
				'del_time'         => Yii::t('app', '删除时间'),
				'delete_type'      => Yii::t('app', '0：未删除；1：成员删除外部联系人；2：外部联系人删除成员'),
				'repeat_type'      => Yii::t('app', '0已删除1再次添加'),
				'update_time'      => Yii::t('app', '最后一次跟进状态时间'),
				'is_chat'          => Yii::t('app', '沟通状态 0一直未沟通 1已沟通'),
				'nickname'         => Yii::t('app', '设置的用户昵称备注'),
				'des'              => Yii::t('app', '设置的用户描述'),
				'close_rate'       => Yii::t('app', '预计成交率'),
				'follow_num'       => Yii::t('app', '跟进次数'),
				'activity_id'      => Yii::t('app', '任务宝id'),
				'is_reclaim'       => Yii::t('app', '是否已回收：0未回收、1已回收'),
				'punch_id'         => Yii::t('app', '群打卡ID'),
				'is_protect'       => Yii::t('app', '是否客户保护：0否、1是'),
				'other_way'        => Yii::t('app', '添加客户的其它来源：1公海池'),
				'store_id'         => Yii::t('app', '门店id'),
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
		public function getExternalUser ()
		{
			return $this->hasOne(WorkExternalContact::className(), ['id' => 'external_userid']);
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
		public function getWay ()
		{
			return $this->hasOne(WorkContactWay::className(), ['id' => 'way_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkExternalContactMembers ()
		{
			return $this->hasMany(WorkExternalContactMember::className(), ['follow_user_id' => 'id']);
		}

		/**
		 * 获取外部联系的来源信息
		 *
		 * @param int $index
		 *
		 * @return string|string[]
		 */
		public static function getAddWay ($index = -1)
		{
			$data = [
				self::UNKNOW_ADD_WAY               => '其它渠道',
				self::QRCODE_ADD_WAY               => '扫描二维码',
				self::SEARCH_PHONE_ADD_WAY         => '搜索手机号',
				self::CARD_SHARE_ADD_WAY           => '名片分享',
				self::CHAT_ADD_WAY                 => '群聊',
				self::CALL_HISTORY_ADD_WAY         => '手机通讯录',
				self::WX_CONTACT_ADD_WAY           => '微信联系人',
				self::WX_ADD_APPLY_ADD_WAY         => '来自微信的添加好友申请',
				self::OTHER_AGENT_AUTO_ADD_ADD_WAY => '安装第三方应用时自动添加的客服人员',
				self::SEARCH_EMAIL_ADD_WAY         => '搜索邮箱',
				self::USER_SHARE_ADD_WAY           => '内部成员共享',
				self::ADMIN_ASSIGN_ADD_WAY         => '管理员/负责人分配',
			];

			$result = $data;

			if ($index != -1) {
				$result = !empty($data[$index]) ? $data[$index] : '';
			}

			return $result;
		}

		/**
		 * @param bool $withUserInfo
		 * @param bool $withDepartment
		 *
		 * @return array
		 */
		public function dumpData ($withUserInfo = false, $withDepartment = false)
		{
			$result = [
				'userid'           => $this->userid,
				'remark'           => $this->remark,
				'description'      => $this->description,
				'createtime'       => $this->createtime,
				'tags'             => $this->tags,
				'remark_corp_name' => $this->remark_corp_name,
				'remark_mobiles'   => $this->remark_mobiles,
				'state'            => $this->state,
				'del_type'         => $this->del_type,
				'delete_type'      => $this->delete_type,
				'repeat_type'      => $this->repeat_type,
				'add_way'          => $this->add_way,
				'oper_userid'      => $this->oper_userid,
				'add_way_info'     => static::getAddWay($this->add_way),
			];

			if ($withUserInfo) {
				$result['user_info'] = $this->user->dumpData($withDepartment);
			}

			return $result;
		}

		/**
		 * @param $externalUser
		 * @param $followInfo
		 * @param $name
		 * @param $corpName
		 * @param $createtime
		 *
		 * @return bool
		 *
		 * @throws InvalidDataException
		 */
		public static function fansTimeLine ($externalUser, $followInfo, $name, $corpName, $createtime)
		{
			if (!empty($externalUser->unionid)) {
				$fans = Fans::findOne(['unionid' => $externalUser->unionid]);
				if (!empty($fans)) {
					$fans->external_userid = $externalUser->id;
					$fans->save();
					$addWayInfo = '其他';
					if (!empty($followInfo['add_way'])) {
						$addWayInfo = static::getAddWay($followInfo['add_way']);
					}
					$wayInfo = '';
					if (isset($followInfo['way_id']) && $followInfo['way_id'] > 0) {
						$wayInfo    = '渠道活码';
						$contactWay = WorkContactWay::findOne($followInfo['way_id']);
						if (!empty($contactWay)) {
							$title   = $contactWay->title;
							$wayInfo = $wayInfo . '-' . $title;
						}
					} elseif (isset($followInfo['chat_way_id']) && $followInfo['chat_way_id'] > 0) {
						$wayInfo = '自动拉群';
						$way     = WorkChatContactWay::findOne($followInfo['chat_way_id']);
						if (!empty($way)) {
							$title   = $way->title;
							$wayInfo = $wayInfo . '-' . $title;
						}
					} elseif (isset($followInfo['fission_id']) && $followInfo['fission_id'] > 0) {
						$wayInfo = '裂变引流';
						$fission = Fission::findOne($followInfo['fission_id']);
						if (!empty($fission)) {
							$title   = $fission->title;
							$wayInfo = $wayInfo . '-' . $title;
						}
					} elseif (isset($followInfo['award_id']) && $followInfo['award_id'] > 0) {
						$wayInfo = '抽奖引流';
						$award   = AwardsActivity::findOne($followInfo['award_id']);
						if (!empty($award)) {
							$title   = $award->title;
							$wayInfo = $wayInfo . '-' . $title;
						}
					} elseif (isset($followInfo['red_pack_id']) && $followInfo['red_pack_id'] > 0) {
						$wayInfo = '红包裂变';
						$red     = RedPack::findOne($followInfo['red_pack_id']);
						if (!empty($red)) {
							$title   = $red->title;
							$wayInfo = $wayInfo . '-' . $title;
						}
					} elseif (isset($followInfo['punch_id']) && $followInfo['punch_id'] > 0) {
						$wayInfo = '群打卡';
						$punch   = WorkGroupClockActivity::findOne($followInfo['punch_id']);
						if (!empty($punch)) {
							$title   = $punch->title;
							$wayInfo = $wayInfo . '-' . $title;
						}
					}
					if (!empty($wayInfo)) {
						$addWayInfo = $wayInfo;
					}
					$remark = '粉丝通过【' . $addWayInfo . '】来源加成员【' . $name . '】成为【' . $corpName . '】企业微信的客户';
					FansTimeLine::create($fans->id, 'custom', $createtime, 0, 0, $remark);
				}
			}

			return true;
		}

		/**
		 * @param        $userId
		 * @param        $externalUserId
		 * @param        $followInfo
		 * @param        $corpId
		 * @param string $followUserId
		 * @param string $state
		 * @param string $fromEvent
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function setFollowUser ($userId, $externalUserId, $followInfo, $corpId, $followUserId = '', $state = '', $fromEvent = '')
		{
			$workUser   = WorkUser::findOne($userId);
			$followUser = static::findOne(['external_userid' => $externalUserId, 'user_id' => $userId]);
			$workCorp   = WorkCorp::findOne($corpId);

			$uid = 0;
			if (!empty($workUser)) {
				$userCorp = UserCorpRelation::findOne(['corp_id' => $workUser->corp_id]);
				$uid      = $userCorp->uid;
			}
			$isTimeLine  = 0;//记录客户轨迹
			$isClaimUser = 0;//跟新认领记录
			$isAddFollow = 0;//是否新增
			if (empty($followUser)) {
				$followUser  = new WorkExternalContactFollowUser();
				$isTimeLine  = 1;
				$isClaimUser = 1;
				$isAddFollow = 1;
			}
			$followUser->user_id         = $userId;
			$followUser->external_userid = $externalUserId;

			if (!empty($followInfo['userid'])) {
				$followUser->userid = $followInfo['userid'];
			}

			if (!empty($followInfo['createtime'])) {
				if (empty($followUser->createtime)) {
					$followUser->createtime = $followInfo['createtime'];
					//添加粉丝记录
					if (!empty($followUser->externalUser)) {
						try {
							static::fansTimeLine($followUser->externalUser, $followInfo, $workUser->name, $followUser->externalUser->corp->corp_name, $followInfo['createtime']);
						} catch (\Exception $e) {
							\Yii::error($e->getMessage(), 'message_fans');
						}

					}
				}
			}
			if ($followUser->userid == $followUserId && !in_array($followUser->del_type, [static::WORK_CON_EX])) {
				$followUser->del_type = static::WORK_CON_EX;
				$isClaimUser          = 1;
				if ($followUser->delete_type == static::EX_DEL_WORK) {
					$isTimeLine              = 2;
					$followUser->repeat_type = static::IS_REPEAT;//标记为再次添加
					$followUser->createtime  = time();
					//添加粉丝记录
					if (!empty($followUser->externalUser)) {
						static::fansTimeLine($followUser->externalUser, $followInfo, $workUser->name, $followUser->externalUser->corp->corp_name, time());
					}

				}
			}
			if (!empty($followInfo['remark'])) {
				$followUser->remark = $followInfo['remark'];
				if (empty($followUser->nickname)) {
					$followUser->nickname = $followInfo['remark'];
				} else {
					if ($fromEvent == WorkExternalContact::EVENT_EXTERNAL_CONTACT) {
						$followUser->nickname = $followInfo['remark'];
					}
				}
			} else {
				if ($fromEvent == WorkExternalContact::EVENT_EXTERNAL_CONTACT) {
					$followUser->nickname = '';
				}
			}

			if (!empty($followInfo['description'])) {
				$followUser->description = $followInfo['description'];
				if (empty($followUser->des)) {
					$followUser->des = $followInfo['description'];
				} else {
					if ($fromEvent == WorkExternalContact::EVENT_EXTERNAL_CONTACT) {
						$followUser->des = $followInfo['description'];
					}
				}
			} else {
				if ($fromEvent == WorkExternalContact::EVENT_EXTERNAL_CONTACT) {
					$followUser->des = '';
				}
			}

			if (!empty($followInfo['tags'])) {
				if (!empty($followInfo['scrm_from']) && $followInfo['scrm_from'] == WorkUtils::BATCH_GET_BY_USER) {
					if (!empty($followUser->tags)) {
						$localTags = Json::decode($followUser->tags);

						foreach ($localTags as $localTag) {
							if (empty($localTag['type']) || (!empty($localTag['type']) && $localTag['type'] != WorkTag::CORP_TAG)) {
								array_push($followInfo['tags'], $localTag);
							}
						}
					}
				}

				$followUser->tags = Json::encode($followInfo['tags'], JSON_UNESCAPED_UNICODE);
			}
			if (!empty($followInfo['remark_corp_name'])) {
				$followUser->remark_corp_name = $followInfo['remark_corp_name'];
				//同步到客户自定义属性表
				CustomFieldValue::add('company', $externalUserId, $followInfo['remark_corp_name'], $fromEvent, 0, 0, $uid);
			} else {
				if ($fromEvent == WorkExternalContact::EVENT_EXTERNAL_CONTACT) {
					$followUser->remark_corp_name = '';
				}
			}

			$hasImport = [];
			if (!empty($followInfo['remark_mobiles'])) {
				$followUser->remark_mobiles = implode(',', $followInfo['remark_mobiles']);

				//是否有导入会员
				//if ($isTimeLine) {
					foreach ($followInfo['remark_mobiles'] as $remark_mobiles) {
                        $hasImport = WorkImportCustomerDetail::findOne(['user_id' => $userId, 'phone' => $remark_mobiles, 'is_add' => [0,2]]);//0未添加，2待分配
						if (!empty($hasImport)) {
							break;
						}
					}
				//}

				//同步到客户自定义属性表
				CustomFieldValue::add('phone', $externalUserId, $followUser->remark_mobiles, $fromEvent, $workCorp->unshare_field, $userId, $uid);
			} else {
				if ($fromEvent == WorkExternalContact::EVENT_EXTERNAL_CONTACT) {
					$followUser->remark_mobiles = '';
				}
			}

			if (!empty($followInfo['state'])) {
				$followUser->state = $followInfo['state'];
			}

			if (!empty($followInfo['way_id'])) {
				$followUser->way_id = $followInfo['way_id'];
			}
			if (!empty($followInfo['activity_id'])) {
				$followUser->activity_id = $followInfo['activity_id'];
			}

			if (!empty($followInfo['baidu_way_id'])) {
				$followUser->baidu_way_id = $followInfo['baidu_way_id'];
			}

			if (!empty($followInfo['auth_store'])) {
				$followUser->store_id = $followInfo['auth_store'];
			}

			if (!empty($followInfo['way_redpack_id'])) {
				//红包活动活码
				$followUser->way_redpack_id = $followInfo['way_redpack_id'];
			}

			if (!empty($followInfo['add_way'])) {
				$followUser->add_way = intval($followInfo['add_way']);
				if ($followInfo['add_way'] == WorkExternalContactFollowUser::ADMIN_ASSIGN_ADD_WAY) {

					\Yii::error($followInfo, '$followInfo');
					$workExContact = WorkExternalContact::findOne($externalUserId);
					//找到分配中的客户
					$disUserDetail = WorkDismissUserDetail::find()->where(['corp_id' => $corpId, 'external_userid' => $workExContact->id])->all();
					if (!empty($disUserDetail)) {
						/** @var WorkDismissUserDetail $detail */
						foreach ($disUserDetail as $detail) {
							$workApi    = WorkUtils::getWorkApi($corpId, WorkUtils::EXTERNAL_API);
							$assignList = $workApi->ECGetUnAssignedList();
							$workUser   = WorkUser::findOne($detail->user_id);
							$contact    = WorkExternalContact::findOne($detail->external_userid);
							$flag       = 0;
							\Yii::error($assignList, '$assignList');
							if (!empty($workUser) && !empty($contact)) {
								foreach ($assignList as $list) {
									if ($list['handover_userid'] == $workUser->userid && $list['external_userid'] == $contact->external_userid) {
										//还未分配
										$flag = 1;
									}
								}
							}
							\Yii::error($flag, '$flag');
							if (empty($flag)) {
								//分配中
								$data                    = [];
								$data['external_userid'] = $workExContact->external_userid;
								$data['handover_userid'] = $workUser->userid;
								$data['takeover_userid'] = $followInfo['userid'];
								\Yii::error($data, '$data1');
								$res    = EContactGetTransferResult::parseFromArray($data);
								$result = $workApi->EContactGetTransferResult($res);
								\Yii::error($result, '$result1');

								$userOne = WorkUser::findOne(['userid' => $followInfo['userid'], 'corp_id' => $corpId]);
								if ($result['status'] == 1) {
									$detail->allocate_time    = $result['takeover_time'];
									$detail->allocate_user_id = $userOne->id;
									$detail->status           = WorkDismissUserDetail::IS_ASSIGN;
									$detail->save();
									$contactUser = WorkExternalContactFollowUser::findOne(['userid' => $workUser->userid, 'external_userid' => $externalUserId]);
									if (!empty($contactUser)) {
										$contactUser->del_type = WorkExternalContactFollowUser::HAS_ASSIGN;
										$contactUser->save();
									}
									\Yii::error($userOne->id, '$userOne->id1');
									ExternalTimeLine::updateAll(['user_id' => $userOne->id], ['user_id' => $detail->user_id, 'external_id' => $externalUserId]);
								} else {
									//查不到 默认一个
									$detail->allocate_time    = !empty($followInfo['createtime']) ? $followInfo['createtime'] : time();
									$detail->allocate_user_id = $userOne->id;
									$detail->status           = WorkDismissUserDetail::IS_ASSIGN;
									$detail->save();
									$contactUser = WorkExternalContactFollowUser::findOne(['userid' => $workUser->userid, 'external_userid' => $externalUserId]);
									if (!empty($contactUser)) {
										$contactUser->del_type = WorkExternalContactFollowUser::HAS_ASSIGN;
										$contactUser->save();
									}
									\Yii::error($userOne->id, '$userOne->id2');
									ExternalTimeLine::updateAll(['user_id' => $userOne->id], ['user_id' => $detail->user_id, 'external_id' => $externalUserId]);
									WorkExternalContactFollowRecord::updateAll(['user_id' => $userOne->id], ['user_id' => $detail->user_id, 'external_id' => $externalUserId]);
								}

							}
						}
					}
				}

			} else {//目前接口还没给传add_way这个接口，根据其它字段来赋值
				if (!empty($followInfo['way_id']) || !empty($followInfo['fission_id']) || !empty($followInfo['award_id']) || !empty($followInfo['red_pack_id'])) {
					$followUser->add_way = 1;
				}
			}

			if (!empty($followInfo['oper_userid'])) {
				$followUser->oper_userid = $followInfo['oper_userid'];
			}

			if (!empty($followInfo['chat_way_id'])) {
				$followUser->chat_way_id = $followInfo['chat_way_id'];
			}

			if (!empty($followInfo['fission_id'])) {
				$followUser->fission_id = $followInfo['fission_id'];
			}

			$addSop = 0;
			if (empty($followUser->follow_id)) {
				$follow_id = Follow::getFollowIdByCorpId($corpId);
				if (!empty($follow_id)) {
					$followUser->follow_id   = $follow_id;
					$followUser->update_time = $followUser->createtime;

					$addSop = 1;
				}
			}

			if (!empty($followInfo['award_id'])) {
				$followUser->award_id = $followInfo['award_id'];
			}

			if (!empty($followInfo['red_pack_id'])) {
				$followUser->red_pack_id = $followInfo['red_pack_id'];
			}

			if (!empty($followInfo['punch_id'])) {
				$followUser->punch_id = $followInfo['punch_id'];
			}

			//导入的信息
			if (!empty($hasImport)) {
				if (!empty($hasImport->des)) {
					$followUser->nickname = $hasImport->des;//用户昵称备注
				}
				/*if (!empty($hasImport->nickname)) {
					$followUser->nickname = $hasImport->nickname;//昵称
				}*/

				if (!empty($hasImport->phone) || !empty($hasImport->name) || !empty($hasImport->sex) || !empty($hasImport->area)) {
					$customField = CustomField::find()->andWhere(['uid' => 0])->andWhere(['in', 'key', ['phone', 'name', 'sex', 'area']])->all();
					foreach ($customField as $field) {
						$key    = $field->key;
						$fieldV = $hasImport->$key;
						if (!empty($fieldV)) {
							if ($key == 'sex') {
								$fieldV = $fieldV == 1 ? '男' : '女';
							}
							if ($workCorp->unshare_field == 0) {
								$fieldValue = CustomFieldValue::findOne(['type' => 1, 'cid' => $externalUserId, 'fieldid' => $field->id]);
							} else {
								$fieldValue = CustomFieldValue::findOne(['type' => 1, 'cid' => $externalUserId, 'fieldid' => $field->id, 'user_id' => $userId]);
							}

							if (empty($fieldValue)) {
								$fieldValue          = new CustomFieldValue();
								$fieldValue->uid     = $uid;
								$fieldValue->type    = 1;
								$fieldValue->cid     = $externalUserId;
								$fieldValue->fieldid = $field->id;
								$fieldValue->value   = $fieldV;
								$fieldValue->time    = time();
								if ($workCorp->unshare_field == 1) {
									$fieldValue->user_id = $userId;
								}
								$fieldValue->save();
							} elseif (empty($fieldValue->value)) {
								$fieldValue->value = $fieldV;
								$fieldValue->save();
							}
						}
					}

				}
			}

			if ($followUser->dirtyAttributes) {
				if (!$followUser->validate() || !$followUser->save()) {
					throw new InvalidDataException(SUtils::modelError($followUser));
				}
			}
			if (!empty($followInfo['way_id'])) {
				$contactWay = WorkContactWay::findOne($followInfo['way_id']);
				if(!empty($contactWay)){
					$package = Package::find()->alias("a")
						->leftJoin("{{%user}} as b", "a.id = b.package_id")
						->leftJoin("{{%user_corp_relation}} as c", "b.uid = c.uid")
						->leftJoin("{{%work_user}} as d", "d.corp_id = c.corp_id")
						->where(["d.id" => $followUser->user_id])->select("a.follow_num,a.follow_open")->asArray()->one();
					if (!empty($package) && $package["follow_open"] == 1 && $contactWay->is_new == 1 && $contactWay->package_del == 0) {
						$followCount = WorkExternalContactFollowUser::find()->where(["way_id" => $contactWay->id])->count();
						if ($followCount >= $package["follow_num"]) {
							try {
								$workApi = WorkUtils::getWorkApi($corpId, WorkUtils::EXTERNAL_API);
								$workApi->ECDelContactWay($contactWay->config_id);
								$contactWay->package_del = 1;
								$contactWay->save();
							}catch (\Exception $e){
								Yii::error($e->getMessage(),'ECDelContactWay');
							}
						}
					}
				}
			}
			//导入客户状态更改
			if (!empty($hasImport)) {
                $importUsers = WorkImportCustomerDetail::find()->where(['user_id' => $userId, 'phone' => $hasImport->phone, 'is_add' => [0,2]])->all();
				foreach ($importUsers as $importUser){
                    $importUser->is_add             = 1;
                    $importUser->add_time           = time();
                    $importUser->external_follow_id = $followUser->id;
                    if (!$importUser->validate() || !$importUser->save()) {
                        throw new InvalidDataException(SUtils::modelError($importUser));
                    }
				}
				$workImport = WorkImportCustomer::findOne($hasImport->import_id);
				if($workImport && $workImport->tag_ids){
                    WorkTag::addUserTag(2, [$followUser->id], explode(',',$workImport->tag_ids));
                }
			}

			//新客培育SOP
			if ($isTimeLine == 1 || $isTimeLine == 2) {
				WorkSop::sendSopMsg($followUser->externalUser->corp_id, 1, $userId, $externalUserId);
			}
			//客户跟进SOP
			if ($addSop){
				WorkSop::sendSopMsg($followUser->externalUser->corp_id, 2, $userId, $externalUserId, $followUser->follow_id);
			}

			$externalOpenid = $followUser->externalUser->openid;
			if (!empty($externalOpenid) && (!empty($followUser->fission_id) || !empty($followUser->award_id) || !empty($followUser->red_pack_id)) && ($state == $followUser->state)) {
				$socket_type = $mission_id = '';
				if (!empty($followUser->fission_id)) {
					$socket_type = SUtils::FISSION_WEBSOCKET_TYPE;
					$mission_id  = $followUser->fission_id;
				} elseif (!empty($followUser->award_id)) {
					$socket_type = SUtils::AWARD_WEBSOCKET_TYPE;
					$mission_id  = $followUser->award_id;
				} elseif (!empty($followUser->red_pack_id)) {
					$socket_type = SUtils::RED_WEBSOCKET_TYPE;
					$mission_id  = $followUser->red_pack_id;
				}
				$info = [
					'mission_id' => $mission_id,
					'type'       => $socket_type,
					'has_chat'   => true,
				];
				if (!empty($followUser->award_id)) {
					$site_url          = \Yii::$app->params['site_url'];
					$head_url          = $site_url . '/static/image/default-head.png';
					$info['nick_name'] = $followUser->externalUser->name;
					$info['avatar']    = !empty($followUser->externalUser->avatar) ? $followUser->externalUser->avatar : $head_url;
					$awardActivity     = AwardsActivity::findOne($followUser->award_id);
					if (!empty($awardActivity)) {
						if (!empty($awardActivity->is_share_open)) {
							$shareSetting       = json_decode($awardActivity->share_setting, true);
							$shareNum           = $shareSetting[0]['total_num']; //分享一次增加的抽奖次数
							$info['add_chance'] = $shareNum;
						} else {
							$info['add_chance'] = '';
						}
					}
					if (!empty($followUser->state)) {
						$stateArr = explode(',', $followUser->state);
						if (empty($stateArr[2])) {
							$info['is_refresh'] = 1;
						}
					}
				}
				Yii::$app->websocket->send([
					'channel' => 'web-message',
					'to'      => $externalOpenid,
					'type'    => $socket_type,
					'info'    => $info
				]);
			}
			//添加待办
			$followId = Follow::getFollowIdByCorpId($corpId);
			if (!empty($followUser->follow_id) && $followId == $followUser->follow_id) {
				$waitTask = WaitTask::find()->alias('w')->leftJoin('{{%wait_project}} p', 'w.project_id=p.id')->where(['w.follow_id' => $followId, 'p.is_del' => 0, 'w.is_del' => 0])->select('w.id')->asArray()->all();
				if (!empty($waitTask)) {
					$jobId = \Yii::$app->queue->push(new WaitUserTaskJob([
						'followId' => $followId,
						'type'     => 1,
						'corpId'   => $followUser->externalUser->corp_id,
						'daysNew'  => 0
					]));
				}
			}

			//beenlee 用户创建或变更 检查并打标签
			if (!empty($followUser->id)) {
				$dateJob = [];
				if ($isAddFollow > 0) {
					$dateJob['type'] = 4;
				} else {
					$dateJob['type'] = 5;
				}
				$dateJob['corp_id']     = $followUser->externalUser->corp_id;
				$dateJob['uid']         = $uid;
				$dateJob['external_id'] = $followUser->external_userid;
//				\Yii::$app->queue->push(new ChangeUserAndChangeTagsJob($dateJob));
			}

			try {
				$isTag = 0;
				//给外部联系人打标签
				if (!empty($followInfo['way_id'])) {
					$work_contact_way = WorkContactWay::findOne($followInfo['way_id']);
					if (!empty($work_contact_way->tag_ids)) {
						$tag_ids        = explode(',', $work_contact_way->tag_ids);
						$external_ids[] = $followUser->id;
						try {
							WorkTag::addUserTag(2, $external_ids, $tag_ids);
						} catch (\Exception $e) {
							Yii::error($e->getMessage(), 'setFollowUserWay');
						}
					}
					$isTag = 1;
				}
				//通过百度二维码活动打标签
				if (!empty($followInfo['baidu_way_id'])) {
					$baiDuInfo = WorkContactWayBaidu::findOne($followInfo['baidu_way_id']);
					if (!empty($baiDuInfo) && !empty($baiDuInfo->tag_ids)) {
						$tag_ids   = explode(',', $baiDuInfo->tag_ids);
						$otherData = ['type' => 'fission', 'msg' => '百度统计【' . $baiDuInfo->title . '】'];
						try {
							WorkTag::addUserTag(2, [$followUser->id], $tag_ids, $otherData);
						} catch (\Exception $e) {
							Yii::error($e->getMessage(), 'setFollowUserBaiDu');
						}
					}
					$isTag = 1;
				}
				//通过红包活动活码打标签
				if (!empty($followInfo['way_redpack_id'])) {
					$redpacketInfo = WorkContactWayRedpacket::findOne($followInfo['way_redpack_id']);
					if (!empty($redpacketInfo) && !empty($redpacketInfo->tag_ids)) {
						$tag_ids   = explode(',', $redpacketInfo->tag_ids);
						$otherData = ['type' => WorkContactWayRedpacket::REDPACKET_WAY, 'msg' => '红包拉新【' . $redpacketInfo->name . '】'];
						try {
							WorkTag::addUserTag(2, [$followUser->id], $tag_ids, $otherData);
						} catch (\Exception $e) {
							Yii::error($e->getMessage(), 'setFollowUserRedpacket');
						}
					}
					$isTag = 1;
				}

				//通过群聊二维码添加打标签
				if (!empty($followInfo['chat_way_id'])) {
					$chatWay = WorkChatContactWay::findOne($followInfo['chat_way_id']);
					if (!empty($chatWay) && !empty($chatWay->tag_ids)) {
						$tag_ids    = explode(',', $chatWay->tag_ids);
						$chat_ids[] = $followUser->id;
						try {
							WorkTag::addUserTag(2, $chat_ids, $tag_ids);
						} catch (\Exception $e) {
							Yii::error($e->getMessage(), 'setFollowUserChat');
						}

					}
					$isTag = 1;
				}

				//抽奖引流打标签
				if (!empty($followInfo['award_id'])) {
					$awardAct = AwardsActivity::findOne($followInfo['award_id']);
					if (!empty($awardAct->tag_ids)) {
						$tag_ids        = explode(',', $awardAct->tag_ids);
						$external_ids[] = $followUser->id;
						try {
							WorkTag::addUserTag(2, $external_ids, $tag_ids);
						} catch (\Exception $e) {
							Yii::error($e->getMessage(), 'setFollowUserAward');
						}

					}
					$isTag = 1;
				}

				//裂变引流打标签
				if (!empty($followInfo['fission_id'])) {
					$fission = Fission::findOne($followInfo['fission_id']);
					if (!empty($fission->tag_ids)) {
						$tag_ids        = explode(',', $fission->tag_ids);
						$external_ids[] = $followUser->id;
						try {
							WorkTag::addUserTag(2, $external_ids, $tag_ids);
						} catch (\Exception $e) {
							Yii::error($e->getMessage(), 'setFollowUserFission');
						}
					}
					$isTag = 1;
				}
                //excel导入打标签
                if (!empty($hasImport)) {
                    $workImportCustomer = WorkImportCustomer::findOne($hasImport->import_id);
                    if (!empty($workImportCustomer->tag_ids)) {
                        $tag_ids        = explode(',', $workImportCustomer->tag_ids);
                        $external_ids[] = $followUser->id;
                        try {
                            WorkTag::addUserTag(2, $external_ids, $tag_ids);
                        } catch (\Exception $e) {
                            Yii::error($e->getMessage(), 'setFollowUserWorkImportCustomer');
                        }
                    }
                    $isTag = 1;
                }
				//红包打标签
				if (!empty($followInfo['red_pack_id'])) {
					$red = RedPack::findOne($followInfo['red_pack_id']);
					if (!empty($red->tag_ids)) {
						$tag_ids        = explode(',', $red->tag_ids);
						$external_ids[] = $followUser->id;
						try {
							WorkTag::addUserTag(2, $external_ids, $tag_ids);
						} catch (\Exception $e) {
							Yii::error($e->getMessage(), 'setFollowUserRed');
						}
					}
					$isTag = 1;
				}
				//上面活动此时已有标签,下面更改状态要排除这些已有的
				$userTagId = [];
				if (!empty($tag_ids)) {
					$userTagList = WorkTagFollowUser::find()->where(['tag_id' => $tag_ids, 'follow_user_id' => $followUser->id, 'status' => 1])->all();
					if (!empty($userTagList)) {
						$userTagId = array_column($userTagList, 'tag_id');
					}
				}

				//当前已有标签
				if ($followUser->userid == $followUserId) {
					$haveTagId   = [];
					$havePerId   = [];
					$haveTagList = WorkTagFollowUser::find()->where(['and', ['follow_user_id' => $followUser->id, 'status' => 1], ['not', ['tag_id' => $userTagId]]])->all();
					if (!empty($haveTagList)) {
						$haveTagId = array_column($haveTagList, 'tag_id');
					}
					$havePerTagList = WorkPerTagFollowUser::find()->where(['follow_user_id' => $followUser->id, 'status' => 1])->all();
					if (!empty($havePerTagList)) {
						$havePerId = array_column($havePerTagList, 'id');
					}
				}

				$addTagName = [];
				$delTagName = [];
				$userPerId  = [];
				//同步企业微信
				if (!empty($followInfo['tags'])) {
					$workUser = WorkUser::findOne($userId);
					if (!empty($workUser)) {
						foreach ($followInfo['tags'] as $tagInfo) {
							$groupName = isset($tagInfo['group_name']) ? $tagInfo['group_name'] : '';
							$tagName   = isset($tagInfo['tag_name']) ? $tagInfo['tag_name'] : '';
							if (!empty($groupName) && !empty($tagName)) {
								if ($tagInfo['type'] == 1) {
									//企业标签
									$workTagGroup = WorkTagGroup::findOne(['corp_id' => $workUser->corp_id, 'group_name' => $groupName, 'type' => 0]);
									if (!empty($workTagGroup)) {
										$workTag = WorkTag::findOne(['corp_id' => $workUser->corp_id, 'tagname' => $tagInfo['tag_name'], 'group_id' => $workTagGroup->id, 'is_del' => 0]);
										if (!empty($workTag)) {
											array_push($userTagId, $workTag->id);
											$workTagFollow = WorkTagFollowUser::findOne(['tag_id' => $workTag->id, 'follow_user_id' => $followUser->id]);
											if (empty($workTagFollow)) {
												try {
													$workTagFollow                 = new WorkTagFollowUser();
													$workTagFollow->tag_id         = $workTag->id;
													$workTagFollow->follow_user_id = $followUser->id;
													$workTagFollow->corp_id        = $workUser->corp_id;
													$workTagFollow->status         = 1;
													if (!$workTagFollow->validate() || !$workTagFollow->save()) {
														Yii::error(SUtils::modelError($workTagFollow), 'tagMessage');
													}
													array_push($addTagName, '【' . $workTag->tagname . '】');
												} catch (\Exception $e) {
													\Yii::error($e->getMessage(), 'message');
												}
											} else {
												if ($workTagFollow->status == 0) {
													array_push($addTagName, '【' . $workTag->tagname . '】');
												}
												$workTagFollow->status = 1;
												$workTagFollow->save();
											}
										}
									}
								} else {
									//个人标签
									$contact = WorkExternalContact::findOne($followUser->external_userid);
									if (!empty($contact)) {
										$workPerTag = WorkPerTagFollowUser::findOne(['corp_id' => $contact->corp_id, 'group_name' => $groupName, 'tag_name' => $tagInfo['tag_name'], 'follow_user_id' => $followUser->id]);
										if (empty($workPerTag)) {
											$workPerTag                 = new WorkPerTagFollowUser();
											$workPerTag->group_name     = $groupName;
											$workPerTag->tag_name       = $tagInfo['tag_name'];
											$workPerTag->follow_user_id = $followUser->id;
											$workPerTag->corp_id        = $contact->corp_id;
											$workPerTag->status         = 1;
											if (!$workPerTag->validate() || !$workPerTag->save()) {
												Yii::error(SUtils::modelError($workPerTag), '$workPerTag');
											}
											if (!empty($tagName)) {
												array_push($addTagName, '【' . $tagName . '】');
											}
										} else {
											if ($workPerTag->status == 0 && !empty($tagName)) {
												array_push($addTagName, '【' . $tagName . '】');
											}
											$workPerTag->status = 1;
											$workPerTag->save();
										}
										array_push($userPerId, $workPerTag->id);
									}
								}
							}
						}
					}
//					WorkPerTagFollowUser::updateAll(['status' => 0], ['and', ['follow_user_id' => $followUser->id], ['not', ['id' => $userPerId]]]);
//					WorkTagFollowUser::updateAll(['status' => 0], ['and', ['follow_user_id' => $followUser->id], ['not', ['tag_id' => $userTagId]]]);
//					WorkUserTagExternal::updateAll(['status' => 0], ['and', ['user_id' => $userId, 'follow_user_id' => $followUser->id], ['not', ['tag_id' => $userTagId]]]);
				} else {
					if ($isTag == 0) {
//						WorkTagFollowUser::updateAll(['status' => 0], ['follow_user_id' => $followUser->id]);
//						WorkPerTagFollowUser::updateAll(['status' => 0], ['follow_user_id' => $followUser->id]);
//						WorkUserTagExternal::updateAll(['status' => 0], ['follow_user_id' => $followUser->id]);
					}
				}
				\Yii::error($userPerId, 'userPerId_' . $followUser->id);
				\Yii::error($userTagId, 'userTagId_' . $followUser->id);
				WorkPerTagFollowUser::updateAll(['status' => 0], ['and', ['follow_user_id' => $followUser->id], ['not', ['id' => $userPerId]]]);
				WorkTagFollowUser::updateAll(['status' => 0], ['and', ['follow_user_id' => $followUser->id], ['not', ['tag_id' => $userTagId]]]);
				WorkUserTagExternal::updateAll(['status' => 0], ['and', ['user_id' => $userId, 'follow_user_id' => $followUser->id], ['not', ['tag_id' => $userTagId]]]);

				//标签轨迹
				if ($followUser->userid == $followUserId) {
					//企业标签
					$delTagId = array_diff($haveTagId, $userTagId);
					if (!empty($delTagId)) {
						$delTagList = WorkTag::find()->where(['id' => $delTagId])->all();
						if (!empty($delTagList)) {
							foreach ($delTagList as $delTag) {
								array_push($delTagName, '【' . $delTag->tagname . '】');
							}
						}
					}
					//个人标签
					$delPerId = array_diff($havePerId, $userPerId);
					if (!empty($delTagId)) {
						$delPerList = WorkPerTagFollowUser::find()->where(['id' => $delPerId])->all();
						if (!empty($delPerList)) {
							foreach ($delPerList as $delPer) {
								array_push($delTagName, '【' . $delPer->tag_name . '】');
							}
						}
					}

					if (!empty($addTagName)) {
						ExternalTimeLine::addExternalTimeLine(['uid' => 0, 'user_id' => $followUser->user_id, 'external_id' => $followUser->external_userid, 'event' => 'add_tag', 'related_id' => $followUser->user_id, 'remark' => implode('、', $addTagName)]);
					}
					if (!empty($delTagName)) {
						ExternalTimeLine::addExternalTimeLine(['uid' => 0, 'user_id' => $followUser->user_id, 'external_id' => $followUser->external_userid, 'event' => 'del_tag', 'related_id' => $followUser->user_id, 'remark' => implode('、', $delTagName)]);
					}
				}

				//判断当前添加员工的活码是否配置了每日添加上限
				if (!empty($followUser->way_id)) {
					$everyContactWay = WorkContactWay::findOne($followUser->way_id);
					if (!empty($everyContactWay) && $everyContactWay->is_limit == 2) {
						$limitFlag     = false;
						$workUserLimit = WorkContactWayUserLimit::find()->where(['way_id' => $followUser->way_id])->all();
						if (!empty($workUserLimit)) {
							/** @var WorkContactWayUserLimit $limit */
							foreach ($workUserLimit as $limit) {
								if ($limit->limit > 0) {
									$todayTimeStart = strtotime(date('Y-m-d'));
									//获取当前员工今日添加客户数
									$hasCount = WorkExternalContactFollowUser::find()->where(['way_id' => $followUser->way_id, 'user_id' => $limit->user_id])->andFilterWhere(['between', 'createtime', $todayTimeStart, time()]);
									\Yii::error($hasCount->createCommand()->getRawSql(), 'sql1111');
									$hasCount = $hasCount->count();
									if ($hasCount >= $limit->limit) {
										$limitFlag = true;
									}
								}
							}
						}
						if ($limitFlag) {
							//根据添加上限再次生成活码
							WorkContactWay::getNewCode($everyContactWay->id, $everyContactWay->corp_id, $everyContactWay->open_date);
						}
					}
				}
				if (!empty($followUser->way_redpack_id)) {
					$everyContactWayRedpacket = WorkContactWayRedpacket::findOne($followUser->way_redpack_id);
					if (!empty($everyContactWayRedpacket) && $everyContactWayRedpacket->is_limit == 2) {
						$limitFlag     = false;
						$workUserLimit = WorkContactWayRedpacketUserLimit::find()->where(['way_id' => $followUser->way_redpack_id])->all();
						if (!empty($workUserLimit)) {
							foreach ($workUserLimit as $limit) {
								if ($limit->limit > 0) {
									$todayTimeStart = strtotime(date('Y-m-d'));
									//获取当前员工今日添加客户数
									$hasCount = WorkExternalContactFollowUser::find()->where(['way_redpack_id' => $followUser->way_redpack_id, 'user_id' => $limit->user_id])->andFilterWhere(['between', 'createtime', $todayTimeStart, time()]);
									$hasCount = $hasCount->count();
									if ($hasCount >= $limit->limit) {
										$limitFlag = true;
									}
								}
							}
						}
						if ($limitFlag) {
							//根据添加上限再次生成活码
							WorkContactWayRedpacket::getNewCode($everyContactWayRedpacket->id, $everyContactWayRedpacket->corp_id, $everyContactWayRedpacket->open_date);
						}
					}
				}

//				if (!empty($followInfo['tags'])) {
//					$workUser = WorkUser::findOne($userId);
//					if (!empty($workUser)) {
//						foreach ($followInfo['tags'] as $tagInfo) {
//							$groupName    = $tagInfo['group_name'];
//							$workTagGroup = WorkTagGroup::findOne(['corp_id' => $workUser->corp_id, 'group_name' => $groupName, 'type' => 0]);
//							if (!empty($workTagGroup)) {
//								$workTag = WorkTag::findOne(['corp_id' => $workUser->corp_id, 'tagname' => $tagInfo['tag_name'], 'group_id' => $workTagGroup->id, 'is_del' => 0]);
//								if (!empty($workTag)) {
//									$workTagContact = WorkTagContact::findOne(['tag_id' => $workTag->id, 'contact_id' => $externalUserId]);
//									if (empty($workTagContact)) {
//										try {
//											$workTagContact             = new WorkTagContact();
//											$workTagContact->tag_id     = $workTag->id;
//											$workTagContact->contact_id = $externalUserId;
//											if (!$workTagContact->validate() || !$workTagContact->save()) {
//												Yii::error(SUtils::modelError($workTagContact), 'sql');
//											}
//										} catch (\Exception $e) {
//											\Yii::error($e->getMessage(), 'message');
//										}
//									}
//								}
//							}
//						}
//					}
//				}

				//渠道活码统计
				if (!empty($followUser->way_id) && $followUser->state == $state && $followUser->userid == $followUserId) {
					$contact = WorkExternalContact::findOne($followUser->external_userid);
					$gender  = isset($contact->gender) ? $contact->gender : 0;
					WorkContactWayLine::add($followUser->way_id, $gender, 1, $externalUserId, $userId);
				}
				//更新已认领的状态
				$isSeaRemark = 0;
				if (!empty($isClaimUser)) {
					$claimUser = PublicSeaClaimUser::findOne(['corp_id' => $corpId, 'external_userid' => $externalUserId, 'new_user_id' => $userId, 'new_follow_user_id' => 0]);
					if (!empty($claimUser)) {
						$followUser->other_way = 1;
						$followUser->update();
						$isSeaRemark = 1;
					}
					PublicSeaClaimUser::updateAll(['new_follow_user_id' => $followUser->id, 'status' => 1], ['corp_id' => $corpId, 'external_userid' => $externalUserId, 'new_user_id' => $userId, 'new_follow_user_id' => 0]);
				}
				//记录客户轨迹
				if ($isTimeLine == 1 || $isTimeLine == 2) {
					//内部成员分享标签同步
					if ($isTimeLine == 1 && !empty($followInfo['add_way']) && ($followInfo['add_way'] == WorkExternalContactFollowUser::USER_SHARE_ADD_WAY) && !empty($followInfo['oper_userid'])) {
						$oldWorkUser = WorkUser::findOne(['corp_id' => $corpId, 'userid' => $followInfo['oper_userid']]);
						if (!empty($oldWorkUser)) {
							$oldFollowUser = WorkExternalContactFollowUser::findOne(['external_userid' => $externalUserId, 'user_id' => $oldWorkUser->id]);
							if (!empty($oldFollowUser)) {
								$tagUser = WorkTagFollowUser::find()->where(['corp_id' => $corpId, 'follow_user_id' => $oldFollowUser->id, 'status' => 1])->all();
								if (!empty($tagUser)) {
									$newTagIds = array_column($tagUser, 'tag_id');
									WorkTag::addUserTag(2, [$followUser->id], $newTagIds);
								}
							}
						}
					}
					$add = '添加';
					if ($isTimeLine == 2) {
						$add = '重复添加';
					}
					$workUser = WorkUser::findOne($userId);
					$remark   = '';
					$eventId  = 0;
					if (!empty($followInfo['way_id'])) {
						$eventId = $followInfo['way_id'];
						$remark  .= '客户通过扫渠道活码【' . $work_contact_way->title . '】' . $add . '企业成员【' . $workUser->name . '】';
					} elseif (!empty($followInfo['baidu_way_id'])) {
						$eventId = $followInfo['baidu_way_id'];
						$remark  .= '客户通过扫百度活码【' . $baiDuInfo->title . '】' . $add . '企业成员【' . $workUser->name . '】';
					} elseif (!empty($followInfo['way_redpack_id'])) {
						$eventId = $followInfo['way_redpack_id'];
						$remark  .= '客户通过红包拉新活动【' . $redpacketInfo->name . '】' . $add . '企业成员【' . $workUser->name . '】';
					} elseif (!empty($followInfo['fission_id'])) {
						$eventId = $followInfo['fission_id'];
						$fission = Fission::findOne($followInfo['fission_id']);
						$remark  .= '客户通过裂变活动【' . $fission->title . '】' . $add . '企业成员【' . $workUser->name . '】';
					} elseif (!empty($followInfo['award_id'])) {
						$eventId = $followInfo['award_id'];
						$award   = AwardsActivity::findOne($followInfo['award_id']);
						$remark  .= '客户通过抽奖活动【' . $award->title . '】' . $add . '企业成员【' . $workUser->name . '】';
					} elseif (!empty($followInfo['red_pack_id'])) {
						$eventId = $followInfo['red_pack_id'];
						$redPack = RedPack::findOne($followInfo['red_pack_id']);
						$remark  .= '客户通过红包裂变活动【' . $redPack->title . '】' . $add . '企业成员【' . $workUser->name . '】';
					} elseif (!empty($followInfo['chat_way_id'])) {
						$eventId = $followInfo['chat_way_id'];
						$remark  .= '客户通过扫群活码【' . $chatWay->title . '】' . $add . '企业成员【' . $workUser->name . '】';
					} elseif (!empty($followInfo['activity_id'])) {
						$eventId  = $followInfo['activity_id'];
						$activity = WorkPublicActivity::findOne($followInfo['activity_id']);
						$remark   .= '客户通过扫裂变引流【' . $activity->activity_name . '】' . $add . '企业成员【' . $workUser->name . '】';
					} elseif (!empty($followInfo['punch_id'])) {
						$punchId  = $followInfo['punch_id'];
						$activity = WorkGroupClockActivity::findOne($punchId);
						$remark   .= '客户通过群打卡【' . $activity->title . '】' . $add . '企业成员【' . $workUser->name . '】';
					} else {
						$wayInfo = static::getAddWay($followUser->add_way);
						if ($followUser->add_way == 2) {
							if ($followUser->oper_userid == $followUser->userid) {
								$remark .= '企业成员【' . $workUser->name . '】通过' . $wayInfo . $add . '客户';
							} else {
								$remark .= '客户通过' . $wayInfo . $add . '企业成员【' . $workUser->name . '】';
							}
						} else {
							$wayInfo = !empty($wayInfo) ? $wayInfo : '其它渠道';
							$remark  .= '客户通过' . $wayInfo . $add . '企业成员【' . $workUser->name . '】';
						}
					}
					//从公海池过来的轨迹
					if (!empty($isSeaRemark)) {
						if (!empty($claimUser->claim_str)) {
							$remark = $claimUser->claim_str;
						} elseif (!empty($remark)) {
							$remark = '公海池认领：' . $remark;
						}
					}
					ExternalTimeLine::addExternalTimeLine(['external_id' => $externalUserId, 'event' => 'add_user', 'event_id' => $eventId, 'related_id' => $followUser->id, 'remark' => $remark]);
				}

				//beenlee 判断添加客户任务增加明细
//				WorkTaskCustomerList::addCustomer($corpId, $userId, $externalUserId, $externalOpenid);

			} catch (\Exception $e) {
				Yii::error($e->getMessage(), 'setFollowUser');
			}
			//电商系统-新增企业用户添加顾客
            if(!empty($followUser->id)){
                ShopCustomer::clearWorkUser(0,$followUser->id);
            }
			return $followUser->id;
		}

		/**
		 * 导出客户数量发送
		 *
		 * @param $uid
		 * @param $corpId
		 * @param $total
		 * @param $exportNum
		 * @param $url
		 *
		 * @return bool
		 *
		 */
		public static function exportNumWebsocket ($uid, $corpId, $total, $exportNum, $url = '')
		{
			\Yii::$app->websocket->send([
				'channel' => 'push-message',
				'to'      => $uid,
				'type'    => WebsocketUtil::EXPORT_EXTERNAL_TYPE,
				'info'    => [
					'type'       => 'export_customer',
					'from'       => $uid,
					'corpid'     => $corpId,
					'snum'       => $total,
					'export_num' => $exportNum,
					'url'        => $url,
				]
			]);

			return true;
		}

		/**
		 * 群发客户查询  根据条件查询客户
		 *
		 * @param $data
		 *
		 * @return array
		 *
		 * @throws InvalidParameterException
		 * @throws \yii\db\Exception
		 */
		public static function getData ($data)
		{
			$corpId          = $data['corp_id'];
			$isMasterAccount = $data['isMasterAccount'];
			$sub_id          = $data['sub_id'];
			$name            = isset($data['name']) ? $data['name'] : '';
			$phone           = isset($data['phone']) ? $data['phone'] : '';
			$sex             = $data['sex'];
			$work            = isset($data['work']) ? $data['work'] : '';
			$province        = $data['province'];
			$city            = $data['city'];
			$follow_status   = isset($data['follow_status']) ? $data['follow_status'] : -1;
			$follow_id       = $data['follow_id'];
			$fieldData       = $data['fieldData'];
			$tag_ids         = $data['tag_ids'];
			$group_id        = isset($data['group_id']) ? $data['group_id'] : '';
			$tag_type        = isset($data['tag_type']) ? $data['tag_type'] : 1;
			$start_time      = $data['start_time'];
			$end_time        = $data['end_time'];
			$correctness     = $data['correctness'];
			$update_time     = $data['update_time'];
			$follow_num1     = $data['follow_num1'];
			$follow_num2     = $data['follow_num2'];
			$chat_time       = $data['chat_time'];
			$sign_id         = $data['sign_id'];
			$chat_id         = $data['chat_id'];
			$user_ids        = $data['user_ids'];
			$belong_id       = isset($data['belong_id']) ? $data['belong_id'] : 0;
			$is_fans         = isset($data['is_fans']) ? $data['is_fans'] : 0;
			$is_follow_full  = isset($data['is_follow_full']) ? $data['is_follow_full'] : 0;
			$external_id     = isset($data['external_id']) ? $data['external_id'] : -1;
			$uid             = $data['uid'];
			if (!empty($belong_id)) {
				$sendId = [];
				if ($belong_id == 1) {
					$workUser = WorkUser::find()->where(['corp_id' => $corpId])->asArray()->all();
					if (!empty($workUser)) {
						foreach ($workUser as $user) {
							$sendId[] = $user['id'];
						}
					}
					$user_ids = $sendId;
				}
			}

			$workCorp = WorkCorp::findOne($corpId);

			$workExternalUserData = WorkExternalContactFollowUser::find()->alias('wf');
			$workExternalUserData = $workExternalUserData->leftJoin('{{%work_external_contact}} we', 'we.id=wf.external_userid');
			$workExternalUserData = $workExternalUserData->andWhere(['we.corp_id' => $corpId, 'wf.del_type' => [WorkExternalContactFollowUser::WORK_CON_EX, WorkExternalContactFollowUser::NO_ASSIGN]]);
			if ($isMasterAccount == 2) {
				$sub_detail = AuthoritySubUserDetail::getDepartmentUserLists($sub_id, $corpId);
				if (is_array($sub_detail)) {
					$userDetail           = WorkUser::find()->where(['id' => $sub_detail, 'is_del' => 0])->select('id')->asArray()->all();
					$sub_detail           = array_column($userDetail, 'id');
					$workExternalUserData = $workExternalUserData->andWhere(['wf.user_id' => $sub_detail]);
				} else if ($sub_detail === false) {
					return ["real_num" => 0, "result" => []];
				}

//				$subUser = SubUser::findOne($sub_id);
//				if ($subUser->sub_id != 61 && $subUser->sub_id != 32) {
//					$department        = '';
//					$is_leader_in_dept = '';
//					if (!empty($subUser)) {
//						$workUser = WorkUser::findOne(['corp_id' => $corpId, 'mobile' => $subUser->account]);
//						if (!empty($workUser)) {
//							$userId            = $workUser->id;
//							$department        = $workUser->department;
//							$is_leader_in_dept = $workUser->is_leader_in_dept;
//						}
//					}
//					if (!empty($department)) {
//						$userID = WorkDepartment::getDepartId($department, $corpId, $is_leader_in_dept);
//						if (!empty($userID)) {
//							array_push($userID, $userId);
//						} else {
//							$userID = $userId;
//						}
//					} else {
//						$userID = $userId;
//					}
//					$workExternalUserData = $workExternalUserData->andWhere(['wf.user_id' => $userID]);
//				}

			}
			//高级属性搜索
			$fieldList    = CustomField::find()->where('is_define=0')->select('`id`,`key`')->asArray()->all();//默认属性
			$fieldD       = [];
			$contactField = [];//列表展示字段
			foreach ($fieldList as $k => $v) {
				$fieldD[$v['key']] = $v['id'];
				if (in_array($v['key'], ['name', 'sex', 'phone', 'area'])) {
					$contactField[] = $v['id'];
				}
			}
			if (!empty($update_time)) {
				$workExternalUserData = $workExternalUserData->andFilterWhere(['between', 'wf.update_time', strtotime($update_time[0]), strtotime($update_time[1] . ':59')]);
			}

			if (!empty($is_fans)) {
				if ($is_fans == 1) {
					$workExternalUserData = $workExternalUserData->andWhere(['we.is_fans' => 1]);
				} else {
					$workExternalUserData = $workExternalUserData->andWhere(['we.is_fans' => 0]);
				}
			}

			if ($follow_num1 != '' || $follow_num2 != '') {
				$follow_num = '';
				if (($follow_num1 == '0' && $follow_num2 == '0') || ($follow_num1 == '' && $follow_num2 == '0') || ($follow_num1 == '0' && $follow_num2 == '')) {
					$follow_num = 0;
				}
				if ((($follow_num1 == '' || $follow_num == '0') && $follow_num2 > 0)) {
					$follow_num = $follow_num2;
				}
				if (($follow_num1 > 0 && ($follow_num2 == '' || $follow_num2 == '0'))) {
					$follow_num = $follow_num1;
				}
				if (!empty($follow_num) || $follow_num == '0') {
					$workExternalUserData = $workExternalUserData->andWhere(['wf.follow_num' => $follow_num]);
				} else {
					$workExternalUserData = $workExternalUserData->andWhere(['>=', 'wf.follow_num', $follow_num1]);
					$workExternalUserData = $workExternalUserData->andWhere(['<=', 'wf.follow_num', $follow_num2]);
				}
			}

			if (!empty($chat_time)) {
				$contactId      = [];
				$chartStartTime = strtotime($chat_time[0]) . '000';
				$chartEndTime   = strtotime($chat_time[1] . ':59') . '000';
				$sql            = 'SELECT external_id, msgtime FROM ( SELECT `wa`.`external_id`, wa.msgtime FROM {{%work_msg_audit_info}} `wa` LEFT JOIN {{%work_msg_audit}} `w` ON w.corp_id = wa.audit_id WHERE (`w`.`corp_id` = 1) AND (`wa`.`external_id` != \'\') ORDER BY msgtime DESC ) AS a GROUP BY external_id';
				$auditInfo      = \Yii::$app->getDb()->createCommand($sql)->queryAll();
				if (!empty($auditInfo)) {
					foreach ($auditInfo as $info) {
						if ($info['msgtime'] >= $chartStartTime && $info['msgtime'] <= $chartEndTime) {
							$contactId[] = $info['external_id'];
						}
					}
				}
				$workExternalUserData = $workExternalUserData->andWhere(['we.id' => $contactId]);
			}

			if (!empty($sign_id)) {
				$contactId = [];
				$member    = WorkExternalContactMember::find()->where(['sign_id' => $sign_id, 'is_bind' => 1])->select('external_userid')->groupBy('external_userid');
				$member    = $member->asArray()->all();
				if (!empty($member)) {
					foreach ($member as $mem) {
						$contactId[] = $mem['external_userid'];
					}
				}
				$workExternalUserData = $workExternalUserData->andWhere(['we.id' => $contactId]);
			}

			if (!empty($user_ids)) {
				$workExternalUserData = $workExternalUserData->andWhere(['in', 'wf.user_id', $user_ids]);
			}

			if ($external_id != '-1') {
				$workExternalUserData = $workExternalUserData->andWhere(['in', 'we.external_userid', $external_id]);
			}

			if ($correctness == 2) {
				if (!empty($start_time) && empty($end_time)) {
					throw new InvalidParameterException('请填写结束时间！');
				}
				if (!empty($end_time) && empty($start_time)) {
					throw new InvalidParameterException('请填写开始时间！');
				}
				if (!empty($start_time) && !empty($end_time)) {
					$workExternalUserData = $workExternalUserData->andFilterWhere(['between', 'createtime', strtotime($start_time), strtotime($end_time . ':59')]);
				}
				if ($follow_status != '-1') {
					$workExternalUserData = $workExternalUserData->andWhere(['we.follow_status' => $follow_status]);
				}
				if ($follow_id != '-1') {
					$workExternalUserData = $workExternalUserData->andWhere(['wf.follow_id' => $follow_id]);
				}
				//标签搜索
				$tagIds = $tag_ids ? (is_array($tag_ids) ? $tag_ids : explode(',', $tag_ids)) : [];
				if (!empty($tagIds) && in_array($tag_type, [1, 2, 3])) {
					$userTag = WorkTagFollowUser::find()
                        ->alias('wtf')
                        ->innerJoin('{{%work_tag}} wtg', '`wtf`.`tag_id` = `wtg`.`id` AND wtg.`is_del` = 0')
                        ->where(['wtf.corp_id' => $corpId,'wtg.corp_id' => $corpId,'wtf.status' => 1])
                        ->groupBy('wtf.follow_user_id')
                        ->select('wtf.follow_user_id,GROUP_CONCAT(wtg.id) tag_ids');

                    $workExternalUserData = $workExternalUserData->leftJoin(['wt' => $userTag], '`wt`.`follow_user_id` = `wf`.`id`');
                    $tagsFilter = [];
                    if ($tag_type == 1) {//标签或
                        $tagsFilter[] = 'OR';
                        array_walk($tagIds, function($value) use (&$tagsFilter){
                            $tagsFilter[] = ($value == -1) ? ['wt.tag_ids' => NULL] : (new Expression("FIND_IN_SET($value,wt.tag_ids)"));
                        });
                    }elseif ($tag_type == 2) {//标签且
                        $tagsFilter[] = 'AND';
                        array_walk($tagIds, function($value) use (&$tagsFilter){
                            $tagsFilter[] = ($value == -1) ? ['wt.tag_ids' => NULL] : (new Expression("FIND_IN_SET($value,wt.tag_ids)"));
                        });
                    }elseif ($tag_type == 3) {//标签不包含
                        $tagsFilter[] = 'AND';
                        array_walk($tagIds, function($value) use (&$tagsFilter){
                            $tagsFilter[] = ($value == -1) ? ['is not','wt.tag_ids',NULL] : (new Expression("NOT FIND_IN_SET($value,IFNULL(wt.tag_ids,''))"));
                        });
                    }
                    $workExternalUserData->andWhere($tagsFilter);
				}
				if ($name || $phone || $work || $province || $sex != '-1' || !empty($fieldData)) {
					$i            = 0;
					$customValue  = [];
					$newValue     = [];
					$fieldSubUser = [];
					if ($isMasterAccount == 2 && $workCorp->unshare_field == 1) {
						if (!empty($sub_detail) && is_array($sub_detail)) {
							$fieldSubUser = array_merge($sub_detail, [0]);
						}
					}
					if ($sex != '-1') {
						if ($sex == 1) {
							$sex = '男';
						} elseif ($sex == 2) {
							$sex = '女';
						} else {
							$sex = ['未知'];
						}
						$custom = CustomFieldValue::find()->where(['in', 'uid', [0, $uid]])->andWhere(['type' => 1, 'fieldid' => $fieldD['sex'], 'value' => $sex]);
						if ($isMasterAccount == 2 && $workCorp->unshare_field == 1 && !empty($fieldSubUser)) {
							$custom = $custom->andWhere(['user_id' => $fieldSubUser]);
						}
						$custom = $custom->select('`cid`')->groupBy('cid')->asArray()->all();

						$customId      = array_column($custom, 'cid');
						$customValue[] = $customId;
						$i++;
					}

					if ($isMasterAccount == 2 && $workCorp->unshare_field == 1) {
						$workExternalUserData = $workExternalUserData->leftJoin('{{%custom_field_value}} cf', '`cf`.`cid` = `we`.`id` AND `cf`.`type`=1 AND `cf`.`user_id` in (' . implode(',', $fieldSubUser) . ')');
					} else {
						$workExternalUserData = $workExternalUserData->leftJoin('{{%custom_field_value}} cf', '`cf`.`cid` = `we`.`id` AND `cf`.`type`=1');
					}
					if (!empty($name)) {
						$workExternalUserData = $workExternalUserData->andWhere(' we.name_convert like \'%' . $name . '%\' or wf.remark like \'%' . $name . '%\' or wf.nickname like \'%' . $name . '%\' or (cf.fieldid in (' . $fieldD['company'] . ',' . $fieldD['name'] . ') and cf.value like \'%' . $name . '%\')');
					}

					if (!empty($phone)) {
						$custom = CustomFieldValue::find()->where(['and', ['in', 'fieldid', [$fieldD['phone'], $fieldD['qq']]], ['like', 'value', $phone]]);
						if ($isMasterAccount == 2 && $workCorp->unshare_field == 1 && !empty($fieldSubUser)) {
							$custom = $custom->andWhere(['user_id' => $fieldSubUser]);
						}
						$custom        = $custom->select('cid')->groupBy('cid')->asArray()->all();
						$customId      = array_column($custom, 'cid');
						$customValue[] = $customId;
						$i++;
					}
					if (!empty($work)) {
						$custom = CustomFieldValue::find()->where(['fieldid' => $fieldD['work'], 'value' => $work]);
						if ($isMasterAccount == 2 && $workCorp->unshare_field == 1 && !empty($fieldSubUser)) {
							$custom = $custom->andWhere(['user_id' => $fieldSubUser]);
						}
						$custom        = $custom->select('cid')->groupBy('cid')->asArray()->all();
						$customId      = array_column($custom, 'cid');
						$customValue[] = $customId;
						$i++;
					}
					if (!empty($province)) {
						if (!empty($city)) {
							$custom = CustomFieldValue::find()->where(['fieldid' => $fieldD['area'], 'value' => $province . '-' . $city]);
							if ($isMasterAccount == 2 && $workCorp->unshare_field == 1 && !empty($fieldSubUser)) {
								$custom = $custom->andWhere(['user_id' => $fieldSubUser]);
							}
							$custom        = $custom->select('cid')->groupBy('cid')->asArray()->all();
							$customId      = array_column($custom, 'cid');
							$customValue[] = $customId;
							$i++;

						} else {
							$custom = CustomFieldValue::find()->where(['and', ['fieldid' => $fieldD['area']], ['like', 'value', $province . '-']]);
							if ($isMasterAccount == 2 && $workCorp->unshare_field == 1 && !empty($fieldSubUser)) {
								$custom = $custom->andWhere(['user_id' => $fieldSubUser]);
							}
							$custom        = $custom->select('cid')->groupBy('cid')->asArray()->all();
							$customId      = array_column($custom, 'cid');
							$customValue[] = $customId;
							$i++;
						}
					}
					if (!empty($fieldData)) {
						foreach ($fieldData as $val) {
							if(!is_array($val['match'])){
								$val['match'] = [$val['match']];
							}

							$var_match_where = ['or'];
							foreach ($val['match'] as $var_key => $var_match) {
								$var_match_where[] = new Expression("FIND_IN_SET(:value_" . $var_key . ", value)", [":value_" . $var_key => $var_match]);
							}

							$custom = CustomFieldValue::find()->where(['fieldid' => $val['field']])->andWhere($var_match_where);
							if ($isMasterAccount == 2 && $workCorp->unshare_field == 1 && !empty($fieldSubUser)) {
								$custom = $custom->andWhere(['user_id' => $fieldSubUser]);
							}
							$custom        = $custom->select('cid')->groupBy('cid')->asArray()->all();
							$customId      = array_column($custom, 'cid');
							$customValue[] = $customId;
							$i++;

						}
					}

					$new = [];
					if (!empty($customValue)) {
						foreach ($customValue as $value) {
							foreach ($value as $val) {
								$new[] = $val;
							}
						}
						$newVal = array_count_values($new);
						if (!empty($newVal)) {
							foreach ($newVal as $k => $v) {
								if ($v == $i) {
									$newValue[] = $k;
								}
							}
						}
					}
					$workExternalUserData = $workExternalUserData->andWhere(['in', 'we.id', $newValue]);
				}
			}

			$users = [];
			if (!empty($chat_id)) {
				$chatInfo = WorkChatInfo::find()->where(['chat_id' => $chat_id, 'status' => 1, 'type' => 2])->select('external_id');
				if ($is_follow_full == 0) {
					$chatInfo = $chatInfo->groupBy('external_id');
				}
				$chatInfo = $chatInfo->asArray()->all();
				if (!empty($chatInfo)) {
					foreach ($chatInfo as $info) {
						$users[] = $info['external_id'];
					}
				}
				if (!empty($users)) {
					$workExternalUserData = $workExternalUserData->andWhere(['not in', 'wf.external_userid', $users]);
				}
			}

			$result               = [];
			$externalId           = [];

			if ($is_follow_full == 0) {
				$workExternalUserData = $workExternalUserData->select('we.id as wid');
				$workExternalUserData = $workExternalUserData->groupBy('we.id');
			}else{
				$workExternalUserData = $workExternalUserData->select('wf.id as wid');
				$workExternalUserData = $workExternalUserData->groupBy('wf.id');
			}
			$workExternalUserData = $workExternalUserData->asArray()->all();
			if (!empty($workExternalUserData)) {
				//剩余群发次数过滤
				$externalStatistic = WorkGroupSending::getExternalMonthCount($corpId);

                $filterExternalStatistic = [];
                if (!empty($externalStatistic) && empty($data["is_moment"])) {
                    foreach ($externalStatistic as $sta) {
                        if ($sta['cc'] >= 4) {
                            //当前客户超过4次 需要过滤
                            array_push($filterExternalStatistic,$sta['external_id']);
                        }
                    }
                }
				$i = 0;
				foreach ($workExternalUserData as $key => $val) {
					$flag = false;
					if ($is_follow_full == 0) {
						if (!empty($externalStatistic) && empty($data["is_moment"])) {
						    if(in_array($val['wid'],$filterExternalStatistic)){
                                $flag = true;
                            }
//							foreach ($externalStatistic as $sta) {
//								if ($sta['external_id'] == $val['wid'] && $sta['cc'] >= 4) {
//									//当前客户超过4次 需要过滤
//									$flag = true;
//								}
//							}
						}
					}
					if (!$flag) {
						$result[$i]['key'] = $val['wid'];
						$externalId[]      = $val['wid'];
						$i++;
					}
				}
			}
			$real_num = 0;
			if (!empty($chat_id)) {
				$num = 0;
				if (!empty($users)) {
					foreach ($users as $v) {
						if (in_array($v, $externalId, true)) {
							$num++;
						}
					}
				}
				$real_num = count($externalId) - $num;
			}

			return [
				'real_num' => $real_num,
				'result'   => $result,
			];
		}

		/**
		 * @param $contactId
		 * @param $sendId
		 *
		 * @return array
		 *
		 */
		public static function getUserSendData ($contactId, $sendId)
		{
			$externalUserid = [];
			foreach ($contactId as $cId) {
				$follow = WorkExternalContactFollowUser::findOne(['external_userid' => $cId, 'userid' => $sendId, 'del_type' => WorkExternalContactFollowUser::WORK_CON_EX]);
				if (!empty($follow)) {
					$contact = WorkExternalContact::findOne($cId);
					if (!empty($contact)) {
						array_push($externalUserid, $contact->external_userid);
					}
				}
			}

			return $externalUserid;
		}

		/**
		 * @param $workExternalUserDataNew
		 * @param $corpId
		 * @param $userId
		 * @param $data
		 *
		 * @return mixed
		 *
		 * @throws \yii\db\Exception
		 */
		public static function getCondition ($workExternalUserDataNew, $corpId, $data)
		{
			//$userIds     = $data['userIds'];
			$day         = $data['day'];
			$update_time = $data['update_time'];
			$follow_num1 = $data['follow_num1'];
			$follow_num2 = $data['follow_num2'];
			$chat_time   = $data['chat_time'];
			$sign_id     = $data['sign_id'];
			$user_ids    = $data['user_ids'];
			$start_time  = $data['start_time'];
			$end_time    = $data['end_time'];
			$follow_id   = $data['follow_id'];
			$add_way     = $data['add_way'];
			$way_id      = $data['way_id'];
			$chat_id     = $data['chat_id'];
			$chat_type   = $data['chat_type'];
			$tag_type    = $data['tag_type'];
			$tag_ids     = $data['tag_ids'];
			$group_id    = $data['group_id'];
			$no_tag      = $data['no_tag'];
			$name        = $data['name'];
			$phone       = $data['phone'];
			$work        = $data['work'];
			$province    = $data['province'];
			$city        = $data['city'];
			$sex         = $data['sex'];
			$fieldData   = $data['fieldData'];
			$uid         = $data['uid'];
			$fieldD      = $data['fieldD'];
			$type        = $data['type'];
			$is_fans     = $data['is_fans'];
			$status_id   = $data['status_id'];
			$isProtect   = $data['is_protect'];

			$workCorp = WorkCorp::findOne($corpId);

			if (!empty($update_time)) {
				$workExternalUserDataNew = $workExternalUserDataNew->andFilterWhere(['between', 'wf.update_time', strtotime($update_time[0]), strtotime($update_time[1] . ':59')]);
			}

			if (!empty($type)) {
				$sTime = '';
				$eTime = '';
				switch ($type) {
					case 1:
						$sTime = strtotime(date('Y-m-d'));
						$eTime = strtotime(date('Y-m-d') . ' 23:59:59');
						break;
					case 2:
						$sDefaultDate = date("Y-m-d");
						$w            = date('w', strtotime($sDefaultDate));
						$weekStart    = date('Y-m-d', strtotime("$sDefaultDate -" . ($w ? $w - 1 : 6) . ' days'));
						$weekEnd      = date('Y-m-d', strtotime("$weekStart +6 days"));
						$sTime        = strtotime($weekStart);
						$eTime        = strtotime($weekEnd . ' 23:59:59');
						break;
					case 3:
						$firstDay = date('Y-m-01', strtotime(date("Y-m-d")));
						$lastDay  = date('Y-m-d', strtotime("$firstDay +1 month -1 day"));
						$sTime    = strtotime($firstDay);
						$eTime    = strtotime($lastDay . ' 23:59:59');
						break;
				}

				if (!empty($sTime) && !empty($eTime)) {
					$workExternalUserDataNew = $workExternalUserDataNew->andFilterWhere(['between', 'wf.createtime', $sTime, $eTime]);
				}

			}
			$e_date = date('Y-m-d');
			if ($status_id == WorkExternalContactFollowRecord::ALL_DAY) {
				//$time2 = strtotime($e_date);
				//$workExternalUserDataNew = $workExternalUserDataNew->andWhere(['<', 'wf.update_time', $time2]);
				$workExternalUserDataNew = $workExternalUserDataNew->andWhere('wf.update_time = wf.createtime');
			}
			if ($status_id == WorkExternalContactFollowRecord::ONE_DAY) {
				$time2                   = strtotime($e_date) - (24 * 3600 * 1);
				$workExternalUserDataNew = $workExternalUserDataNew->andWhere(['<', 'wf.update_time', $time2]);
			}
			if ($status_id == WorkExternalContactFollowRecord::THREE_DAY) {
				$time2                   = strtotime($e_date) - (24 * 3600 * 3);
				$workExternalUserDataNew = $workExternalUserDataNew->andWhere(['<', 'wf.update_time', $time2]);
			}
			if ($status_id > 0) {
				$day                     = WorkNotFollowDay::findOne($status_id);
				$time2                   = strtotime($e_date) - (24 * 3600 * $day->day);
				$workExternalUserDataNew = $workExternalUserDataNew->andWhere(['<', 'wf.update_time', $time2]);
			}

			if ($isProtect != '-1') {
				$workExternalUserDataNew = $workExternalUserDataNew->andWhere(['wf.is_protect' => $isProtect]);
			}

			if (!empty($is_fans)) {
				if ($is_fans == 1) {
					$workExternalUserDataNew = $workExternalUserDataNew->andWhere(['we.is_fans' => 1]);
				} else {
					$workExternalUserDataNew = $workExternalUserDataNew->andWhere(['we.is_fans' => 0]);
				}
			}
			if ($follow_num1 != '' || $follow_num2 != '') {
				if (($follow_num1 == '0' && $follow_num2 == '0')) {
					$follow_num              = '0';
					$workExternalUserDataNew = $workExternalUserDataNew->andWhere(['wf.follow_num' => $follow_num]);
				} else {
					if ((($follow_num1 == '') && $follow_num2 >= 0)) {
						$workExternalUserDataNew = $workExternalUserDataNew->andWhere(['>=', 'wf.follow_num', 0]);
						$workExternalUserDataNew = $workExternalUserDataNew->andWhere(['<=', 'wf.follow_num', $follow_num2]);
					}
					if (($follow_num1 >= 0 && ($follow_num2 == ''))) {
						$workExternalUserDataNew = $workExternalUserDataNew->andWhere(['>=', 'wf.follow_num', $follow_num1]);
					}
					if (!empty($follow_num1) && !empty($follow_num2)) {
						$workExternalUserDataNew = $workExternalUserDataNew->andWhere(['>=', 'wf.follow_num', $follow_num1]);
						$workExternalUserDataNew = $workExternalUserDataNew->andWhere(['<=', 'wf.follow_num', $follow_num2]);
					}
				}

			}

//			if (!empty($day)) {
//				switch ($day) {
//					case 1:
//						//$followUser = $followUser->andWhere(['wf.update_time' => 'wf.createtime']);
//						$workExternalUserDataNew = $workExternalUserDataNew->andWhere('wf.update_time=wf.createtime');
//						break;
//					case 2:
//						$time                    = Follow::getTime(1);
//						$workExternalUserDataNew = $workExternalUserDataNew->andWhere(['OR', ['<', 'wf.update_time', $time], 'wf.update_time=wf.createtime']);
//						break;
//					case 3:
//						$time                    = Follow::getTime(2);
//						$workExternalUserDataNew = $workExternalUserDataNew->andWhere(['OR', ['<', 'wf.update_time', $time], 'wf.update_time=wf.createtime']);
//						break;
//					case 4:
//						$time                    = Follow::getTime(3);
//						$workExternalUserDataNew = $workExternalUserDataNew->andWhere(['OR', ['<', 'wf.update_time', $time], 'wf.update_time=wf.createtime']);
//						break;
//					case 5:
//						$time                    = Follow::getTime(4);
//						$workExternalUserDataNew = $workExternalUserDataNew->andWhere(['OR', ['<', 'wf.update_time', $time], 'wf.update_time=wf.createtime']);
//						break;
//					case 6:
//						$time                    = Follow::getTime(5);
//						$workExternalUserDataNew = $workExternalUserDataNew->andWhere(['OR', ['<', 'wf.update_time', $time], 'wf.update_time=wf.createtime']);
//						break;
//					case 7:
//						$time                    = Follow::getTime(6);
//						$workExternalUserDataNew = $workExternalUserDataNew->andWhere(['OR', ['<', 'wf.update_time', $time], 'wf.update_time=wf.createtime']);
//						break;
//					case 8:
//						$time                    = Follow::getTime(7);
//						$workExternalUserDataNew = $workExternalUserDataNew->andWhere(['OR', ['<', 'wf.update_time', $time], 'wf.update_time=wf.createtime']);
//						break;
//					case 9:
//						$time                    = Follow::getTime(8);
//						$workExternalUserDataNew = $workExternalUserDataNew->andWhere(['OR', ['<', 'wf.update_time', $time], 'wf.update_time=wf.createtime']);
//						break;
//				}
//			}

			if (!empty($chat_time)) {
				$contactId      = [];
				$chartStartTime = strtotime($chat_time[0]) . '000';
				$chartEndTime   = strtotime($chat_time[1] . ':59') . '000';
				$sql            = 'SELECT we.id, max(wa.msgtime) AS msgtime FROM {{%work_msg_audit_info}} AS wa LEFT JOIN {{%work_external_contact}} AS we ON we.external_userid = ( CASE WHEN wa.from_type = 1 THEN wa.tolist ELSE wa.from END ) WHERE wa.audit_id = 1 AND wa.roomid IS NULL AND we.corp_id = ' . $corpId . ' AND wa.from_type != 3 AND (( CASE WHEN wa.from_type = 1 THEN wa.tolist ELSE wa.from END ) LIKE (\'wm_%\') OR ( CASE WHEN wa.from_type = 1 THEN wa.tolist ELSE wa.from END ) LIKE (\'wo_%\')) GROUP BY we.id';
				\Yii::error($sql, 'chat_sql');
				$auditInfo = \Yii::$app->getDb()->createCommand($sql)->queryAll();
				if (!empty($auditInfo)) {
					foreach ($auditInfo as $info) {
						if ($info['msgtime'] >= $chartStartTime && $info['msgtime'] <= $chartEndTime) {
							if (!empty($info['id'])) {
								array_push($contactId, $info['id']);
							}
						}
					}
				}
				$workExternalUserDataNew = $workExternalUserDataNew->andWhere(['we.id' => $contactId]);
			}

			if (!empty($sign_id)) {
				$contactId = [];
				$member    = WorkExternalContactMember::find()->where(['sign_id' => $sign_id, 'is_bind' => 1])->select('external_userid')->groupBy('external_userid');
				$member    = $member->asArray()->all();
				if (!empty($member)) {
					foreach ($member as $mem) {
						array_push($contactId, $mem['external_userid']);
					}
				}
				$workExternalUserDataNew = $workExternalUserDataNew->andWhere(['we.id' => $contactId]);
			}

			if (!empty($user_ids)) {
				$workExternalUserDataNew = $workExternalUserDataNew->andWhere(['in', 'wf.user_id', $user_ids]);
			}

			if (!empty($type) && (!empty($start_time) || !empty($end_time))) {
				if (!empty($start_time) && !empty($end_time)) {
					$workExternalUserDataNew = $workExternalUserDataNew->andFilterWhere(['between', 'wf.createtime', strtotime($start_time), strtotime($end_time . ':59')]);
				} elseif (!empty($start_time)) {
					$workExternalUserDataNew = $workExternalUserDataNew->andWhere(['>', 'wf.createtime', strtotime($start_time)]);
				} else {
					$workExternalUserDataNew = $workExternalUserDataNew->andWhere(['<', 'wf.createtime', strtotime($end_time)]);
				}
			}

			if ($follow_id != '-1') {
				$workExternalUserDataNew = $workExternalUserDataNew->andWhere(['wf.follow_id' => $follow_id]);
			}

			//来源搜索
			if ($add_way != '-1') {
				if ($add_way === 'way') {
					$workExternalUserDataNew = $workExternalUserDataNew->andWhere(['>', 'wf.way_id', 0]);
				} elseif ($add_way === 'chatWay') {
					$workExternalUserDataNew = $workExternalUserDataNew->andWhere(['>', 'wf.chat_way_id', 0]);
				} elseif ($add_way === 'fission') {
					$workExternalUserDataNew = $workExternalUserDataNew->andWhere(['>', 'wf.fission_id', 0]);
				} elseif ($add_way === 'award') {
					$workExternalUserDataNew = $workExternalUserDataNew->andWhere(['>', 'wf.award_id', 0]);
				} elseif ($add_way === 'redPack') {
					$workExternalUserDataNew = $workExternalUserDataNew->andWhere(['>', 'wf.red_pack_id', 0]);
				} else {
					$workExternalUserDataNew = $workExternalUserDataNew->andWhere(['wf.add_way' => $add_way]);
				}
			}

			//活码搜索
			if (!empty($way_id)) {
				$wayArr = explode('_', $way_id);
				if ($wayArr[0] == 'way') {
					$workExternalUserDataNew = $workExternalUserDataNew->andWhere(['wf.way_id' => $wayArr[1]]);
				} elseif ($wayArr[0] == 'chatWay') {
					$workExternalUserDataNew = $workExternalUserDataNew->andWhere(['wf.chat_way_id' => $wayArr[1]]);
				} elseif ($wayArr[0] == 'fission') {
					$workExternalUserDataNew = $workExternalUserDataNew->andWhere(['wf.fission_id' => $wayArr[1]]);
				} elseif ($wayArr[0] == 'award') {
					$workExternalUserDataNew = $workExternalUserDataNew->andWhere(['wf.award_id' => $wayArr[1]]);
				} elseif ($wayArr[0] == 'redPack') {
					$workExternalUserDataNew = $workExternalUserDataNew->andWhere(['wf.red_pack_id' => $wayArr[1]]);
				}
			}

			//客户群搜索
			if (!empty($chat_id)) {
				$chatInfo = WorkChatInfo::find()->where(['chat_id' => $chat_id, 'type' => 2, 'status' => 1])->select('id,external_id')->all();
				if (!empty($chatInfo)) {
					$tempId                  = array_column($chatInfo, 'external_id');
					$workExternalUserDataNew = $workExternalUserDataNew->andWhere(['in', 'we.id', $tempId]);
				} else {
					$workExternalUserDataNew = $workExternalUserDataNew->andWhere(['we.id' => 0]);
				}
			}

			//群搜索
			if (!empty($chat_type)) {
				$tempContact = WorkExternalContact::find()->alias('wec');
				$tempContact = $tempContact->leftJoin('{{%work_chat_info}} wci', '`wci`.`external_id` = `wec`.`id` and `wci`.`status`=1 and `wci`.`type`=2');
				$tempContact = $tempContact->where(['wec.corp_id' => $corpId]);
				$tempContact = $tempContact->select('wec.id,count(wci.id) count');
				$tempContact = $tempContact->groupBy('wec.id');
				if ($chat_type == 1) {
					$tempContact = $tempContact->having('count=0');
				} elseif ($chat_type == 2) {
					$tempContact = $tempContact->having('count=1');
				} elseif ($chat_type == 3) {
					$tempContact = $tempContact->having('count > 1');
				}
				$tempContact = $tempContact->all();
				if (!empty($tempContact)) {
					$tempId                  = array_column($tempContact, 'id');
					$workExternalUserDataNew = $workExternalUserDataNew->andWhere(['in', 'we.id', $tempId]);
				} else {
					$workExternalUserDataNew = $workExternalUserDataNew->andWhere(['we.id' => 0]);
				}
			}
            //标签搜索
            $tagIds = $tag_ids ? (is_array($tag_ids) ? $tag_ids : explode(',', $tag_ids)) : [];
            if (!empty($tagIds) && in_array($tag_type, [1, 2, 3])) {
                $userTag = WorkTagFollowUser::find()
                    ->alias('wtf')
                    ->innerJoin('{{%work_tag}} wtg', '`wtf`.`tag_id` = `wtg`.`id` AND wtg.`is_del` = 0')
                    ->where(['wtf.corp_id' => $corpId,'wtg.corp_id' => $corpId,'wtf.status' => 1])
                    ->groupBy('wtf.follow_user_id')
                    ->select('wtf.follow_user_id,GROUP_CONCAT(wtg.id) tag_ids');

                $workExternalUserDataNew = $workExternalUserDataNew->leftJoin(['wt' => $userTag], '`wt`.`follow_user_id` = `wf`.`id`');
                $tagsFilter = [];
                if ($tag_type == 1) {//标签或
                    $tagsFilter[] = 'OR';
                    array_walk($tagIds, function($value) use (&$tagsFilter){
                        $tagsFilter[] = ($value == -1) ? ['wt.tag_ids' => NULL] : (new Expression("FIND_IN_SET($value,wt.tag_ids)"));
                    });
                }elseif ($tag_type == 2) {//标签且
                    $tagsFilter[] = 'AND';
                    array_walk($tagIds, function($value) use (&$tagsFilter){
                        $tagsFilter[] = ($value == -1) ? ['wt.tag_ids' => NULL] : (new Expression("FIND_IN_SET($value,wt.tag_ids)"));
                    });
                }elseif ($tag_type == 3) {//标签不包含
                    $tagsFilter[] = 'AND';
                    array_walk($tagIds, function($value) use (&$tagsFilter){
                        $tagsFilter[] = ($value == -1) ? ['is not','wt.tag_ids',NULL] : (new Expression("NOT FIND_IN_SET($value,IFNULL(wt.tag_ids,''))"));
                    });
                }
                $workExternalUserDataNew->andWhere($tagsFilter);
            }
			//其他搜索
			if ($name || $phone !== '' || $work || $province || $sex != '-1' || !empty($fieldData)) {
				if (!empty($phone)) {
					$workExternalUserDataNew = $workExternalUserDataNew->andWhere(' wf.remark_mobiles like  \'%' . $phone . '%\' ');
				}

				$fieldSubUser = [];
				if ($workCorp->unshare_field == 1 && !empty($user_ids) && is_array($user_ids)) {
					$fieldSubUser            = array_merge($user_ids, [0]);
					$workExternalUserDataNew = $workExternalUserDataNew->leftJoin('{{%custom_field_value}} cf', '`cf`.`cid` = `we`.`id` AND `cf`.`type`=1 AND `cf`.`user_id` in (' . implode(',', $fieldSubUser) . ')');
				} else {
					$workExternalUserDataNew = $workExternalUserDataNew->leftJoin('{{%custom_field_value}} cf', '`cf`.`cid` = `we`.`id` AND `cf`.`type`=1');
				}
				if (!empty($name)) {
					$workExternalUserDataNew = $workExternalUserDataNew->andWhere(' we.name_convert like \'%' . $name . '%\' or wf.remark_corp_name like \'%' . $name . '%\'  or wf.remark like \'%' . $name . '%\' or wf.nickname like \'%' . $name . '%\' or (cf.fieldid in (' . $fieldD['name'] . ') and cf.value like \'%' . $name . '%\')');
				}

				if ($work || $province || $sex != '-1' || !empty($fieldData)) {
					$fieldUserArr = [];
					$havaField    = 1;//有符合条件的客户

					if ($sex != '-1') {
						if ($sex == 1) {
							$sex = '男';
						} elseif ($sex == 2) {
							$sex = '女';
						} else {
							$sex = '未知';
						}
						$fieldUserData = CustomFieldValue::find()->where(['uid' => $uid, 'type' => 1, 'fieldid' => $fieldD['sex'], 'value' => $sex]);
						if ($workCorp->unshare_field == 1 && !empty($fieldSubUser)) {
							$fieldUserData = $fieldUserData->andWhere(['user_id' => $fieldSubUser]);
						}
						$fieldUserData           = $fieldUserData->select('`cid`')->asArray()->all();
						$contactId               = array_column($fieldUserData, 'cid');
						$fieldUserArr            = !empty($fieldUserArr) ? array_intersect($fieldUserArr, $contactId) : $contactId;
						$havaField               = empty($fieldUserArr) ? 0 : $havaField;
						$workExternalUserDataNew = $workExternalUserDataNew->andWhere(['cf.fieldid' => $fieldD['sex']]);
					}
//							if ($phone !== '' && $havaField) {
//								$fieldUserData = CustomFieldValue::find()->where(['uid' => $uid, 'type' => 1])->andWhere(['and', ['in', 'fieldid', [$fieldD['phone'], $fieldD['qq']]], ['like', 'value', $phone]])->select('`cid`')->asArray()->all();
//								$contactId     = array_column($fieldUserData, 'cid');
//								$fieldUserArr  = !empty($fieldUserArr) ? array_intersect($fieldUserArr, $contactId) : $contactId;
//								$havaField     = empty($fieldUserArr) ? 0 : $havaField;
//							}
					if (!empty($work) && $havaField) {
						$fieldUserData = CustomFieldValue::find()->where(['uid' => $uid, 'type' => 1])->andWhere(['fieldid' => $fieldD['work'], 'value' => $work]);
						if ($workCorp->unshare_field == 1 && !empty($fieldSubUser)) {
							$fieldUserData = $fieldUserData->andWhere(['user_id' => $fieldSubUser]);
						}
						$fieldUserData = $fieldUserData->select('`cid`')->asArray()->all();
						$contactId     = array_column($fieldUserData, 'cid');
						$fieldUserArr  = !empty($fieldUserArr) ? array_intersect($fieldUserArr, $contactId) : $contactId;
						$havaField     = empty($fieldUserArr) ? 0 : $havaField;
					}
					if (!empty($province) && $havaField) {
						if (!empty($city)) {
							$fieldUserData = CustomFieldValue::find()->where(['uid' => $uid, 'type' => 1])->andWhere(['fieldid' => $fieldD['area'], 'value' => $province . '-' . $city]);
						} else {
							$fieldUserData = CustomFieldValue::find()->where(['uid' => $uid, 'type' => 1])->andWhere(['and', ['fieldid' => $fieldD['area']], ['like', 'value', $province . '-']]);
						}
						if ($workCorp->unshare_field == 1 && !empty($fieldSubUser)) {
							$fieldUserData = $fieldUserData->andWhere(['user_id' => $fieldSubUser]);
						}
						$fieldUserData = $fieldUserData->select('`cid`')->asArray()->all();
						$contactId     = array_column($fieldUserData, 'cid');
						$fieldUserArr  = !empty($fieldUserArr) ? array_intersect($fieldUserArr, $contactId) : $contactId;
						$havaField     = empty($fieldUserArr) ? 0 : $havaField;
					}

					if (!empty($fieldData) && $havaField) {
						foreach ($fieldData as $val) {
							if ($havaField) {
								if ($val['type'] == 3) {
									//多选属性需模糊匹配
									$fieldUserData = CustomFieldValue::find()->where(['uid' => $uid, 'type' => 1])->andWhere(['and', ['fieldid' => $val['field']], ['like', 'value', $val['match']]]);
								} else {
									$fieldUserData = CustomFieldValue::find()->where(['uid' => $uid, 'type' => 1])->andWhere(['fieldid' => $val['field'], 'value' => $val['match']]);
								}
								if ($workCorp->unshare_field == 1 && !empty($fieldSubUser)) {
									$fieldUserData = $fieldUserData->andWhere(['user_id' => $fieldSubUser]);
								}
								$fieldUserData = $fieldUserData->select('`cid`')->asArray()->all();
								$contactId     = array_column($fieldUserData, 'cid');
								$fieldUserArr  = !empty($fieldUserArr) ? array_intersect($fieldUserArr, $contactId) : $contactId;
								$havaField     = empty($fieldUserArr) ? 0 : $havaField;
							}
						}
					}

					if (!empty($fieldUserArr)) {
						$workExternalUserDataNew = $workExternalUserDataNew->andWhere(['in', 'we.id', $fieldUserArr]);
					} else {
						$workExternalUserDataNew = $workExternalUserDataNew->andWhere(['we.id' => 0]);
					}


				}

			}

			return $workExternalUserDataNew;
		}
	}
