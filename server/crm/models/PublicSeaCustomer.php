<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\queue\WaitUserTaskJob;
	use app\util\SUtils;
	use app\util\WebsocketUtil;
	use app\util\WorkUtils;
	use dovechen\yii2\weWork\src\dataStructure\ExternalContact;
	use dovechen\yii2\weWork\src\dataStructure\ExternalContactRemark;
	use dovechen\yii2\weWork\src\dataStructure\Message;
	use dovechen\yii2\weWork\src\dataStructure\TextMesssageContent;
	use Yii;

	/**
	 * This is the model class for table "{{%public_sea_customer}}".
	 *
	 * @property int      $id
	 * @property int      $uid                   账户ID
	 * @property int      $sub_id                子账户ID
	 * @property int      $corp_id               授权的企业ID
	 * @property int      $type                  0非企微客户1企微客户
	 * @property string   $name                  姓名
	 * @property string   $wx_num                微信号
	 * @property string   $phone                 手机号
	 * @property string   $qq                    QQ
	 * @property string   $remark                备注
	 * @property int      $field_option_id       来源高级属性选项id
	 * @property int      $external_userid       绑定的外部联系人id
	 * @property int      $bind_time             绑定时间
	 * @property int      $user_id               上次认领成员
	 * @property int      $follow_user_id        外部联系人添加信息表id
	 * @property int      $is_claim              是否已认领0否1是
	 * @property string   $update_time           更新时间
	 * @property string   $add_time              创建时间
	 * @property string   $reclaim_time          回收时间
	 * @property string   $reclaim_rule          回收条件
	 * @property string   $is_del                是否已删除
	 * @property int      $ignore_add_wechat_tip 是否忽略打完电话添加客户微信好友的弹窗提示0：不忽略；1：忽略
	 *
	 * @property WorkCorp $corp
	 */
	class PublicSeaCustomer extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%public_sea_customer}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_id', 'type', 'field_option_id', 'follow_user_id', 'is_claim', 'ignore_add_wechat_tip'], 'integer'],
				[['phone'], 'string'],
				[['update_time', 'add_time', 'reclaim_time'], 'safe'],
				[['wx_num'], 'string', 'max' => 64],
				[['uid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['uid' => 'uid']],
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
				'uid'                   => Yii::t('app', '账户ID'),
				'sub_id'                => Yii::t('app', '子账户ID'),
				'corp_id'               => Yii::t('app', '授权的企业ID'),
				'type'                  => Yii::t('app', '0非企微客户1企微客户'),
				'name'                  => Yii::t('app', '姓名'),
				'wx_num'                => Yii::t('app', '微信号'),
				'phone'                 => Yii::t('app', '手机号'),
				'qq'                    => Yii::t('app', 'QQ'),
				'remark'                => Yii::t('app', '备注'),
				'field_option_id'       => Yii::t('app', '来源高级属性选项id'),
				'external_userid'       => Yii::t('app', '外部联系人id'),
				'bind_time'             => Yii::t('app', '绑定时间'),
				'user_id'               => Yii::t('app', '上次认领成员'),
				'follow_user_id'        => Yii::t('app', '外部联系人添加信息表id'),
				'is_claim'              => Yii::t('app', '是否已认领0否1是'),
				'update_time'           => Yii::t('app', '更新时间'),
				'add_time'              => Yii::t('app', '创建时间'),
				'reclaim_time'          => Yii::t('app', '回收时间'),
				'reclaim_rule'          => Yii::t('app', '回收条件'),
				'is_del'                => Yii::t('app', '是否已删除'),
				'ignore_add_wechat_tip' => Yii::t('app', '是否忽略打完电话添加客户微信好友的弹窗提示0：不忽略；1：忽略'),
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
		public function getU ()
		{
			return $this->hasOne(User::className(), ['uid' => 'uid']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getCorp ()
		{
			return $this->hasOne(WorkCorp::className(), ['id' => 'corp_id']);
		}

		/*
		 * $type 0非企微客户1企微客户
		 */
		public function dumpData ($type = 0, $otherData = [])
		{
			$result = [
				'key'               => $this->id,
				'id'                => $this->id,
				'uid'               => $this->uid,
				'sub_id'            => $this->sub_id,
				'corp_id'           => $this->corp_id,
				'type'              => $this->type,
				'name'              => $this->name,
				'wx_num'            => $this->wx_num,
				'phone'             => $this->phone,
				'qq'                => $this->qq,
				'remark'            => $this->remark,
				'field_option_id'   => $this->field_option_id,
				'field_option_name' => '',
				'user_id'           => $this->user_id,
				'follow_user_id'    => $this->follow_user_id,
				'is_claim'          => $this->is_claim,
				'add_time'          => !empty($this->add_time) ? date('Y-m-d H:i', $this->add_time) : '--',
				'reclaim_time'      => !empty($this->reclaim_time) ? date('Y-m-d H:i', $this->reclaim_time) : '--',
				'reclaim_rule'      => $this->reclaim_rule,
				'customerInfo'      => [],
			];
			//重新认领提示
			$claimTip   = '';
			$againClaim = 0;
			$time       = time();

			$fieldInfo = CustomField::findOne(['uid' => 0, 'key' => 'sex']);

			if ($type == 0) {
				$fieldValue       = CustomFieldValue::findOne(['type' => 4, 'cid' => $this->id, 'fieldid' => $fieldInfo->id]);
				$result['gender'] = !empty($fieldValue) ? $fieldValue->value : '未知';
				if (!empty($this->field_option_id)) {
					$optionInfo = CustomFieldOption::findOne($this->field_option_id);
					if (!empty($optionInfo)) {
						$result['field_option_name'] = $optionInfo->match;
					}
				}

				if (!empty($otherData['user_id'])) {
					$claimInfo = PublicSeaClaim::find()->where(['sea_id' => $this->id, 'user_id' => $otherData['user_id']])->orderBy(['id' => SORT_DESC])->one();
					if (!empty($claimInfo) && !empty($claimInfo->reclaim_time)) {
						$reclaimTime = $claimInfo->reclaim_time;
						$reclaimDay  = $otherData['reclaim_day'];
						$claimTime   = $reclaimTime + $reclaimDay * 86400;
						$claimDate   = date('Y-m-d', $claimTime);
						$nowDate     = date('Y-m-d');
						if ($nowDate >= $claimDate) {
							$againClaim = 1;
						} else {
							$claimTip = '你可将于' . $claimDate . '重新认领';
						}
					}
				}
				//获取标签
				$tagData = [];
				if (!empty($otherData['corp_id'])) {
					$tagData = PublicSeaPrivateTag::getTagBySeaId($otherData['corp_id'], $this->id);
				}
				$result['tagData'] = $tagData;
			} else {
				if (!empty($this->external_userid)) {
					$contactInfo = WorkExternalContact::findOne($this->external_userid);
					if (!empty($contactInfo)) {
						$customerInfo              = [];
						$customerInfo['name']      = !empty($contactInfo->name) ? $contactInfo->name : '';
						$customerInfo['avatar']    = $contactInfo->avatar;
						$customerInfo['corp_name'] = $contactInfo->corp_name;
						$fieldValue                = CustomFieldValue::findOne(['type' => 1, 'cid' => $this->external_userid, 'fieldid' => $fieldInfo->id]);
						$customerInfo['gender']    = !empty($fieldValue) ? $fieldValue->value : '';

						//来源
						$member = '';
						if (!empty($this->follow_user_id)) {
							$followUser = WorkExternalContactFollowUser::findOne($this->follow_user_id);
							if (!empty($followUser)) {
								$addWay   = static::getAddWay($followUser);
								$workUser = WorkUser::findOne($followUser->user_id);
								if ($followUser->del_type == 1) {
									$claimTip = '客户已被员工删除，无法认领';
								} elseif ($followUser->del_type == 2) {
									$claimTip = '员工已被客户删除/拉黑，无法认领';
								} elseif ($workUser->is_del == 1) {
									$claimTip = '原归属员工已离职，但尚无新的接替成员时，无法认领';
								}
								$departName = WorkDepartment::getDepartNameByUserId($workUser->department, $workUser->corp_id);
								$member     = $workUser->name . '--' . $departName;
							}
						}
						$result['member']         = $member;
						$result['add_other_info'] = !empty($addWay) ? $addWay['add_other_info'] : '';
						$result['add_way_info']   = !empty($addWay) ? $addWay['add_way_info'] : '';
						$result['add_way_title']  = !empty($addWay) ? $addWay['add_way_title'] : '';

						$result['customerInfo'] = $customerInfo;
					}
					if (!empty($otherData['user_id']) && empty($claimTip)) {
						$followUser = WorkExternalContactFollowUser::findOne(['external_userid' => $this->external_userid, 'user_id' => $otherData['user_id']]);
						if (!empty($followUser) && $followUser->del_type == 0) {
							$claimTip = '已加过此客户，无需再认领！';
						}
					}
				}
			}
			$result['claimTip']    = $claimTip;
			$result['again_claim'] = $againClaim;//0我要认领、1重新认领

			//认领数据
			$claimData             = PublicSeaClaim::getClaimData($this->id, $otherData['corp_id']);
			$result['claimData']   = $claimData;
			$result['claimCount']  = count($claimData);
			$result['claimName']   = !empty($claimData) ? $claimData[0]['name'] : '';
			$result['is_del_show'] = !empty($claimData) ? 0 : 1;

			return $result;
		}

		//获取来源
		public static function getAddWay ($followUser)
		{
			$result = [];
			/**@var WorkExternalContactFollowUser $followUser * */
			$addWayInfo = WorkExternalContactFollowUser::getAddWay($followUser->add_way);
			$title      = '';
			$wayInfo    = '';
			if ($followUser->way_id > 0) {
				$wayInfo    = '渠道活码';
				$contactWay = WorkContactWay::findOne($followUser->way_id);
				if (!empty($contactWay)) {
					$title = $contactWay->title;
				}
			} elseif ($followUser->baidu_way_id > 0) {
				$wayInfo = '百度统计';
				$way     = WorkContactWayBaidu::findOne($followUser->baidu_way_id);
				if (!empty($way)) {
					$title = $way->title;
				}
			} elseif ($followUser->chat_way_id > 0) {
				$wayInfo = '自动拉群';
				$way     = WorkChatContactWay::findOne($followUser->chat_way_id);
				if (!empty($way)) {
					$title = $way->title;
				}
			} elseif ($followUser->fission_id > 0) {
				$wayInfo = '裂变引流';
				$fission = Fission::findOne($followUser->fission_id);
				if (!empty($fission)) {
					$title = $fission->title;
				}
			} elseif ($followUser->award_id > 0) {
				$wayInfo = '抽奖引流';
				$award   = AwardsActivity::findOne($followUser->award_id);
				if (!empty($award)) {
					$title = $award->title;
				}
			} elseif ($followUser->red_pack_id > 0) {
				$wayInfo = '红包裂变';
				$red     = RedPack::findOne($followUser->red_pack_id);
				if (!empty($red)) {
					$title = $red->title;
				}
			}
			$result['add_other_info'] = $addWayInfo;
			$result['add_way_info']   = $wayInfo;
			$result['add_way_title']  = $title;

			return $result;
		}

		//根据来源id获取数据
		public static function getAddWayById ($otherData)
		{
			$result     = [];
			$addWay     = !empty($otherData['add_way']) ? $otherData['add_way'] : 0;
			$wayId      = !empty($otherData['way_id']) ? $otherData['way_id'] : 0;
			$baiDuWayId = !empty($otherData['baidu_way_id']) ? $otherData['baidu_way_id'] : 0;
			$chatWayId  = !empty($otherData['chat_way_id']) ? $otherData['chat_way_id'] : 0;
			$fissionId  = !empty($otherData['fission_id']) ? $otherData['fission_id'] : 0;
			$awardId    = !empty($otherData['award_id']) ? $otherData['award_id'] : 0;
			$redPackId  = !empty($otherData['red_pack_id']) ? $otherData['red_pack_id'] : 0;
			$wayRedpackId = !empty($otherData['way_redpack_id']) ? $otherData['way_redpack_id'] : 0;
			/**@var WorkExternalContactFollowUser $followUser * */
			$addWayInfo = WorkExternalContactFollowUser::getAddWay($addWay);
			$title      = '';
			$wayInfo    = '';

			if ($wayId > 0) {
				$wayInfo    = '渠道活码';
				$contactWay = WorkContactWay::findOne($wayId);
				if (!empty($contactWay)) {
					$title = $contactWay->title;
				}
			} elseif ($baiDuWayId > 0) {
				$wayInfo = '百度统计';
				$way     = WorkContactWayBaidu::findOne($baiDuWayId);
				if (!empty($way)) {
					$title = $way->title;
				}
			} elseif ($chatWayId > 0) {
				$wayInfo = '自动拉群';
				$way     = WorkChatContactWay::findOne($chatWayId);
				if (!empty($way)) {
					$title = $way->title;
				}
			} elseif ($fissionId > 0) {
				$wayInfo = '裂变引流';
				$fission = Fission::findOne($fissionId);
				if (!empty($fission)) {
					$title = $fission->title;
				}
			} elseif ($awardId > 0) {
				$wayInfo = '抽奖引流';
				$award   = AwardsActivity::findOne($awardId);
				if (!empty($award)) {
					$title = $award->title;
				}
			} elseif ($redPackId > 0) {
				$wayInfo = '红包裂变';
				$red     = RedPack::findOne($redPackId);
				if (!empty($red)) {
					$title = $red->title;
				}
			} elseif ($wayRedpackId > 0) {
				$wayInfo = '红包拉新';
				$redWay  = WorkContactWayRedpacket::findOne($wayRedpackId);
				if (!empty($redWay)) {
					$title = $redWay->name;
				}
			}
			$result['add_other_info'] = $addWayInfo;
			$result['add_way_info']   = $wayInfo;
			$result['add_way_title']  = $title;

			return $result;
		}

		//非企微客户录入
		public static function setData ($data, $from = 0)
		{
			$time          = time();
			$id            = !empty($data['id']) ? $data['id'] : 0;
			$uid           = !empty($data['uid']) ? $data['uid'] : 0;
			$subId         = !empty($data['sub_id']) ? $data['sub_id'] : 0;
			$name          = !empty($data['name']) ? trim($data['name']) : '';
			$wxNum         = !empty($data['wx_num']) ? trim($data['wx_num']) : '';
			$phone         = !empty($data['phone']) ? trim($data['phone']) : '';
			$qq            = !empty($data['qq']) ? trim($data['qq']) : '';
			$email         = !empty($data['email']) ? trim($data['email']) : '';
			$sex           = !empty($data['sex']) ? trim($data['sex']) : '未知';
			$area          = !empty($data['area']) ? trim($data['area']) : '';
			$remark        = !empty($data['remark']) ? trim($data['remark']) : '';
			$company       = !empty($data['company']) ? trim($data['company']) : '';
			$fieldOptionId = !empty($data['field_option_id']) ? $data['field_option_id'] : 0;
			$isClaim       = !empty($data['is_claim']) ? $data['is_claim'] : 0;
			$corpId        = !empty($data['corp_id']) ? $data['corp_id'] : 0;
			$tagIds        = !empty($data['tag_ids']) ? $data['tag_ids'] : [];
			$userId        = !empty($data['user_id']) ? $data['user_id'] : 0;//操作成员
			$isFrom        = !empty($data['is_from']) ? $data['is_from'] : 0;//0:公海池录入、1:从非企微客户列表录入
			if (!empty($tagIds) && !is_array($tagIds)) {
				$tagIds = explode(',', $tagIds);
			}
			if (empty($uid)) {
				if (!empty($from)) {
					return 'skipPhone';
				}
				throw new InvalidDataException('参数不正确');
			}
			if (empty($name)) {
				if (!empty($from)) {
					return 'skipPhone';
				}
				throw new InvalidDataException('请填写姓名');
			} elseif (mb_strlen($name, 'utf-8') > 64) {
				$name = mb_substr($name, 0, 64, 'utf-8');
			}
			if (empty($wxNum) && empty($phone) && empty($qq)) {
				if (!empty($from)) {
					return 'skipPhone';
				}
				throw new InvalidDataException('微信号/手机号至少要填一个');
			}

			if (!empty($phone)) {
				if (!preg_match("/^((13[0-9])|(14[0-9])|(15([0-9]))|(16([0-9]))|(17([0-9]))|(18[0-9])|(19[0-9]))\d{8}$/", $phone)) {
					if (!empty($from)) {
						return 'skipPhone';
					}
					throw new InvalidDataException('请输入正确的手机号');
				}
			}
			if (!empty($wxNum)) {
				$wxReg = '/^[a-zA-Z][a-zA-Z0-9_-]{5,19}$/u';
				if (!preg_match($wxReg, $wxNum)) {
					if (!empty($from)) {
						return 'skipPhone';
					}
					throw new InvalidDataException('请输入正确的微信号');
				}
			}

			if (!empty($qq)) {
				$qqReg = "/^[1-9][0-9]{4,9}$/";
				if (!preg_match($qqReg, $qq)) {
					if (!empty($from)) {
						return 'skipPhone';
					}
					throw new InvalidDataException('请输入正确的QQ号');
				}
			}
			if (!empty($wxNum) && (mb_strlen($wxNum, 'utf-8') > 64)) {
				$wxNum = mb_substr($wxNum, 0, 64, 'utf-8');
			}
			if (!empty($qq) && (mb_strlen($qq, 'utf-8') > 64)) {
				$qq = mb_substr($qq, 0, 64, 'utf-8');
			}
			if (!empty($remark) && mb_strlen($remark, 'utf-8') > 10) {
				$remark = mb_substr($remark, 0, 10, 'utf-8');
			}
			if (!empty($company) && mb_strlen($company, 'utf-8') > 30) {
				$company = mb_substr($company, 0, 30, 'utf-8');
			}
			if (!in_array($sex, ['男', '女', '未知'])) {
				$sex = '未知';
			}
			if (!empty($email)) {
				if (!preg_match("/^\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}$/", $email)) {
					if (!empty($from)) {
						return 'skipPhone';
					}
					throw new InvalidDataException('邮箱格式不正确！');
				}
			}

			//判断是否已存在
			$tempWhere = ['or'];
			if (!empty($wxNum)) {
				array_push($tempWhere, ['wx_num' => $wxNum]);
			}
			if (!empty($phone)) {
				array_push($tempWhere, ['phone' => $phone]);
			}
			if (!empty($qq)) {
				array_push($tempWhere, ['qq' => $qq]);
			}
			$tempInfo = PublicSeaCustomer::find()->where(['uid' => $uid, 'corp_id' => $corpId, 'is_del' => 0])->andWhere($tempWhere);

			if (!empty($id)) {
				$customer              = PublicSeaCustomer::findOne($id);
				$tempInfo              = $tempInfo->andWhere(['!=', 'id', $id]);
				$customer->update_time = $time;
			} else {
				$customer           = new PublicSeaCustomer();
				$customer->uid      = $uid;
				$customer->sub_id   = $subId;
				$customer->corp_id  = $corpId;
				$customer->type     = 0;
				$customer->add_time = $time;
				$customer->is_claim = $isClaim;
			}

			$tempInfo = $tempInfo->one();
			if (!empty($tempInfo)) {
				if (!empty($from)) {
					return 'skip';
				}
				throw new InvalidDataException('微信号或手机号或QQ已存在');
			}

			$customer->name            = $name;
			$customer->wx_num          = $wxNum;
			$customer->phone           = $phone;
			$customer->qq              = $qq;
			$customer->remark          = $remark;
			$customer->field_option_id = $fieldOptionId;
			Yii::error($customer->phone, 'sea_order');
			if (!$customer->validate() || !$customer->save()) {
				if (!empty($from)) {
					return 'skip';
				}
				throw new InvalidDataException(SUtils::modelError($customer));
			}
			Yii::error($customer->id, 'sea_order');
			//电商系统-新增非企业用户时添加顾客  不能放入队列执行
			ShopCustomer::clearSeaUser(0, $customer->id);

			$fieldList = CustomField::find()->where(['uid' => 0, 'key' => ['sex', 'wx_num', 'name', 'phone', 'area', 'email', 'qq', 'company']])->all();
			/**@var CustomField $field * */
			foreach ($fieldList as $field) {
				if ($field->key == 'sex') {
					$value = $sex;
				} elseif ($field->key == 'wx_num') {
					$value = $wxNum;
				} elseif ($field->key == 'name') {
					$value = $name;
				} elseif ($field->key == 'phone') {
					$value = $phone;
				} elseif ($field->key == 'area') {
					$value = $area;
				} elseif ($field->key == 'email') {
					$value = $email;
				} elseif ($field->key == 'qq') {
					$value = $qq;
				} elseif ($field->key == 'company') {
					$value = $company;
				} else {
					continue;
				}
				$fieldValue = CustomFieldValue::findOne(['cid' => $customer->id, 'type' => 4, 'fieldid' => $field->id]);
				if (empty($fieldValue)) {
					if (empty($value)) {
						continue;
					}
					$fieldValue          = new CustomFieldValue();
					$fieldValue->uid     = $uid;
					$fieldValue->type    = 4;
					$fieldValue->cid     = $customer->id;
					$fieldValue->fieldid = $field->id;
				} else {
					if ($value == $fieldValue->value) {
						continue;
					}
				}
				$fieldValue->value = $value;
				$fieldValue->time  = $time;
				$fieldValue->save();
			}

			//标签,新增时才添加
			if (empty($id) && !empty($tagIds) && !empty($corpId)) {
				$setData = ['uid' => $uid, 'sub_id' => $subId, 'sea_id' => $customer->id, 'corp_id' => $corpId];
				PublicSeaPrivateTag::setData($tagIds, $setData);
			}
			//轨迹
			if (empty($id)) {
				$fromStr = !empty($from) ? '导入' : '录入';
				$nameArr = array_filter([$phone, $wxNum, $qq]);
				$nameStr = implode('/', $nameArr);
				if (!empty($isFrom)) {
					$workUser  = WorkUser::findOne($userId);
					$remarkStr = '【' . $workUser->name . '】' . $fromStr . '非企微客户【' . $name . '】到非企微客户管理';
				} else {
					$remarkStr = '【' . $nameStr . '】的【' . $name . '】' . $fromStr . '至公海池';
				}
				PublicSeaTimeLine::addExternalTimeLine(['uid' => $uid, 'sub_id' => $subId, 'sea_id' => $customer->id, 'user_id' => $userId, 'event' => 'add_custom', 'remark' => $remarkStr]);
			}

			if (!empty($from)) {
				if ($from == 2) {
					return $customer->id;
				}

				return 'insert';
			}

			return $customer->id;
		}

		//非企微客户导入
		public static function create ($data)
		{
			$uid        = $data['uid'];
			$subId      = $data['sub_id'];
			$corpId     = $data['corp_id'];
			$tagIds     = $data['tag_ids'];
			$userId     = $data['user_id'];
			$insertNum  = $skipNum = $skipPhoneNum = 0;
			$importData = $data['importData'];
			$snum       = count($importData) - 1;
			static::importNumWebsocket($uid, $corpId, $snum, 0, 0, 0, 'import_nocustomer', $subId);
			if ($snum > 1000) {
				$perKey = ceil($snum / 50);
			} else {
				$perKey = 10;
			}

			//获取来源选项
			$customField   = CustomField::findOne(['uid' => 0, 'type' => 2, 'key' => 'offline_source']);
			$optionList    = CustomFieldOption::find()->where(['uid' => 0, 'fieldid' => $customField->id])->select('id,match')->all();
			$optionData    = array_column($optionList, 'match', 'id');
			$otherOptionId = array_search('其他', $optionData);

			foreach ($importData as $k => $v) {
				if ($k == 1) {
					continue;
				}
				$source = trim($v['I']);
				if (!empty($source)) {
					$optionId = array_search($source, $optionData);
					if (empty($optionId)) {
						$optionId = $otherOptionId;
					}
				} else {
					$optionId = $otherOptionId;
				}

				$data = [
					'uid'             => $uid,
					'sub_id'          => $subId,
					'corp_id'         => $corpId,
					'tag_ids'         => $tagIds,
					'user_id'         => $userId,
					'name'            => $v['A'],
					'wx_num'          => $v['B'],
					'phone'           => $v['C'],
					'qq'              => $v['D'],
					'sex'             => $v['E'],
					'area'            => $v['F'],
					'email'           => $v['G'],
					'remark'          => $v['H'],
					'field_option_id' => $optionId,
					'company'         => $v['J'],
				];

				$type = PublicSeaCustomer::setData($data, 1);
				switch ($type) {
					case 'insert':
						$insertNum++;
						break;
					case 'skip':
						$skipNum++;
						break;
					case 'skipPhone':
						$skipPhoneNum++;
						break;
				}
				if (($k - 1) % $perKey == 0) {
					static::importNumWebsocket($uid, $corpId, $snum, $k - 1, $insertNum, $skipNum + $skipPhoneNum, 'import_nocustomer', $subId);
				}
			}

			$textHtml = '本次导入成功' . $insertNum . '条，';
			if (!empty($skipNum)) {
				$textHtml .= '忽略' . $skipNum . '条（已有的），';
			}
			if (!empty($skipPhoneNum)) {
				$textHtml .= $skipPhoneNum . '条格式不正确，';
			}
			$textHtml = trim($textHtml, '，');

			static::importNumWebsocket($uid, $corpId, $snum, $snum, $insertNum, $skipNum + $skipPhoneNum, 'import_nocustomer', $subId, $textHtml);

			return true;
		}

		//非企微客户认领导入
		public static function createFollowUser ($data)
		{
			$uid        = $data['uid'];
			$subId      = $data['sub_id'];
			$corpId     = $data['corp_id'];
			$tagIds     = $data['tag_ids'];
			$userId     = $data['user_id'];
			$insertNum  = $skipNum = $skipPhoneNum = 0;
			$importData = $data['importData'];
			$snum       = count($importData) - 1;
			static::importNumWebsocket($uid, $corpId, $snum, 0, 0, 0, 'import-follow-user', $subId);
			if ($snum > 1000) {
				$perKey = ceil($snum / 50);
			} else {
				$perKey = 10;
			}

			//获取来源选项
			$customField   = CustomField::findOne(['uid' => 0, 'type' => 2, 'key' => 'offline_source']);
			$optionList    = CustomFieldOption::find()->where(['uid' => 0, 'fieldid' => $customField->id])->select('id,match')->all();
			$optionData    = array_column($optionList, 'match', 'id');
			$otherOptionId = array_search('其他', $optionData);
			$time          = time();
			foreach ($importData as $k => $v) {
				if ($k == 1) {
					continue;
				}
				$source = trim($v['I']);
				if (!empty($source)) {
					$optionId = array_search($source, $optionData);
					if (empty($optionId)) {
						$optionId = $otherOptionId;
					}
				} else {
					$optionId = $otherOptionId;
				}
				$data = [
					'uid'             => $uid,
					'sub_id'          => $subId,
					'corp_id'         => $corpId,
					'tag_ids'         => $tagIds,
					'user_id'         => $userId,
					'is_claim'        => 1,
					'name'            => $v['A'],
					'wx_num'          => $v['B'],
					'phone'           => $v['C'],
					'qq'              => $v['D'],
					'sex'             => $v['E'],
					'area'            => $v['F'],
					'email'           => $v['G'],
					'remark'          => $v['H'],
					'field_option_id' => $optionId,
					'company'         => $v['J'],
					'is_from'         => 1,
				];
				// 事务处理
				$transaction = \Yii::$app->mdb->beginTransaction();
				try {
					$type = PublicSeaCustomer::setData($data, 2);
					\Yii::error($type, '$type');
					if (is_numeric($type)) {
						$seaId = $type;
					} else {
						throw new InvalidDataException($type);
					}
					//创建领取记录
					$claimData                  = new PublicSeaClaim();
					$claimData->uid             = $uid;
					$claimData->corp_id         = $corpId;
					$claimData->sea_id          = $seaId;
					$claimData->type            = 0;
					$claimData->claim_type      = 1;
					$claimData->user_id         = $userId;
					$claimData->external_userid = '';
					$claimData->claim_time      = $time;
					$claimData->is_claim        = 0;
					if (!$claimData->validate() || !$claimData->save()) {
						throw new InvalidDataException('skip');
					}
					//创建非企微关联表
					$followUser                   = new PublicSeaContactFollowUser();
					$followUser->close_rate       = 0;
					$followUser->description      = '';
					$followUser->follow_id        = Follow::getFollowIdByUid($uid);
					$followUser->last_follow_time = $time;
					$followUser->add_time         = $time;
					$followUser->corp_id          = $corpId;
					$followUser->sea_id           = $seaId;
					$followUser->user_id          = $userId;
					$followUser->company_name     = !empty($v['J']) ? $v['J'] : '';
					$followUser->is_reclaim       = 0;
					if (!$followUser->validate() || !$followUser->save()) {
						throw new InvalidDataException('skip');
					}
					$transaction->commit();
					//打标签
					if (!empty($tagIds)) {
						if (!is_array($tagIds)) {
							$tagIds = explode(',', $tagIds);
						}
						PublicSeaTag::addUserTag([$followUser->id], $tagIds);
					}

					$insertNum++;
				} catch (InvalidDataException $e) {
					$message = $e->getMessage();
					\Yii::error($message, '$message');
					if ($message == 'skipPhone') {
						$skipPhoneNum++;
					} else {
						$skipNum++;
					}
					$transaction->rollBack();
				}

				if (($k - 1) % $perKey == 0) {
					static::importNumWebsocket($uid, $corpId, $snum, $k - 1, $insertNum, $skipNum + $skipPhoneNum, 'import-follow-user', $subId);
				}
			}

			$textHtml = '本次导入成功' . $insertNum . '条，';
			if (!empty($skipNum)) {
				$textHtml .= '忽略' . $skipNum . '条（已有的），';
			}
			if (!empty($skipPhoneNum)) {
				$textHtml .= $skipPhoneNum . '条格式不正确，';
			}
			$textHtml = trim($textHtml, '，');

			static::importNumWebsocket($uid, $corpId, $snum, $snum, $insertNum, $skipNum + $skipPhoneNum, 'import-follow-user', $subId, $textHtml);
			if ($insertNum > 0) {
				//创建待办事项
				$followId = Follow::getFollowIdByUid($uid);
				WaitTask::publicTask($followId, 2, $corpId);
			}

			return true;
		}

		//认领发送消息
		public static function claimSend ($corpInfo, $follow_user_id, $userId)
		{
			$followUser = WorkExternalContactFollowUser::findOne($follow_user_id);
			if (!empty($followUser)) {
				$workUser = WorkUser::findOne($followUser->user_id);
				if (!empty($workUser)) {
					$where = 'find_in_set("' . $workUser->userid . '",allow_user)';
					if (!empty($workUser->department)) {
						$partyArr = explode(',', $workUser->department);
						$temp     = [];
						foreach ($partyArr as $party) {
							array_push($temp, 'find_in_set(' . $party . ',allow_party)');
						}
						if (!empty($temp)) {
							$tempStr = implode(' or ', $temp);
							$where   = $where . ' or ' . $tempStr;
						}
					}
					\Yii::error($where, 'claimSendWhere');
					$agentInfo = WorkCorpAgent::find()->where(['corp_id' => $corpInfo->id, 'is_del' => WorkCorpAgent::AGENT_NO_DEL, 'close' => WorkCorpAgent::AGENT_NOT_CLOSE, 'agent_type' => WorkCorpAgent::CUSTOM_AGENT])->andWhere($where)->one();
					if (!empty($agentInfo)) {
						\Yii::error($agentInfo->id, 'agent_id');
						$contactInfo = WorkExternalContact::findOne($followUser->external_userid);
						$newWorkUser = WorkUser::findOne($userId);
						if (!empty($contactInfo) && !empty($newWorkUser)) {
							$webUrl     = \Yii::$app->params['web_url'];
							$webPathUrl = $webUrl . PublicSeaReclaimSet::H5_URL . '?user_id=' . $workUser->id . '&type=1&remind_type=2&agent_id=' . $agentInfo->id;
							\Yii::error($webPathUrl, 'webPathUrl');
							$messageContent = '同事【' . $newWorkUser->name . '】已认领您的企微客户【' . $contactInfo->name . '】，请将该客户共享给同事，谢谢。<a href="' . $webPathUrl . '">查看共享客户</a>';
							PublicSeaCustomer::messageSend([$workUser->userid], $agentInfo->id, $messageContent, $corpInfo);
						}
					}
				}
			}
		}

		/**
		 * 发送应用消息
		 *
		 * @param array    $toUser
		 * @param int      $agentId
		 * @param string   $messageContent
		 * @param WorkCorp $authCorp
		 * @param int      $msg_type
		 *
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \app\components\InvalidDataException
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function messageSend ($toUser, $agentId, $messageContent, $authCorp, $msg_type = 1)
		{
			$workApi = WorkUtils::getAgentApi($authCorp->id, $agentId);
			switch ($msg_type) {
				case 1:
					$messageContent = [
						'content' => $messageContent,
					];
					$messageContent = TextMesssageContent::parseFromArray($messageContent);
					break;
			}
			$agent   = WorkCorpAgent::findOne($agentId);
			$message = [
				'touser'                   => $toUser,
				'agentid'                  => $agent->agentid,
				'messageContent'           => $messageContent,
				'duplicate_check_interval' => 10,
			];

			$message = Message::pareFromArray($message);
			try {
				$result = $workApi->MessageSend($message, $invalidUserIdList, $invalidPartyIdList, $invalidTagIdList);
				\Yii::error($result, '$result');
			} catch (\Exception $e) {
				\Yii::error($e->getMessage(), 'messageSendPublic');
			}
		}

		/**
		 * 导入数量发送
		 *
		 * @param $uid
		 * @param $corpid
		 * @param $snum
		 * @param $import_num
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 * @throws \app\components\InvalidParameterException]
		 */
		private static function importNumWebsocket ($uid, $corpid, $snum, $import_num, $insertNum = 0, $skipNum = 0, $type = 'import_nocustomer', $sub_id = 0, $msg = '', $error = 0)
		{
			\Yii::$app->websocket->send([
				'channel' => 'push-message',
				'to'      => $uid,
				'type'    => WebsocketUtil::IMPORT_TYPE,
				'info'    => [
					'type'         => $type,
					'from'         => $uid,
					'corpid'       => $corpid,
					'sub_id'       => $sub_id,
					'snum'         => $snum,
					'import_num'   => $import_num,
					'notImportNum' => $snum - $import_num,
					'successNum'   => $insertNum,
					'failNum'      => $skipNum,
					'textHtml'     => $msg,
					'error'        => $error,
				]
			]);

			return true;
		}

		//非企微公海池指派
		public static function noSeaAssign ($uid, $corpId, $userId, $seaIds, $otherData = [])
		{
			$time = time();
			if (!is_array($seaIds)) {
				$seaIds = [$seaIds];
			}
			$count       = count($seaIds);
			$success     = $fail = 0;
			$errorData   = [];
			$newWorkUser = WorkUser::findOne($userId);
			if (empty($corpId) || empty($newWorkUser)) {
				throw new InvalidDataException('参数不正确！');
			}
			$nowDate = date('Y-m-d');
			$restNum = 0;
			if (!empty($otherData['is_claim'])) {
				$reClaim = PublicSeaReclaimSet::getClaimRule($corpId, $userId);
				if (!empty($reClaim)) {
					$priCount = PublicSeaContactFollowUser::find()->where(['corp_id' => $corpId, 'user_id' => $userId, 'is_reclaim' => 0])->count();
					if ($reClaim->private_num <= $priCount) {
						throw new InvalidDataException('此成员已达认领上限，不能再认领！');
					}
					$restNum = $reClaim->private_num - $priCount;
				}
			}

			$companyInfo  = CustomField::findOne(['uid' => 0, 'key' => 'company', 'is_define' => 0]);
			$customerList = PublicSeaCustomer::find()->where(['uid' => $uid, 'id' => $seaIds])->all();
			if (empty($customerList)) {
				throw new InvalidDataException('未找到对应数据');
			}
			/**@var PublicSeaCustomer $customerInfo * */
			foreach ($customerList as $customerInfo) {
				$transaction = \Yii::$app->mdb->beginTransaction();
				try {
					//原归属成员id
					$oldUserId = $customerInfo->user_id;
					if ($customerInfo->is_claim == 1) {
						throw new InvalidDataException('客户已被认领');
					}
					if (!empty($otherData['is_claim'])) {
						if (!empty($reClaim) && empty($restNum)) {
							throw new InvalidDataException('此成员已达认领上限，不能再认领');
						}
						//重新认领时间限制
						if (!empty($reClaim)) {
							$claimInfo = PublicSeaClaim::find()->where(['sea_id' => $customerInfo->id, 'user_id' => $userId])->orderBy(['id' => SORT_DESC])->one();
							if (!empty($claimInfo) && !empty($claimInfo->reclaim_time)) {
								$reclaimTime = $claimInfo->reclaim_time;
								$reclaimDay  = $reClaim->reclaim_day;
								$claimTime   = $reclaimTime + $reclaimDay * 86400;
								$claimDate   = date('Y-m-d', $claimTime);
								if ($nowDate < $claimDate) {
									throw new InvalidDataException('未到重新认领日期');
								}
							}
						}
					}
					$customerInfo->user_id     = $userId;
					$customerInfo->is_claim    = 1;
					$customerInfo->update_time = $time;
					$customerInfo->update();

					//创建领取记录
					$claimData                  = new PublicSeaClaim();
					$claimData->uid             = $uid;
					$claimData->corp_id         = $corpId;
					$claimData->sea_id          = $customerInfo->id;
					$claimData->type            = 0;
					$claimData->claim_type      = 1;
					$claimData->user_id         = $userId;
					$claimData->external_userid = '';
					$claimData->claim_time      = $time;
					$claimData->is_claim        = !empty($otherData['is_claim']) ? 1 : 0;
					if (!$claimData->validate() || !$claimData->save()) {
						throw new InvalidDataException(SUtils::modelError($claimData));
					}
					$flag = false;
					//创建非企微客户关系
					$followUser = PublicSeaContactFollowUser::findOne(['sea_id' => $customerInfo->id, 'user_id' => $userId]);
					if (empty($followUser)) {
						$followUser              = new PublicSeaContactFollowUser();
						$followUser->close_rate  = 0;
						$followUser->description = '';
						$followUser->follow_id   = Follow::getFollowIdByUid($uid);
						$flag                    = true;
					} else {
						$followUser->update_time = $time;
					}
					$followUser->last_follow_time = $time;
					$followUser->add_time         = $time;
					$followUser->corp_id          = $corpId;
					$followUser->sea_id           = $customerInfo->id;
					$followUser->user_id          = $userId;
					$followUser->is_reclaim       = 0;
					//获取公司名称
					if (!empty($companyInfo)) {
						$fieldValue = CustomFieldValue::findOne(['type' => 4, 'fieldid' => $companyInfo->id, 'cid' => $customerInfo->id]);
					}
					$followUser->company_name = !empty($fieldValue) ? $fieldValue->value : '';
					if (!$followUser->validate() || !$followUser->save()) {
						throw new InvalidDataException(SUtils::modelError($followUser));
					}
					$transaction->commit();
					$success++;
					if (!empty($reClaim) && !empty($otherData['is_claim'])) {
						$restNum--;
					}

					//轨迹
					$subId        = !empty($otherData['sub_id']) ? $otherData['sub_id'] : 0;//操作的sub_id
					$handUserId   = !empty($otherData['user_id']) ? $otherData['user_id'] : 0;//操作人的user_id
					$handUserName = !empty($otherData['user_name']) ? $otherData['user_name'] : '';//操作人名称
					if (!empty($otherData['is_claim'])) {
						//认领轨迹
						$claimCount = PublicSeaClaim::find()->where(['sea_id' => $customerInfo->id, 'type' => 0, 'claim_type' => 1, 'is_claim' => 1])->count();
						$belongName = '';//原归属
						if (!empty($oldUserId)) {
							$belongUser = WorkUser::findOne($oldUserId);
							if (!empty($belongUser)) {
								$belongName = '（原归属于' . $belongUser->name . '）';
							}
						}
						$remark = '该客户' . $belongName . '当前被【' . $newWorkUser->name . '】认领，累计认领' . $claimCount . '次';
						PublicSeaTimeLine::addExternalTimeLine(['uid' => $uid, 'sub_id' => $subId, 'sea_id' => $customerInfo->id, 'user_id' => $userId, 'event' => 'claim_custom', 'related_id' => $handUserId, 'remark' => $remark]);
					} else {
						//分配轨迹
						if (!empty($handUserName)) {
							$handUserName = '【' . $handUserName . '】';
						}
						$belongName = '';//原归属
						if (!empty($oldUserId)) {
							$belongUser = WorkUser::findOne($oldUserId);
							if (!empty($belongUser)) {
								$belongName = '（原归属于' . $belongUser->name . '）';
							}
						}
						$remark = $handUserName . '从公海池里将该客户' . $belongName . '指定分配给【' . $newWorkUser->name . '】';
						PublicSeaTimeLine::addExternalTimeLine(['uid' => $uid, 'sub_id' => $subId, 'sea_id' => $customerInfo->id, 'user_id' => $userId, 'event' => 'assign_custom', 'related_id' => $handUserId, 'remark' => $remark]);
					}

					//打标签
					$tagData = PublicSeaPrivateTag::getTagBySeaId($corpId, $customerInfo->id);
					if (!empty($tagData)) {
						$tagIds = array_column($tagData, 'tid');
						PublicSeaTag::addUserTag([$followUser->id], $tagIds);
					}
					//创建待办事项
					if ($flag) {
						WaitTask::publicTask($followUser->follow_id, 2, $corpId);
					}
				} catch (InvalidDataException $e) {
					$transaction->rollBack();
					$fail++;
					array_push($errorData, $e->getMessage());
					continue;
				}
			}
			if (!empty($otherData['is_claim'])) {
				$textHtml = '认领' . $count . '位客户';
				if (!empty($success)) {
					$textHtml .= '，成功' . $success . '位';
				}
				if (!empty($fail)) {
					$textHtml .= '，失败' . $fail . '位';
				}
				if (!empty($errorData)) {
					$errorData = array_unique($errorData);
					$errorStr  = implode('、', $errorData);
					$textHtml  .= '，失败原因：' . $errorStr;
				}
			} else {
				if (!empty($success)) {
					$textHtml = '已成功指派' . $success . '位客户给' . $newWorkUser->name;
				} else {
					$textHtml = '指派' . $count . '位客户给' . $newWorkUser->name;
					if (!empty($success)) {
						$textHtml .= '，成功' . $success . '位';
					}
					if (!empty($fail)) {
						$textHtml .= '，失败' . $fail . '位';
					}
				}
			}

			if ($count == $fail) {
				throw new InvalidDataException($textHtml);
			}

			return $textHtml;
		}

		//企微公海池指派
		public static function seaAssign ($uid, $corpId, $userId, $seaIds, $otherData = [])
		{
			$errorData = [];
			$time      = time();
			if (!is_array($seaIds)) {
				$seaIds = [$seaIds];
			}
			$sum         = count($seaIds);
			$transferNum = 0;
			$shareNum    = 0;
			$corpInfo    = WorkCorp::findOne($corpId);
			$newWorkUser = WorkUser::findOne($userId);
			if (empty($corpInfo) || empty($newWorkUser)) {
				throw new InvalidDataException('参数不正确！');
			}
			$customerList = PublicSeaCustomer::find()->where(['uid' => $uid, 'id' => $seaIds])->all();
			if (empty($customerList)) {
				throw new InvalidDataException('未找到对应数据');
			}
			/**@var PublicSeaCustomer $customerInfo * */
			foreach ($customerList as $customerInfo) {
				$oldFollowUser = WorkExternalContactFollowUser::findOne($customerInfo->follow_user_id);
				$workUser      = WorkUser::findOne($customerInfo->user_id);
				if (empty($oldFollowUser) || empty($workUser)) {
					continue;
				}
				if ($customerInfo->is_claim == 1) {
					array_push($errorData, '客户已被认领');
					continue;
				}

				//判断是否已加过
				$followUser = WorkExternalContactFollowUser::findOne(['external_userid' => $customerInfo->external_userid, 'user_id' => $userId, 'del_type' => WorkExternalContactFollowUser::WORK_CON_EX]);
				if (!empty($followUser)) {
					array_push($errorData, '已加过该客户');
					continue;
				}

				if ($oldFollowUser->del_type == 1) {
					array_push($errorData, '客户已被员工删除');
					continue;
				} elseif ($oldFollowUser->del_type == 2) {
					array_push($errorData, '员工已被客户删除/拉黑');
					continue;
				} elseif ($oldFollowUser->user_id == $userId) {
					array_push($errorData, '已加过该客户');
					continue;
				} elseif ($workUser->is_del == 1) {
					array_push($errorData, '原归属员工已离职');
					continue;
				}

				//查询是否已认领但还没加,原归属成员与客户是否删除
				$tempClaim = PublicSeaClaimUser::find()->alias('cu');
				$tempClaim = $tempClaim->leftJoin('{{%work_external_contact_follow_user}} wf', 'cu.old_follow_user_id=wf.id');
				$tempClaim = $tempClaim->where(['cu.corp_id' => $corpId, 'cu.external_userid' => $customerInfo->external_userid, 'cu.new_user_id' => $userId, 'wf.del_type' => WorkExternalContactFollowUser::WORK_CON_EX, 'cu.new_follow_user_id' => 0]);
				$tempClaim = $tempClaim->select('cu.id')->all();
				if (!empty($tempClaim)) {
					array_push($errorData, '已认领过客户');
					continue;
				}

				$transaction = \Yii::$app->mdb->beginTransaction();
				try {
					//更改认领状态
					$customerInfo->is_claim    = 1;
					$customerInfo->update_time = $time;
					$customerInfo->update();

					//创建领取记录
					$claimData                  = new PublicSeaClaim();
					$claimData->uid             = $uid;
					$claimData->corp_id         = $corpId;
					$claimData->sea_id          = $customerInfo->id;
					$claimData->type            = 1;
					$claimData->claim_type      = 1;
					$claimData->user_id         = $userId;
					$claimData->external_userid = $customerInfo->external_userid;
					$claimData->claim_time      = $time;
					$claimData->is_claim        = !empty($otherData['is_claim']) ? 1 : 0;
					if (!$claimData->validate() || !$claimData->save()) {
						throw new InvalidDataException(SUtils::modelError($claimData));
					}

					//成员添加后的轨迹数据
					$belongName = '';//原归属
					if (!empty($workUser->name)) {
						$belongName = '（原归属于' . $workUser->name . '）';
					}
					if (empty($otherData['is_claim'])) {
						$handUserName = !empty($otherData['user_name']) ? $otherData['user_name'] : (!empty($otherData['name']) ? $otherData['name'] : '');//操作人名称
						if (!empty($handUserName)) {
							$handUserName = '【' . $handUserName . '】';
						}
						$claimStr = $handUserName . '从公海池里将该客户' . $belongName . '指定分配给【' . $newWorkUser->name . '】，该员工成功添加';
					} else {
						$claimStr = '【' . $newWorkUser->name . '】从公海池认领该客户' . $belongName . '，该员工成功添加';
					}
					//创建成员客户认领
					$claimUser = PublicSeaClaimUser::findOne(['uid' => $uid, 'sea_id' => $customerInfo->id, 'old_follow_user_id' => $customerInfo->follow_user_id, 'new_user_id' => $userId]);
					if (empty($claimUser)) {
						$claimUser                     = new PublicSeaClaimUser();
						$claimUser->uid                = $uid;
						$claimUser->sea_id             = $customerInfo->id;
						$claimUser->corp_id            = $corpId;
						$claimUser->external_userid    = $customerInfo->external_userid;
						$claimUser->old_user_id        = $customerInfo->user_id;
						$claimUser->old_follow_user_id = $customerInfo->follow_user_id;
						$claimUser->new_user_id        = $userId;
						$claimUser->reclaim_rule       = $customerInfo->reclaim_rule;
						$claimUser->reclaim_time       = $customerInfo->reclaim_time;
						$claimUser->add_time           = $time;
						$claimUser->claim_str          = $claimStr;
						if (!$claimUser->validate() || !$claimUser->save()) {
							throw new InvalidDataException(SUtils::modelError($claimUser));
						}
					}
					$transaction->commit();
				} catch (InvalidDataException $e) {
					\Yii::error($e->getMessage(), 'seaAssign_error');
					$transaction->rollBack();
					continue;
				}

				$isSend = 1;
				try {
					$workApi     = WorkUtils::getWorkApi($corpId, WorkUtils::EXTERNAL_API);
					$contactInfo = WorkExternalContact::findOne($customerInfo->external_userid);
					$workUser    = WorkUser::findOne($customerInfo->user_id);
					if (!empty($contactInfo) && !empty($workUser) && !empty($newWorkUser)) {
						$externalUserId = $contactInfo->external_userid;
						$handoverUserId = $workUser->userid;
						$takeoverUserId = $newWorkUser->userid;
						$result         = $workApi->ECTransfer($externalUserId, $handoverUserId, $takeoverUserId);
						if ($result['errcode'] == 0) {
							//分配成员24小时后查询,若还没添加则把状态置为拒绝
							PublicSeaClaimUser::updateStatusJob($claimUser->id);
							//添加分配记录
							$transfer                  = new PublicSeaTransferDetail();
							$transfer->uid             = $uid;
							$transfer->sea_id          = $customerInfo->id;
							$transfer->corp_id         = $corpId;
							$transfer->external_userid = $customerInfo->external_userid;
							$transfer->handover_userid = $customerInfo->user_id;
							$transfer->takeover_userid = $userId;
							$transfer->add_time        = $time;
							if (!$transfer->validate() || !$transfer->save()) {
								\Yii::error(SUtils::modelError($transfer), 'transfer');
							}
							$isSend = 0;
							$transferNum++;
						}
					}
				} catch (\Exception $e) {
					\Yii::error($e->getMessage(), 'ECTransfer' . $customerInfo->id);
				}
				if (!empty($isSend)) {
					$shareNum++;
					//发送消息
					if (!empty($customerInfo->follow_user_id)) {
						PublicSeaCustomer::claimSend($corpInfo, $customerInfo->follow_user_id, $userId);
					}
				}
			}
			if (!empty($otherData['is_claim'])) {
				$operateName = '认领';
				$textHtml    = $operateName . $sum . '位客户，';
			} else {
				$operateName = '指派';
				$textHtml    = $operateName . $sum . '位客户给' . $newWorkUser->name . '，';
			}

			if (!empty($transferNum)) {
				$textHtml .= '其中' . $transferNum . '位客户和接替的成员将在24小时后自动成为联系人；';
			}
			if (!empty($shareNum)) {
				if (empty($transferNum)) {
					$textHtml .= '其中';
				}
				$textHtml .= $shareNum . '位客户共享给【' . $newWorkUser->name . '】，已发应用通知提醒原员工；';
			}
			if (!empty($errorData)) {
				$errorData = array_unique($errorData);
				$errorStr  = implode('、', $errorData);
				$restNum   = $sum - $transferNum - $shareNum;
				if ($restNum > 0) {
					$textHtml .= $restNum . '位客户' . $operateName . '无效，原因如下：' . $errorStr . '。';
				}
			} else {
				if (!empty($transferNum) || !empty($shareNum)) {
					$textHtml = rtrim($textHtml, '；');
					$textHtml .= '。';
				}
			}
			$textHtml = trim($textHtml, '，');
			if (!empty($restNum) && $sum == $restNum) {
				throw new InvalidDataException($textHtml);
			}

			return $textHtml;
		}

		//丢弃到公海池
		public static function giveUp ($uid, $corpId, $type, $followIds, $otherData = [])
		{
			$time = time();
			if (!is_array($followIds)) {
				$followIds = [$followIds];
			}
			$count        = count($followIds);
			$success      = $fail = 0;
			$errorData    = [];
			$subId        = !empty($otherData['sub_id']) ? $otherData['sub_id'] : 0;
			$handUserId   = !empty($otherData['user_id']) ? $otherData['user_id'] : 0;
			$handUserName = !empty($otherData['user_name']) ? '【' . $otherData['user_name'] . '】' : '';

			if ($type == 0) {
				$seaUser = PublicSeaContactFollowUser::find()->where(['id' => $followIds])->all();
				if (empty($seaUser)) {
					throw new InvalidDataException('未找到对应数据');
				}
				/**@var PublicSeaContactFollowUser $seaInfo * */
				foreach ($seaUser as $seaInfo) {
					if ($seaInfo->is_protect == 1) {
						$fail++;
						array_push($errorData, '客户已被保护');
						continue;
					}
					//查询是否有待办
					$isTask = WaitTask::getTaskById($type, $seaInfo->sea_id);
					if (!empty($isTask)) {
						$fail++;
						if ($count > 1) {
							array_push($errorData, '客户有待办项目');
						} else {
							array_push($errorData, '客户有待办项目，不能移入公海池');
						}
						continue;
					}

					/**@var PublicSeaClaim $seaClaim * */
					$seaClaim = PublicSeaClaim::find()->where(['corp_id' => $corpId, 'type' => 0, 'user_id' => $seaInfo->user_id, 'sea_id' => $seaInfo->sea_id])->orderBy(['id' => SORT_DESC])->one();
					if (!empty($seaClaim) && $seaClaim->claim_type == 1 && empty($seaClaim->reclaim_time)) {
						//更新上次认领的回收时间
						$seaClaim->reclaim_time = $time;
						$seaClaim->update();
						//更改公海池最后回收时间
						$seaCustomer = PublicSeaCustomer::findOne($seaClaim->sea_id);
						if (!empty($seaCustomer)) {
							$seaCustomer->update_time  = $time;
							$seaCustomer->is_claim     = 0;
							$seaCustomer->user_id      = $seaInfo->user_id;
							$seaCustomer->reclaim_time = $time;
							//成员部门
							$workUser = WorkUser::findOne($seaInfo->user_id);
							if (!empty($workUser)) {
								$departName = WorkDepartment::getDepartNameByUserId($workUser->department, $workUser->corp_id);
								if (!empty($departName)) {
									$memberName = $workUser->name . '-' . $departName;
								} else {
									$memberName = $workUser->name;
								}
							} else {
								$memberName = '未知';
							}
							$seaCustomer->reclaim_rule = '【' . $memberName . '】，自动放弃';
							$seaCustomer->update();
							//创建回收记录
							$claimInfo                 = new PublicSeaClaim();
							$claimInfo->uid            = $uid;
							$claimInfo->corp_id        = $corpId;
							$claimInfo->sea_id         = $seaClaim->sea_id;
							$claimInfo->type           = 0;
							$claimInfo->claim_type     = 0;
							$claimInfo->user_id        = $seaInfo->user_id;
							$claimInfo->follow_user_id = $seaInfo->id;
							$claimInfo->reclaim_time   = $time;
							if (!$claimInfo->validate() || !$claimInfo->save()) {
								\Yii::error(SUtils::modelError($claimInfo), 'claimInfo_error');
								continue;
							}
						}
					}

					//更改回收状态
					$seaInfo->is_reclaim  = 1;
					$seaInfo->update_time = $time;
					$seaInfo->update();
					$success++;

					//轨迹
					$belongName = '';//原归属
					if (!empty($seaInfo->user_id)) {
						$belongWorkUser = WorkUser::findOne($seaInfo->user_id);
						if (!empty($belongWorkUser)) {
							$belongName = '（原归属于' . $belongWorkUser->name . '）';
						}
					}
					$remark = $handUserName . '主动将该客户' . $belongName . '退回至公海池';
					PublicSeaTimeLine::addExternalTimeLine(['uid' => $uid, 'sub_id' => $subId, 'sea_id' => $seaInfo->sea_id, 'user_id' => $seaInfo->user_id, 'event' => 'give_up_custom', 'related_id' => $handUserId, 'remark' => $remark]);
				}
			} else {
				$workCorp   = WorkCorp::findOne($corpId);
				$followUser = WorkExternalContactFollowUser::find()->where(['id' => $followIds])->select('id,external_userid,user_id,is_reclaim,is_protect')->all();
				if (empty($followUser)) {
					throw new InvalidDataException('未找到对应数据');
				}
				/**@var WorkExternalContactFollowUser $userInfo * */
				foreach ($followUser as $userInfo) {
					if ($userInfo->is_reclaim == 1) {
						$fail++;
						array_push($errorData, '客户已在公海池');
						continue;
					}
					if ($userInfo->is_protect == 1) {
						$fail++;
						array_push($errorData, '客户已被保护');
						continue;
					}
					//同一客户归属多个员工跟进时，是否能退回公海池
					if (empty($workCorp->is_return)) {
						//判断是否还有其他成员正在跟进
						$otherCount = WorkExternalContactFollowUser::find()->where(['external_userid' => $userInfo->external_userid, 'del_type' => WorkExternalContactFollowUser::WORK_CON_EX])->andWhere(['!=', 'id', $userInfo->id])->count();
						if (!empty($otherCount)) {
							$fail++;
							if ($count > 1) {
								array_push($errorData, '客户同时也正在被其他员工跟进,不可退回公海');
							} else {
								array_push($errorData, '该客户同时也正在被其他员工跟进，暂且您不可退回公海');
							}
							continue;
						}
					}
					//查询是否有待办
					$isTask = WaitTask::getTaskById($type, $userInfo->external_userid);
					if (!empty($isTask)) {
						$fail++;
						if ($count > 1) {
							array_push($errorData, '客户有待办项目');
						} else {
							array_push($errorData, '客户有待办项目，不能移入公海池');
						}
						continue;
					}
					//进入回收
					$customer = PublicSeaCustomer::findOne(['corp_id' => $corpId, 'type' => 1, 'user_id' => $userInfo->user_id, 'external_userid' => $userInfo->external_userid]);
					if (empty($customer)) {
						$customer                  = new PublicSeaCustomer();
						$customer->uid             = $uid;
						$customer->corp_id         = $corpId;
						$customer->type            = 1;
						$customer->external_userid = $userInfo->external_userid;
						$customer->user_id         = $userInfo->user_id;
						$customer->follow_user_id  = $userInfo->id;
						$customer->add_time        = $time;
						$customer->reclaim_time    = $time;
						//成员部门
						$workUser = WorkUser::findOne($userInfo->user_id);
						if (!empty($workUser)) {
							$departName = WorkDepartment::getDepartNameByUserId($workUser->department, $workUser->corp_id);
							if (!empty($departName)) {
								$memberName = $workUser->name . '-' . $departName;
							} else {
								$memberName = $workUser->name;
							}
						} else {
							$memberName = '未知';
						}
						$customer->reclaim_rule = '【' . $memberName . '】，自动放弃';
						if (!$customer->validate() || !$customer->save()) {
							\Yii::error(SUtils::modelError($customer), 'customer_error');
							continue;
						}
						//创建回收记录
						$claimInfo                  = new PublicSeaClaim();
						$claimInfo->uid             = $uid;
						$claimInfo->corp_id         = $corpId;
						$claimInfo->sea_id          = $customer->id;
						$claimInfo->type            = 1;
						$claimInfo->claim_type      = 0;
						$claimInfo->user_id         = $userInfo->user_id;
						$claimInfo->external_userid = $userInfo->external_userid;
						$claimInfo->follow_user_id  = $userInfo->id;
						$claimInfo->reclaim_time    = $time;
						if (!$claimInfo->validate() || !$claimInfo->save()) {
							\Yii::error(SUtils::modelError($claimInfo), 'claimInfo_error');
							continue;
						}
						//轨迹
						$belongName = '';//原归属
						if (!empty($userInfo->user_id)) {
							$belongWorkUser = WorkUser::findOne($userInfo->user_id);
							if (!empty($belongWorkUser)) {
								$belongName = '（原归属于' . $belongWorkUser->name . '）';
							}
						}
						$remark = $handUserName . '主动将该客户' . $belongName . '退回至公海池';
						ExternalTimeLine::addExternalTimeLine(['uid' => $uid, 'sub_id' => $subId, 'external_id' => $userInfo->external_userid, 'user_id' => $userInfo->user_id, 'event' => 'give_up_custom', 'related_id' => $handUserId, 'remark' => $remark]);
					} else {
						if (!empty($customer->is_del)) {
							//成员部门
							$workUser = WorkUser::findOne($userInfo->user_id);
							if (!empty($workUser)) {
								$departName = WorkDepartment::getDepartNameByUserId($workUser->department, $workUser->corp_id);
								if (!empty($departName)) {
									$memberName = $workUser->name . '-' . $departName;
								} else {
									$memberName = $workUser->name;
								}
							} else {
								$memberName = '未知';
							}
							$customer->reclaim_rule   = '【' . $memberName . '】，自动放弃';
							$customer->follow_user_id = $userInfo->id;
							$customer->reclaim_time   = $time;
							$customer->is_del         = 0;
							$customer->update();
							//更改回收记录时间
							$seaClaim = PublicSeaClaim::findOne(['corp_id' => $corpId, 'sea_id' => $customer->id, 'type' => 1, 'user_id' => $userInfo->user_id, 'external_userid' => $userInfo->external_userid, 'claim_type' => 0]);
							if (!empty($seaClaim)) {
								$seaClaim->follow_user_id = $userInfo->id;
								$seaClaim->reclaim_time   = $time;
								$seaClaim->update();
							}
						} else {
							$fail++;
							array_push($errorData, '客户已被回收');
							continue;
						}
					}

					$userInfo->is_reclaim = 1;
					$userInfo->update();
					$success++;
				}
			}
			$errorData = array_unique($errorData);

			if ($count == 1) {
				if (!empty($errorData)) {
					throw new InvalidDataException($errorData[0]);
				} else {
					$textHtml = '操作成功';
				}
			} else {
				$textHtml = '设置' . $count . '位客户丢弃';
				if (!empty($success)) {
					$textHtml .= '，成功' . $success . '位';
				}
				if (!empty($fail)) {
					$textHtml .= '，失败' . $fail . '位';
				}
				if (!empty($errorData)) {
					$errorStr = implode('、', $errorData);
					$textHtml .= '，失败原因：' . $errorStr;
				}
				if ($count == $fail) {
					throw new InvalidDataException($textHtml);
				}
			}

			return $textHtml;
		}

		//非企微管理客户转交
		public static function noSeaTransfer ($uid, $corpId, $userId, $followIds, $otherData = [])
		{
			if (!is_array($followIds)) {
				$followIds = [$followIds];
			}
			$count     = count($followIds);
			$success   = $fail = 0;
			$errorData = [];
			$time      = time();
			if (empty($userId)) {
				throw new InvalidDataException('请选择成员');
			}
			$newWorkUser = WorkUser::findOne($userId);
			$companyInfo = CustomField::findOne(['uid' => 0, 'key' => 'company', 'is_define' => 0]);
			$seaUser     = PublicSeaContactFollowUser::find()->where(['id' => $followIds])->all();
			if (empty($newWorkUser) || empty($seaUser)) {
				throw new InvalidDataException('未找到对应数据');
			}
			/**@var PublicSeaContactFollowUser $seaInfo * */
			foreach ($seaUser as $seaInfo) {
				if ($seaInfo->is_protect == 1) {
					$fail++;
					array_push($errorData, '客户已被保护');
					continue;
				}
				if ($seaInfo->user_id == $userId) {
					$fail++;
					if ($count > 1) {
						array_push($errorData, '已认领过客户');
					} else {
						array_push($errorData, '已认领过该客户，无需转交');
					}
					continue;
				}
				if ($seaInfo->is_reclaim == 1) {
					$fail++;
					array_push($errorData, '客户已被回收');
					continue;
				}
				//查询是否有待办
				$isTask = WaitTask::getTaskById(0, $seaInfo->sea_id);
				if (!empty($isTask)) {
					$fail++;
					if ($count > 1) {
						array_push($errorData, '客户有待办项目');
					} else {
						array_push($errorData, '客户有待办项目，不能转交');
					}
					continue;
				}
				$transaction = \Yii::$app->mdb->beginTransaction();
				try {
					/**@var PublicSeaClaim $seaClaim * */
					$seaClaim = PublicSeaClaim::find()->where(['corp_id' => $corpId, 'type' => 0, 'user_id' => $seaInfo->user_id, 'sea_id' => $seaInfo->sea_id])->orderBy(['id' => SORT_DESC])->one();
					if (!empty($seaClaim) && $seaClaim->claim_type == 1 && empty($seaClaim->reclaim_time)) {
						//更新上次认领的回收时间
						$seaClaim->reclaim_time = $time;
						$seaClaim->update();
						//创建回收记录
						$claimInfo                 = new PublicSeaClaim();
						$claimInfo->uid            = $uid;
						$claimInfo->corp_id        = $corpId;
						$claimInfo->sea_id         = $seaClaim->sea_id;
						$claimInfo->type           = 0;
						$claimInfo->claim_type     = 0;
						$claimInfo->user_id        = $seaInfo->user_id;
						$claimInfo->follow_user_id = $seaInfo->id;
						$claimInfo->reclaim_time   = $time;
						$claimInfo->is_claim       = 0;
						if (!$claimInfo->validate() || !$claimInfo->save()) {
							throw new InvalidDataException(SUtils::modelError($claimInfo));
						}
					}
					//更改回收状态
					$seaInfo->is_reclaim  = 1;
					$seaInfo->update_time = $time;
					$seaInfo->update();
					//认领
					$claimData                  = new PublicSeaClaim();
					$claimData->uid             = $uid;
					$claimData->corp_id         = $corpId;
					$claimData->sea_id          = $seaInfo->sea_id;
					$claimData->type            = 0;
					$claimData->claim_type      = 1;
					$claimData->user_id         = $userId;
					$claimData->external_userid = '';
					$claimData->claim_time      = $time;
					$claimInfo->is_claim        = 0;
					if (!$claimData->validate() || !$claimData->save()) {
						throw new InvalidDataException(SUtils::modelError($claimData));
					}
					$flag = false;
					//创建
					$followUser = PublicSeaContactFollowUser::findOne(['sea_id' => $seaInfo->sea_id, 'user_id' => $userId]);
					if (empty($followUser)) {
						$followUser              = new PublicSeaContactFollowUser();
						$followUser->close_rate  = 0;
						$followUser->description = '';
						$followUser->follow_id   = Follow::getFollowIdByUid($uid);
						$flag                    = true;
					} else {
						$followUser->update_time = $time;
					}
					$followUser->last_follow_time = $time;
					$followUser->add_time         = $time;
					$followUser->corp_id          = $corpId;
					$followUser->sea_id           = $seaInfo->sea_id;
					$followUser->user_id          = $userId;
					$followUser->is_reclaim       = 0;
					//获取公司名称
					if (!empty($companyInfo)) {
						$fieldValue = CustomFieldValue::findOne(['type' => 4, 'fieldid' => $companyInfo->id, 'cid' => $seaInfo->sea_id]);
					}
					$followUser->company_name = !empty($fieldValue) ? $fieldValue->value : '';
					if (!$followUser->validate() || !$followUser->save()) {
						throw new InvalidDataException(SUtils::modelError($followUser));
					}

					$transaction->commit();
					$success++;
					//打标签
					$tagData = PublicSeaPrivateTag::getTagBySeaId($corpId, $seaInfo->sea_id);
					if (!empty($tagData)) {
						$tagIds = array_column($tagData, 'tid');
						PublicSeaTag::addUserTag([$followUser->id], $tagIds);
					}
					//创建待办事项
					if ($flag) {
						WaitTask::publicTask($followUser->follow_id, 2, $corpId);
					}
					//转交轨迹
					$subId        = !empty($otherData['sub_id']) ? $otherData['sub_id'] : 0;//操作的sub_id
					$handUserId   = !empty($otherData['user_id']) ? $otherData['user_id'] : 0;//操作人的user_id
					$handUserName = !empty($otherData['user_name']) ? $otherData['user_name'] : '';//操作人名称
					if (!empty($handUserName)) {
						$handUserName = '【' . $handUserName . '】';
					}
					$belongName = '';//原归属
					if (!empty($seaInfo->user_id)) {
						$belongUser = WorkUser::findOne($seaInfo->user_id);
						if (!empty($belongUser)) {
							$belongName = '（原归属于' . $belongUser->name . '）';
						}
					}
					$remark = $handUserName . '将该客户' . $belongName . '转交给【' . $newWorkUser->name . '】';
					PublicSeaTimeLine::addExternalTimeLine(['uid' => $uid, 'sub_id' => $subId, 'sea_id' => $seaInfo->sea_id, 'user_id' => $userId, 'event' => 'transfer_custom', 'related_id' => $handUserId, 'remark' => $remark]);
				} catch (InvalidDataException $e) {
					\Yii::error($e->getMessage(), 'notranfer_error');
					$transaction->rollBack();
					$fail++;
					continue;
				}
			}
			$errorData = array_unique($errorData);
			if ($count == 1) {
				if (!empty($errorData)) {
					throw new InvalidDataException($errorData[0]);
				} else {
					$textHtml = '操作成功';
				}
			} else {
				$textHtml = '设置' . $count . '位客户转交';
				if (!empty($success)) {
					$textHtml .= '，成功' . $success . '位';
				}
				if (!empty($fail)) {
					$textHtml .= '，失败' . $fail . '位';
				}
				if (!empty($errorData)) {
					$errorStr = implode('、', $errorData);
					$textHtml .= '，失败原因：' . $errorStr;
				}
			}
			if ($count == $fail) {
				throw new InvalidDataException($textHtml);
			}

			return $textHtml;
		}

		//企微管理客户转交
		public static function seaTransfer ($uid, $corpId, $userId, $followIds, $otherData = [])
		{

			$errorData   = [];
			$time        = time();
			$sum         = count($followIds);
			$transferNum = 0;
			$shareNum    = 0;
			if (empty($userId)) {
				throw new InvalidDataException('请选择成员');
			}
			$corpInfo    = WorkCorp::findOne($corpId);
			$newWorkUser = WorkUser::findOne($userId);
			$followUser  = WorkExternalContactFollowUser::find()->where(['id' => $followIds])->select('id,external_userid,user_id,is_reclaim,is_protect')->all();
			if (empty($newWorkUser) || empty($followUser)) {
				throw new InvalidDataException('未找到对应数据');
			}
			/**@var WorkExternalContactFollowUser $userInfo * */
			foreach ($followUser as $userInfo) {
				if ($userInfo->is_protect == 1) {
					array_push($errorData, '客户已被保护');
					continue;
				}
				if ($userInfo->user_id == $userId) {
					array_push($errorData, '已加过该客户，无需转交');
					continue;
				}
				if ($userInfo->is_reclaim == 1) {
					array_push($errorData, '客户已在公海池');
					continue;
				}
				//查询是否有待办
				$isTask = WaitTask::getTaskById(1, $userInfo->external_userid);
				if (!empty($isTask)) {
					if ($sum > 1) {
						array_push($errorData, '客户有待办项目');
					} else {
						array_push($errorData, '客户有待办项目，不能转交');
					}
					continue;
				}
				//判断是否已加过
				$tempFollowUser = WorkExternalContactFollowUser::findOne(['external_userid' => $userInfo->external_userid, 'user_id' => $userId, 'del_type' => WorkExternalContactFollowUser::WORK_CON_EX]);
				if (!empty($tempFollowUser)) {
					array_push($errorData, '已加过客户');
					continue;
				}

				//查询是否已认领但还没加,原归属成员与客户是否删除
				$tempClaim = PublicSeaClaimUser::find()->alias('cu');
				$tempClaim = $tempClaim->leftJoin('{{%work_external_contact_follow_user}} wf', 'cu.old_follow_user_id=wf.id');
				$tempClaim = $tempClaim->where(['cu.corp_id' => $corpId, 'cu.external_userid' => $userInfo->external_userid, 'cu.new_user_id' => $userId, 'wf.del_type' => WorkExternalContactFollowUser::WORK_CON_EX, 'cu.new_follow_user_id' => 0]);
				$tempClaim = $tempClaim->select('cu.id')->all();
				if (!empty($tempClaim)) {
					array_push($errorData, '已认领过客户');
					continue;
				}

				$transaction = \Yii::$app->mdb->beginTransaction();
				try {
					/**@var PublicSeaClaim $seaClaim * */
					$seaClaim = PublicSeaClaim::find()->where(['corp_id' => $corpId, 'type' => 1, 'user_id' => $userInfo->user_id, 'external_userid' => $userInfo->external_userid, 'claim_type' => 0, 'follow_user_id' => $userInfo->id])->one();
					if (empty($seaClaim)) {
						//进入回收
						$customerInfo                  = new PublicSeaCustomer();
						$customerInfo->uid             = $uid;
						$customerInfo->corp_id         = $corpId;
						$customerInfo->type            = 1;
						$customerInfo->external_userid = $userInfo->external_userid;
						$customerInfo->user_id         = $userInfo->user_id;
						$customerInfo->follow_user_id  = $userInfo->id;
						$customerInfo->add_time        = $time;
						$customerInfo->reclaim_time    = $time;
						$customerInfo->reclaim_rule    = '指定人员分配';
						if (!$customerInfo->validate() || !$customerInfo->save()) {
							\Yii::error(SUtils::modelError($customerInfo), 'customer_error');
							continue;
						}

						//创建回收记录
						$claimInfo                  = new PublicSeaClaim();
						$claimInfo->uid             = $uid;
						$claimInfo->corp_id         = $corpId;
						$claimInfo->sea_id          = $customerInfo->id;
						$claimInfo->type            = 1;
						$claimInfo->claim_type      = 0;
						$claimInfo->user_id         = $userInfo->user_id;
						$claimInfo->external_userid = $userInfo->external_userid;
						$claimInfo->follow_user_id  = $userInfo->id;
						$claimInfo->reclaim_time    = $time;
						$claimInfo->is_claim        = 0;
						if (!$claimInfo->validate() || !$claimInfo->save()) {
							\Yii::error(SUtils::modelError($claimInfo), 'claimInfo_error');
							continue;
						}
					} else {
						$customerInfo = PublicSeaCustomer::findOne($seaClaim->sea_id);
					}

					$userInfo->is_reclaim = 1;
					$userInfo->update();

					//更改认领状态
					$customerInfo->is_claim    = 1;
					$customerInfo->update_time = $time;
					$customerInfo->update();

					//创建领取记录
					$claimData                  = new PublicSeaClaim();
					$claimData->uid             = $uid;
					$claimData->corp_id         = $corpId;
					$claimData->sea_id          = $customerInfo->id;
					$claimData->type            = 1;
					$claimData->claim_type      = 1;
					$claimData->user_id         = $userId;
					$claimData->external_userid = $userInfo->external_userid;
					$claimData->claim_time      = $time;
					$claimInfo->is_claim        = 0;
					if (!$claimData->validate() || !$claimData->save()) {
						throw new InvalidDataException(SUtils::modelError($claimData));
					}

					//成员添加后的轨迹数据
					$subId        = !empty($otherData['sub_id']) ? $otherData['sub_id'] : 0;//操作的sub_id
					$handUserId   = !empty($otherData['user_id']) ? $otherData['user_id'] : 0;//操作人的user_id
					$handUserName = !empty($otherData['user_name']) ? $otherData['user_name'] : '';//操作人名称
					if (!empty($handUserName)) {
						$handUserName = '【' . $handUserName . '】';
					}
					$belongName = '';//原归属
					if (!empty($userInfo->user_id)) {
						$belongWorkUser = WorkUser::findOne($userInfo->user_id);
						if (!empty($belongWorkUser)) {
							$belongName = '（原归属于' . $belongWorkUser->name . '）';
						}
					}

					//创建成员客户认领
					$claimUser = PublicSeaClaimUser::findOne(['uid' => $uid, 'sea_id' => $customerInfo->id, 'old_follow_user_id' => $userInfo->id, 'new_user_id' => $userId]);
					if (empty($claimUser)) {
						$claimStr = $handUserName . '从公海池里将该客户' . $belongName . '指定分配给【' . $newWorkUser->name . '】，该员工成功添加';

						$claimUser                     = new PublicSeaClaimUser();
						$claimUser->uid                = $uid;
						$claimUser->sea_id             = $customerInfo->id;
						$claimUser->corp_id            = $corpId;
						$claimUser->external_userid    = $customerInfo->external_userid;
						$claimUser->old_user_id        = $customerInfo->user_id;
						$claimUser->old_follow_user_id = $customerInfo->follow_user_id;
						$claimUser->new_user_id        = $userId;
						$claimUser->reclaim_rule       = $customerInfo->reclaim_rule;
						$claimUser->reclaim_time       = $customerInfo->reclaim_time;
						$claimUser->add_time           = $time;
						$claimUser->claim_str          = $claimStr;
						if (!$claimUser->validate() || !$claimUser->save()) {
							throw new InvalidDataException(SUtils::modelError($claimUser));
						}
					}
					$transaction->commit();
				} catch (InvalidDataException $e) {
					\Yii::error($e->getMessage(), 'notranfer_error');
					$transaction->rollBack();
					continue;
				}

				$isSend = 1;
				try {
					$workApi     = WorkUtils::getWorkApi($corpId, WorkUtils::EXTERNAL_API);
					$contactInfo = WorkExternalContact::findOne($customerInfo->external_userid);
					$oldWorkUser = WorkUser::findOne($customerInfo->user_id);
					$workUser    = WorkUser::findOne($userId);
					if (!empty($contactInfo) && !empty($oldWorkUser) && !empty($workUser)) {
						$externalUserId = $contactInfo->external_userid;
						$handoverUserId = $oldWorkUser->userid;
						$takeoverUserId = $workUser->userid;
						$result         = $workApi->ECTransfer($externalUserId, $handoverUserId, $takeoverUserId);
						if ($result['errcode'] == 0) {
							//分配成员24小时后查询,若还没添加则把状态置为拒绝
							PublicSeaClaimUser::updateStatusJob($claimUser->id);
							//添加分配记录
							$transfer                  = new PublicSeaTransferDetail();
							$transfer->uid             = $uid;
							$transfer->sea_id          = $customerInfo->id;
							$transfer->corp_id         = $corpId;
							$transfer->external_userid = $customerInfo->external_userid;
							$transfer->handover_userid = $customerInfo->user_id;
							$transfer->takeover_userid = $userId;
							$transfer->add_time        = $time;
							if (!$transfer->validate() || !$transfer->save()) {
								\Yii::error(SUtils::modelError($transfer), 'transfer');
							}
							$isSend = 0;
							$transferNum++;
						}
					}
				} catch (\Exception $e) {
					\Yii::error($e->getMessage(), 'ECTransfer' . $customerInfo->id);
				}
				if (!empty($isSend)) {
					$transferStr = '共享';
					$shareNum++;
					//发送消息
					if (!empty($customerInfo->follow_user_id)) {
						PublicSeaCustomer::claimSend($corpInfo, $customerInfo->follow_user_id, $userId);
					}
				} else {
					$transferStr = '转交';
				}
				//转交轨迹
				$remark = $handUserName . '将该客户' . $belongName . $transferStr . '给【' . $newWorkUser->name . '】';
				ExternalTimeLine::addExternalTimeLine(['uid' => $uid, 'sub_id' => $subId, 'external_id' => $userInfo->external_userid, 'user_id' => $userInfo->user_id, 'event' => 'transfer_custom', 'related_id' => $handUserId, 'remark' => $remark]);
			}
			$textHtml = '指派' . $sum . '位客户给' . $newWorkUser->name . '，';
			if (!empty($transferNum)) {
				$textHtml .= '其中' . $transferNum . '位客户和接替的成员将在24小时后自动成为联系人；';
			}
			if (!empty($shareNum)) {
				if (empty($transferNum)) {
					$textHtml .= '其中';
				}
				$textHtml .= $shareNum . '位客户共享给【' . $newWorkUser->name . '】，已发应用通知提醒原员工；';
			}
			if (!empty($errorData)) {
				$errorData = array_unique($errorData);
				$errorStr  = implode('、', $errorData);
				$restNum   = $sum - $transferNum - $shareNum;
				if ($restNum > 0) {
					$textHtml .= $restNum . '位客户指派无效，原因如下：' . $errorStr . '。';
				}
			} else {
				if (!empty($transferNum) || !empty($shareNum)) {
					$textHtml = rtrim($textHtml, '；');
					$textHtml .= '。';
				}
			}
			$textHtml = trim($textHtml, '，');

			if (!empty($restNum) && $sum == $restNum) {
				throw new InvalidDataException($textHtml);
			}

			return $textHtml;

		}

		//非企微公海池删除
		public static function noCustomerDel ($seaIds)
		{
			if (!is_array($seaIds)) {
				$seaIds = [$seaIds];
			}
			$count       = count($seaIds);
			$success     = $fail = 0;
			$errorData   = [];
			$seaCustomer = PublicSeaCustomer::find()->where(['id' => $seaIds])->all();
			/**@var PublicSeaCustomer $seaInfo * */
			foreach ($seaCustomer as $seaInfo) {
				if ($seaInfo->is_del == 1) {
					$fail++;
					array_push($errorData, '客户已被删除');
					continue;
				}
				if ($seaInfo->is_claim == 1) {
					$fail++;
					array_push($errorData, '客户已被认领');
					continue;
				}
				//是否有领取记录
				$claimCount = PublicSeaClaim::find()->where(['sea_id' => $seaInfo->id, 'type' => 0, 'claim_type' => 1])->count();
				if (!empty($claimCount)) {
					$fail++;
					array_push($errorData, '客户已有被认领记录，不能删除');
					continue;
				}
				$seaInfo->is_del = 1;
				$seaInfo->update();
				$success++;
			}

			$errorData = array_unique($errorData);
			if ($count == 1) {
				if (!empty($errorData)) {
					throw new InvalidDataException($errorData[0]);
				} else {
					$textHtml = '操作成功';
				}
			} else {
				$textHtml = '删除' . $count . '位客户';
				if (!empty($success)) {
					$textHtml .= '，成功' . $success . '位';
				}
				if (!empty($fail)) {
					$textHtml .= '，失败' . $fail . '位';
				}
				if (!empty($errorData)) {
					$errorStr = implode('、', $errorData);
					$textHtml .= '，失败原因：' . $errorStr;
				}
			}
			if ($count == $fail) {
				throw new InvalidDataException($textHtml);
			}

			return $textHtml;
		}

		//企微客户有跟进时，查询公海池是否有人已认领，没有的话，标记此记录删除
		public static function delSeaCustom ($corpId, $followUser)
		{
			/**@var WorkExternalContactFollowUser $followUser * */
			if (empty($corpId) || empty($followUser->user_id) || empty($followUser->external_userid)) {
				return '';
			}

			try {
				$customer = PublicSeaCustomer::findOne(['corp_id' => $corpId, 'type' => 1, 'user_id' => $followUser->user_id, 'external_userid' => $followUser->external_userid]);
				if (!empty($customer)) {
					if (!empty($customer->is_claim)) {
						return '已被认领';
					}
					//更改公海池状态
					$customer->is_del = 1;
					$customer->update();
					//更改外部联系人回收状态
					$followUser->is_reclaim = 0;
					$followUser->update();
				}
			} catch (\Exception $e) {

			}
		}

		//老数据绑定企业微信
		public static function updateBatch ()
		{
			ini_set('memory_limit', '1024M');
			set_time_limit(0);
			$userCorp = UserCorpRelation::find()->where(['!=', 'uid', ''])->orderBy('create_time desc')->all();
			$uidArr   = [];
			/**@var UserCorpRelation $userInfo * */
			foreach ($userCorp as $userInfo) {
				if (in_array($userInfo->uid, $uidArr)) {
					continue;
				}
				array_push($uidArr, $userInfo->uid);
				$seaList = PublicSeaCustomer::find()->where(['uid' => $userInfo->uid, 'corp_id' => NULL, 'type' => 0])->select('id,uid,corp_id,user_id')->all();
				if (!empty($seaList)) {
					/**@var PublicSeaCustomer $seaInfo * */
					foreach ($seaList as $seaInfo) {
						$seaInfo->corp_id = $userInfo->corp_id;
						if (!empty($seaInfo->user_id)) {
							$workUser = WorkUser::findOne($seaInfo->user_id);
							if (!empty($workUser)) {
								$seaInfo->corp_id = $workUser->corp_id;
							}
						}
						$seaInfo->update();
					}
				}
			}
		}

		//企微绑定非企微
		public static function bindData ($uid, $workCorp, $seaFollowUserId, $followUserId)
		{
			/**@var WorkCorp $workCorp * */
			if (empty($seaFollowUserId) || empty($followUserId)) {
				throw new InvalidDataException('参数不正确');
			}
			$seaFollowUser = PublicSeaContactFollowUser::findOne($seaFollowUserId);
			$followUser    = WorkExternalContactFollowUser::findOne($followUserId);
			if (empty($seaFollowUser) || empty($followUser)) {
				throw new InvalidDataException('参数不正确');
			}
			if (!empty($seaFollowUser->follow_user_id)) {
				throw new InvalidDataException('已被绑定');
			}
			$bindInfo = PublicSeaContactFollowUser::findOne(['corp_id' => $workCorp->id, 'follow_user_id' => $followUserId]);
			if (!empty($bindInfo)) {
				throw new InvalidDataException('已绑定');
			}
			$seaCustomer = PublicSeaCustomer::findOne($seaFollowUser->sea_id);
			if (empty($seaCustomer)) {
				throw new InvalidDataException('参数不正确');
			}
			$workUser = WorkUser::findOne($followUser->user_id);
			if (empty($workUser)) {
				throw new InvalidDataException('参数不正确');
			}

			$seaFollowUser->follow_user_id = $followUserId;
			if (!$seaFollowUser->validate() || !$seaFollowUser->save()) {
				throw new InvalidDataException(SUtils::modelError($seaFollowUser));
			}

			$isMerge = 0;
			//同步非企微用户画像
			if (!empty($workCorp->is_sea_info)) {
				$text = static::syncSeaInfo($uid, $seaFollowUser, $followUser, ['unshare_field' => $workCorp->unshare_field]);
				if (!empty($text)) {
					$isMerge = 1;
					$remark  = $workUser->name . '的企微客户与非企微客户【' . $seaCustomer->name . '】成功绑定，' . $text;
					ExternalTimeLine::addExternalTimeLine(['uid' => $uid, 'sub_id' => 0, 'external_id' => $followUser->external_userid, 'user_id' => $followUser->user_id, 'event' => 'bind_custom', 'related_id' => $seaFollowUser->id, 'remark' => $remark]);
				}
			}
			//同步非企微用户标签
			if (!empty($workCorp->is_sea_tag)) {
				$text = static::syncSeaTag($seaFollowUser->id, $followUser->id);
				if (!empty($text)) {
					$isMerge = 1;
					$remark  = $workUser->name . '的企微客户与非企微客户【' . $seaCustomer->name . '】成功绑定，' . $text;
					ExternalTimeLine::addExternalTimeLine(['uid' => $uid, 'sub_id' => 0, 'external_id' => $followUser->external_userid, 'user_id' => $followUser->user_id, 'event' => 'bind_custom', 'related_id' => $seaFollowUser->id, 'remark' => $remark]);
				}
			}
			//同步非企微用户跟进记录
			if (!empty($workCorp->is_sea_follow)) {
				static::syncSeaFollow($uid, $seaFollowUser, $followUser);
			}
			//同步非企微用户通话记录
			if (!empty($workCorp->is_sea_phone)) {
				static::syncSeaPhone($uid, $seaFollowUser, $followUser);
			}
			//直接绑定不合并资料轨迹
			if (empty($isMerge)) {
				$remark = $workUser->name . '的企微客户与非企微客户【' . $seaCustomer->name . '】成功绑定';
				ExternalTimeLine::addExternalTimeLine(['uid' => $uid, 'sub_id' => 0, 'external_id' => $followUser->external_userid, 'user_id' => $followUser->user_id, 'event' => 'bind_custom', 'related_id' => $seaFollowUser->id, 'remark' => $remark]);
			}

			return true;
		}

		//同步画像
		public static function syncSeaInfo ($uid, $seaFollowUser, $followUser, $otherData)
		{
			$remark = '';
			if (empty($uid) || empty($seaFollowUser) || empty($followUser)) {
				return $remark;
			}
			$unShareField = !empty($otherData['unshare_field']) ? 1 : 0;
			/**@var PublicSeaContactFollowUser $seaFollowUser * */
			$seaId       = $seaFollowUser->sea_id;
			$seaCustomer = PublicSeaCustomer::findOne($seaId);
			/**@var WorkExternalContactFollowUser $followUser * */
			$externalId = $followUser->external_userid;
			$userId     = $followUser->user_id;

			$fieldWhere = '(uid=' . $uid . ' OR is_define = 0) AND status=1';//自定义属性
			$fieldList  = CustomField::find()->where($fieldWhere)->andWhere(['!=', 'key', 'offline_source'])->select('`id` fieldid,`key`,`title`,`type`,`sort`')->orderBy(['sort' => 'desc', 'is_define' => 'asc', 'id' => 'asc'])->asArray()->all();

			$customFieldValue = CustomFieldValue::find()->where(['cid' => $seaId, 'type' => 4])->andWhere(['!=', 'value', ''])->select('`fieldid`,`value`')->groupBy('fieldid')->asArray()->all();
			$customFieldD     = [];
			foreach ($customFieldValue as $k => $v) {
				$customFieldD[$v['fieldid']] = $v['value'];
			}
			$time      = time();
			$sendData  = [];
			$titleData = [];
			foreach ($fieldList as $k => $v) {
				if (!empty($customFieldD[$v['fieldid']]) && ($customFieldD[$v['fieldid']] != '[]')) {
					$seaValue = $customFieldD[$v['fieldid']];
					if ($unShareField == 0) {
						$fieldValue = CustomFieldValue::findOne(['type' => 1, 'cid' => $externalId, 'fieldid' => $v['fieldid']]);
					} else {
						$fieldValue = CustomFieldValue::findOne(['type' => 1, 'cid' => $externalId, 'fieldid' => $v['fieldid'], 'user_id' => $userId]);
					}
					$isPush = 1;
					if (empty($fieldValue)) {
						$fieldValue          = new CustomFieldValue();
						$fieldValue->uid     = $uid;
						$fieldValue->type    = 1;
						$fieldValue->cid     = $externalId;
						$fieldValue->fieldid = $v['fieldid'];
					} elseif ($fieldValue->value == $seaValue) {
						$isPush = 0;
					}
					$fieldValue->user_id = !empty($unShareField) ? $userId : 0;
					$fieldValue->value   = $seaValue;
					$fieldValue->time    = $time;
					if (!$fieldValue->save()) {
						\Yii::error(SUtils::modelError($fieldValue), 'fieldValue');
					}
					if (!empty($isPush)) {
						array_push($titleData, $v['title']);
					}
					if (($v['key'] == 'company') && ($followUser->remark_corp_name != $seaValue)) {
						$sendData['remark_company']   = $seaValue;
						$followUser->remark_corp_name = $seaValue;
					}
					if (($v['type'] == 5) && ($followUser->remark_mobiles != $seaValue)) {
						$sendData['remark_mobiles'] = explode(',', $seaValue);
						$followUser->remark_mobiles = $seaValue;
					}
				}
			}

			//备注
			if (!empty($seaCustomer->remark) && ($seaCustomer->remark != $followUser->nickname)) {
				$followUser->nickname = $seaCustomer->remark;
				$sendData['remark']   = $seaCustomer->remark;
				array_push($titleData, '备注名');
			}
			//描述
			if (!empty($seaFollowUser->description) && ($seaFollowUser->description != $followUser->des)) {
				$followUser->des         = $seaFollowUser->description;
				$sendData['description'] = $seaFollowUser->description;
				array_push($titleData, '描述');
			}

			//预计成交率
			if (!empty($seaFollowUser->close_rate) && ($seaFollowUser->close_rate != $followUser->close_rate)) {
				$followUser->close_rate = $seaFollowUser->close_rate;
				array_push($titleData, '预计成交率');
			}

			//轨迹
			if (!empty($titleData)) {
				if (!$followUser->update()) {
					\Yii::error(SUtils::modelError($followUser), 'followUserxcy');
				}
				$titleStr = implode('、', $titleData);
				$remark   = '将非企微客户资料（' . $titleStr . '）同步过来';
			}

			//修改客户备注信息通知微信
			if (!empty($sendData)) {
				$contactInfo = WorkExternalContact::findOne($followUser->external_userid);
				if (!empty($contactInfo)) {
					$sendData['userid']          = $followUser->userid;
					$sendData['external_userid'] = $contactInfo->external_userid;
					try {
						$workApi = WorkUtils::getWorkApi($contactInfo->corp_id, WorkUtils::EXTERNAL_API);
						if (!empty($workApi)) {
							$contactRemark = ExternalContactRemark::parseFromArray($sendData);
							$data          = $workApi->ECRemark($contactRemark);
							$result        = SUtils::Object2Array($data);
							\Yii::error($result, 'syncSeaInfo');
						}
					} catch (\Exception $e) {
						\Yii::error($sendData, 'sendData');
						\Yii::error($e->getMessage(), 'syncSeaInfo');
					}
				}
			}

			return $remark;
		}

		//同步标签
		public static function syncSeaTag ($seaFollowUserId, $followUserId)
		{
			$tagName    = [];
			$tagIds     = [];
			$seaTagList = PublicSeaTag::find()->where(['follow_user_id' => $seaFollowUserId, 'status' => 1])->all();
			if (!empty($seaTagList)) {
				$workTagList = WorkTagFollowUser::find()->where(['follow_user_id' => $followUserId, 'status' => 1])->all();
				$seaTagIds   = array_column($seaTagList, 'tag_id');
				$workTagIds  = array_column($workTagList, 'tag_id');
				$diffTagIds  = array_diff($seaTagIds, $workTagIds);
				\Yii::error($seaTagIds, 'seaTagIds');
				\Yii::error($workTagIds, 'workTagIds');
				\Yii::error($diffTagIds, 'diffTagIds');
				if (!empty($diffTagIds)) {
					$tagList = WorkTag::find()->where(['id' => $diffTagIds, 'is_del' => 0])->all();
					if (!empty($tagList)) {
						/**@var WorkTag $tag * */
						foreach ($tagList as $tag) {
							array_push($tagIds, $tag->id);
							array_push($tagName, '【' . $tag->tagname . '】');
						}
						WorkTag::addUserTag(2, [$followUserId], $tagIds, [], 1);
					}
				}
			}
			$remark = '';
			if (!empty($tagName)) {
				$tagNameStr = implode('、', $tagName);
				$remark     = '将非企微客户的标签' . $tagNameStr . '同步过来';
			}

			return $remark;
		}

		//同步跟进记录及轨迹
		public static function syncSeaFollow ($uid, $seaFollowUser, $followUser)
		{
			/**@var PublicSeaContactFollowUser $seaFollowUser * */
			/**@var WorkExternalContactFollowUser $followUser * */
			$isLineUpdate  = 0;
			$followNum     = 0;
			$seaRecordList = PublicSeaContactFollowRecord::find()->where(['uid' => $uid, 'sea_id' => $seaFollowUser->sea_id, 'status' => 1, 'is_sync' => 0, 'record_type' => 0])->all();
			if (!empty($seaRecordList)) {
				/**@var PublicSeaContactFollowRecord $seaRecord * */
				foreach ($seaRecordList as $seaRecord) {
					//同步记录
					$record              = new WorkExternalContactFollowRecord();
					$record->uid         = $seaRecord->uid;
					$record->type        = 1;
					$record->external_id = $followUser->external_userid;
					$record->sub_id      = $seaRecord->sub_id;
					$record->user_id     = $seaRecord->user_id;
					$record->record      = $seaRecord->record;
					$record->file        = $seaRecord->file;
					$record->time        = $seaRecord->add_time;
					$record->upt_time    = $seaRecord->update_time;
					$record->status      = 1;
					$record->follow_id   = $seaRecord->follow_id;
					$record->is_master   = $seaRecord->is_master;
					$record->lose_id     = $seaRecord->lose_id;
					if (!$record->validate() || !$record->save()) {
						continue;
					}
					++$followNum;
					//更新
					$seaRecord->is_sync = 1;
					$seaRecord->update();

					//同步轨迹
					$seaRecordLine = PublicSeaTimeLine::findOne(['event' => 'follow', 'related_id' => $seaRecord->id, 'is_sync' => 0]);
					if (!empty($seaRecordLine)) {
						$isLineUpdate          = 1;
						$timeLine              = new ExternalTimeLine();
						$timeLine->uid         = $seaRecordLine->uid;
						$timeLine->external_id = $followUser->external_userid;
						$timeLine->sub_id      = $seaRecordLine->sub_id;
						$timeLine->user_id     = $seaRecordLine->user_id;
						$timeLine->event       = $seaRecordLine->event;
						$timeLine->event_time  = $seaRecordLine->event_time;
						$timeLine->event_id    = $seaRecordLine->event_id;
						$timeLine->related_id  = $record->id;
						$timeLine->remark      = $seaRecordLine->remark;
						if (!$timeLine->validate() || !$timeLine->save()) {
							continue;
						}
						//更新
						$seaRecordLine->is_sync = 1;
						$seaRecordLine->update();
					}
				}

				//更新跟进记录的次数
				if (!empty($isLineUpdate)) {
					$lineList = ExternalTimeLine::find()->alias('tl');
					$lineList = $lineList->leftJoin('{{%work_external_contact_follow_record}} fr', 'tl.related_id = fr.id');
					$lineList = $lineList->where(['tl.event' => 'follow', 'tl.external_id' => $followUser->external_userid, 'fr.record_type' => 0]);
					$lineList = $lineList->orderBy(['tl.event_time' => SORT_ASC])->all();
					if (!empty($lineList)) {
						$num = 0;
						/**@var ExternalTimeLine $line * */
						foreach ($lineList as $line) {
							++$num;
							$line->remark = (string) $num;
							if (!$line->update()) {
								\Yii::error(SUtils::modelError($line), '$numXcy1');
							}
						}
					}
					//跟进次数
					$followUser->follow_num += $followNum;
				}
				//更新跟进状态
				if ($seaFollowUser->last_follow_time > $followUser->update_time) {
					$followUser->follow_id   = $seaFollowUser->follow_id;
					$followUser->update_time = $seaFollowUser->last_follow_time;
				}
				$followUser->update();
			}
		}

		//同步通话记录及轨迹
		public static function syncSeaPhone ($uid, $seaFollowUser, $followUser){
			/**@var PublicSeaContactFollowUser $seaFollowUser * */
			/**@var WorkExternalContactFollowUser $followUser * */
			$isLineUpdate  = 0;
			$seaRecordList = PublicSeaContactFollowRecord::find()->where(['uid' => $uid, 'sea_id' => $seaFollowUser->sea_id, 'status' => 1, 'is_sync' => 0, 'record_type' => 1])->all();
			if(!empty($seaRecordList)){
				/**@var PublicSeaContactFollowRecord $seaRecord * */
				foreach ($seaRecordList as $seaRecord) {
					//同步记录
					$record              = new WorkExternalContactFollowRecord();
					$record->uid         = $seaRecord->uid;
					$record->type        = 1;
					$record->external_id = $followUser->external_userid;
					$record->sub_id      = $seaRecord->sub_id;
					$record->user_id     = $seaRecord->user_id;
					$record->record      = $seaRecord->record;
					$record->file        = $seaRecord->file;
					$record->time        = $seaRecord->add_time;
					$record->upt_time    = $seaRecord->update_time;
					$record->status      = 1;
					$record->follow_id   = $seaRecord->follow_id;
					$record->is_master   = $seaRecord->is_master;
					$record->record_type = 1;
					if (!$record->validate() || !$record->save()) {
						continue;
					}
					//更新
					$seaRecord->is_sync = 1;
					$seaRecord->update();

					//同步轨迹
					$seaRecordLine = PublicSeaTimeLine::findOne(['event' => 'follow', 'related_id' => $seaRecord->id, 'is_sync' => 0]);
					if (!empty($seaRecordLine)) {
						$isLineUpdate          = 1;
						$timeLine              = new ExternalTimeLine();
						$timeLine->uid         = $seaRecordLine->uid;
						$timeLine->external_id = $followUser->external_userid;
						$timeLine->sub_id      = $seaRecordLine->sub_id;
						$timeLine->user_id     = $seaRecordLine->user_id;
						$timeLine->event       = $seaRecordLine->event;
						$timeLine->event_time  = $seaRecordLine->event_time;
						$timeLine->event_id    = $seaRecordLine->event_id;
						$timeLine->related_id  = $record->id;
						$timeLine->remark      = $seaRecordLine->remark;
						if (!$timeLine->validate() || !$timeLine->save()) {
							continue;
						}
						//更新
						$seaRecordLine->is_sync = 1;
						$seaRecordLine->update();
					}
				}

				//更新电话记录的次数
				if ($isLineUpdate) {
					$lineList = ExternalTimeLine::find()->alias('tl');
					$lineList = $lineList->leftJoin('{{%work_external_contact_follow_record}} fr', 'tl.related_id = fr.id');
					$lineList = $lineList->where(['tl.event' => 'follow', 'tl.external_id' => $followUser->external_userid, 'fr.record_type' => 1]);
					$lineList = $lineList->orderBy(['tl.event_time' => SORT_ASC])->all();
					if (!empty($lineList)) {
						$num = 0;
						/**@var ExternalTimeLine $line * */
						foreach ($lineList as $line) {
							++$num;
							$line->remark = (string) $num;
							if (!$line->update()) {
								\Yii::error(SUtils::modelError($line), '$numXcy1');
							}
						}
					}
				}
			}
		}

		//获取外呼电话
		public static function getDialoutPhone ($id)
		{
			$phone = '';
			$data  = static::findOne($id);
			if ($data) {
				$phone = $data->phone ?: '';
			}

			return $phone;
		}
	}
