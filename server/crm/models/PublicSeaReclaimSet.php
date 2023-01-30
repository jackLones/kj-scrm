<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\queue\SyncPublicReclaimJob;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%public_sea_reclaim_set}}".
	 *
	 * @property int      $id
	 * @property int      $uid          账户ID
	 * @property int      $corp_id      授权的企业ID
	 * @property int      $agent_id     应用ID
	 * @property int      $valid_type   生效成员状态：1通用、2仅企业成员适用
	 * @property string   $user_key     生效成员
	 * @property string   $user         用户userID列表
	 * @property string   $party        生效部门
	 * @property string   $reclaim_rule 回收规则
	 * @property int      $private_num  私有池数量
	 * @property int      $is_delay     是否延期：0否、1是
	 * @property int      $delay_day    延期天数
	 * @property int      $reclaim_day  可捡回天数
	 * @property int      $is_protect   是否客户保护：0否、1是
	 * @property int      $protect_num  客户保护数量
	 * @property int      $status       规则状态：0删除、1可用
	 * @property int      $add_time     添加时间
	 * @property int      $update_time  修改时间
	 *
	 * @property WorkCorp $corp
	 */
	class PublicSeaReclaimSet extends \yii\db\ActiveRecord
	{
		const H5_URL = '/h5/pages/highSeasCustomer/index';

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%public_sea_reclaim_set}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_id', 'agent_id', 'valid_type', 'private_num', 'is_delay', 'delay_day', 'reclaim_day', 'status', 'add_time', 'update_time'], 'integer'],
				[['user_key', 'user', 'party', 'reclaim_rule'], 'string'],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'           => Yii::t('app', 'ID'),
				'uid'          => Yii::t('app', '账户ID'),
				'corp_id'      => Yii::t('app', '授权的企业ID'),
				'agent_id'     => Yii::t('app', '应用ID'),
				'valid_type'   => Yii::t('app', '沟通状态：1通用、2仅企业成员适用'),
				'user_key'     => Yii::t('app', '生效成员'),
				'user'         => Yii::t('app', '用户userID列表'),
				'party'        => Yii::t('app', '生效部门'),
				'reclaim_rule' => Yii::t('app', '回收规则'),
				'private_num'  => Yii::t('app', '私有池数量'),
				'is_delay'     => Yii::t('app', '是否延期：0否、1是'),
				'delay_day'    => Yii::t('app', '延期天数'),
				'reclaim_day'  => Yii::t('app', '可捡回天数'),
				'is_protect'   => Yii::t('app', '是否客户保护：0否、1是'),
				'protect_num'  => Yii::t('app', '客户保护数量'),
				'status'       => Yii::t('app', '规则状态：0删除、1可用'),
				'add_time'     => Yii::t('app', '添加时间'),
				'update_time'  => Yii::t('app', '修改时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getCorp ()
		{
			return $this->hasOne(WorkCorp::className(), ['id' => 'corp_id']);
		}

		//回收详情
		public function dumpData ($isList = 0)
		{
			$userKeyArr = json_decode($this->user_key, 1);
			$partyArr   = [];
			if (!empty($this->party)) {
				$partyData = explode(',', $this->party);
				foreach ($partyData as $party) {
					array_push($partyArr, intval($party));
				}
			}

			$ruleArr = json_decode($this->reclaim_rule, 1);
			$result  = [
				'id'           => $this->id,
				'corp_id'      => $this->corp_id,
				'agent_id'     => $this->agent_id,
				'valid_type'   => $this->valid_type,
				'user_key'     => $userKeyArr,
				'party'        => $partyArr,
				'reclaim_rule' => $ruleArr,
				'private_num'  => $this->private_num,
				'is_delay'     => $this->is_delay,
				'delay_day'    => $this->delay_day,
				'reclaim_day'  => $this->reclaim_day,
				'is_protect'   => $this->is_protect,
				'protect_num'  => $this->protect_num,
			];

			if (!empty($isList)) {
				$nameArr = [];
				//生效员工
				if ($this->valid_type == 1) {
					$nameArr = ['所有成员'];
				} else {
					$userKey = json_decode($this->user_key, true);
					if (empty($userKey) && !empty($partyArr)) {
						$departMent = WorkDepartment::find()->where(['corp_id' => $this->corp_id, 'department_id' => $partyArr])->select('name')->all();
						foreach ($departMent as $depart) {
							$temp                = [];
							$temp["id"]          = "d-" . $depart->id;
							$temp["title"]       = $depart->name;
							$temp["scopedSlots"] = ["title" => "title"];
							array_push($userKey, $temp);
						}

					}

				}
				$result['nameArr'] = $nameArr;

				//回收规则
				$ruleData = [];
				foreach ($ruleArr as $rule) {
					$tempStr    = '进入';
					$followInfo = Follow::findOne($rule['follow_id']);
					if (!empty($followInfo)) {
						$title = $followInfo->title;
						if ($followInfo->status == 0) {
							$title .= '（已删除）';
						}
						$tempStr .= '【' . $title . '】阶段，';
					} else {
						continue;
					}
					$tempStr .= $rule['day'] . '天未添加跟进记录';
					array_push($ruleData, $tempStr);
				}
				$result['ruleData'] = $ruleData;
				foreach ($result["user_key"] as &$value) {
					if (!isset($value["title"])) {
						$value["title"] = isset($value["name"]) ? $value["name"] : '';
					}
					if (!isset($value["scopedSlots"])) {
						$value["scopedSlots"] = ['title' => 'custom'];
					}
				}
				//成员限制
				$userLimit = [];
				array_push($userLimit, '每个员工认领客户上限' . $this->private_num . '个');
				if (empty($this->is_delay)) {
					$delayStr = '不允许员工延期';
				} else {
					$delayStr = '允许员工延期' . $this->delay_day . '天';
				}
				array_push($userLimit, $delayStr);
				array_push($userLimit, $this->reclaim_day . '天后员工可捡回');
				$result['userLimit'] = $userLimit;
			}

			return $result;
		}

		//回收设置
		public static function setData ($data)
		{
			$id         = !empty($data['id']) ? $data['id'] : 0;
			$uid        = !empty($data['uid']) ? $data['uid'] : 0;
			$corpId     = !empty($data['corp_id']) ? $data['corp_id'] : 0;
			$agentId    = !empty($data['agent_id']) ? $data['agent_id'] : 0;
			$validType  = !empty($data['valid_type']) ? $data['valid_type'] : 1;
			$userKey    = !empty($data['user_key']) ? $data['user_key'] : [];
			$party      = !empty($data['party']) ? $data['party'] : '';
			$ruleData   = !empty($data['ruleData']) ? $data['ruleData'] : '[]';
			$privateNum = !empty($data['private_num']) ? $data['private_num'] : 0;
			$isDelay    = !empty($data['is_delay']) ? intval($data['is_delay']) : 0;
			$delayDay   = !empty($data['delay_day']) ? $data['delay_day'] : 0;
			$reclaimDay = !empty($data['reclaim_day']) ? $data['reclaim_day'] : 0;
			$isProtect  = !empty($data['is_protect']) ? intval($data['is_protect']) : 0;
			$protectNum = !empty($data['protect_num']) ? $data['protect_num'] : 0;
			if (empty($uid) || empty($corpId) || empty($agentId)) {
				throw new InvalidDataException('参数不正确');
			}
			if (empty($ruleData)) {
				throw new InvalidDataException('请设置规则');
			}
			if (empty($privateNum)) {
				throw new InvalidDataException('请设置私有池数量');
			} elseif ($privateNum > 9999) {
				throw new InvalidDataException('设置私有池数量不能大于9999');
			}
			if (!empty($isDelay)) {
				if (empty($delayDay)) {
					throw new InvalidDataException('请设置延期天数');
				} elseif ($delayDay > 9999) {
					throw new InvalidDataException('设置延期天数不能大于9999');
				}
			} else {
				$delayDay = 0;
			}
			if (empty($reclaimDay)) {
				throw new InvalidDataException('请设置员工捡回天数');
			} elseif ($reclaimDay > 9999) {
				throw new InvalidDataException('设置员工捡回天数不能大于9999');
			}
			if (!empty($isProtect)) {
				if (empty($protectNum)) {
					throw new InvalidDataException('请设置客户保护数量');
				} elseif ($protectNum > 99999) {
					throw new InvalidDataException('设置客户保护数量不能大于99999');
				}
			} else {
				$protectNum = 0;
			}

			//检查规则设置
			$followIdArr = [];
			foreach ($ruleData as $rule) {
				if (empty($rule['follow_id'])) {
					throw new InvalidDataException('请选择跟进状态');
				}
				if (empty($rule['day'])) {
					throw new InvalidDataException('请设置未跟进记录天数');
				}
				if ($rule['day'] < $rule['reclaim_day']) {
					throw new InvalidDataException('回收提醒天数不能大于未跟进记录天数');
				}
				if (in_array($rule['follow_id'], $followIdArr)) {
					throw new InvalidDataException('选择的跟进状态有重复，请更换');
				}
				array_push($followIdArr, $rule['follow_id']);
			}

			$time = time();

			if (!empty($id)) {
				$reClaim = PublicSeaReclaimSet::findOne($id);
				if (empty($reClaim)) {
					throw new InvalidDataException('参数不正确');
				}
				$reClaim->update_time = $time;
			} else {
				$reClaim           = new PublicSeaReclaimSet();
				$reClaim->uid      = $uid;
				$reClaim->corp_id  = $corpId;
				$reClaim->add_time = $time;
			}
			$reClaim->agent_id   = $agentId;
			$reClaim->valid_type = $validType;
			if ($validType == 1) {
				$reClaim->user_key = json_encode([]);
				$reClaim->user     = '';
				$reClaim->party    = '';
			} else {
				if (empty($userKey) && empty($party)) {
					throw new InvalidDataException('成员或者部门要选择一个');
				}
				$reClaim->user_key = json_encode($userKey, 288);
				if (!empty($userKey)) {
					$TempUserIds = array_column($userKey, 'id');
					$userIds     = [];
					foreach ($TempUserIds as $userId) {
						if (strpos($userId, 'd') === false) {
							array_push($userIds, $userId);
						}
					}
					$reClaim->user = '';
					if (!empty($userIds)) {
						$reClaim->user = implode(',', $userIds);
					}
				} else {
					$reClaim->user = '';
				}
				if (!empty($party)) {
					$reClaim->party = implode(',', $party);
				} else {
					$reClaim->party = '';
				}
			}
			$reClaim->reclaim_rule = json_encode($ruleData, 288);
			$reClaim->private_num  = $privateNum;
			$reClaim->is_delay     = $isDelay;
			$reClaim->delay_day    = $delayDay;
			$reClaim->reclaim_day  = $reclaimDay;
			$reClaim->is_protect   = $isProtect;
			$reClaim->protect_num  = $protectNum;
			$reClaim->status       = 1;
			if (!$reClaim->validate() || !$reClaim->save()) {
				throw new InvalidDataException(SUtils::modelError($reClaim));
			}
			\Yii::$app->queue->push(new SyncPublicReclaimJob([
				'reclaim_id' => $reClaim->id
			]));

			return true;
		}

		//成员反查符合的规则
		public static function getClaimRule ($corpId, $userId)
		{
			$count = PublicSeaReclaimSet::find()->where(['corp_id' => $corpId, 'status' => 1])->count();
			if (empty($count)) {
				return [];
			}
			//先根据成员查询
			$reClaim = PublicSeaReclaimSet::find()->where(['corp_id' => $corpId, 'status' => 1])->andWhere("find_in_set ($userId,user)")->one();
			if (!empty($reClaim)) {
				return $reClaim;
			}
			//再根据成员所属部门查询
			$workUser = WorkUser::findOne($userId);
			if (empty($workUser)) {
				return [];
			}
			$departId    = explode(',', $workUser->department);
			$departId    = WorkMsgAuditUser::getDepartIdAll($corpId, $departId);
			$departId    = array_unique($departId);
			$departIdStr = implode(',', $departId);
			$departWhere = '';
			foreach ($departId as $id) {
				$departWhere .= " find_in_set ($id,party) or";
			}
			$departWhere = trim($departWhere, ' or');
			$reClaim     = PublicSeaReclaimSet::find()->where(['corp_id' => $corpId, 'status' => 1])->andWhere(['!=', 'party', '']);
			if (!empty($departIdStr)) {
				$reClaim = $reClaim->andWhere($departWhere)->orderBy(["FIELD(party," . $departIdStr . ")" => true]);
			}
			$reClaim = $reClaim->one();
			if (!empty($reClaim)) {
				return $reClaim;
			}

			//最后选择通用的
			$reClaim = PublicSeaReclaimSet::findOne(['corp_id' => $corpId, 'status' => 1, 'valid_type' => 1]);

			return $reClaim;
		}

		//单条规则回收设置跑数据
		public static function dealData ($reclaimId)
		{
			$reclaim = PublicSeaReclaimSet::findOne($reclaimId);
			if (empty($reclaim)) {
				return [];
			}
			try {
				$delayDay = !empty($reclaim->is_delay) ? intval($reclaim->delay_day) : 0;
				//获取成员
				$workUser = [];
				if ($reclaim->valid_type == 1) {
					$workUser = WorkUser::find()->where(['corp_id' => $reclaim->corp_id, 'is_del' => 0, 'status' => 1])->select('id,userid,name')->all();
				} else {
					$where = ['or'];
					if (!empty($reclaim->user)) {
						$userIds = explode(',', $reclaim->user);
						array_push($where, ['id' => $userIds]);
					}
					if (!empty($reclaim->party)) {
						$departId    = explode(',', $reclaim->party);
						$departWhere = '';
						foreach ($departId as $id) {
							$departWhere .= " find_in_set ($id,department) or";
						}
						$departWhere = trim($departWhere, ' or');
						array_push($where, $departWhere);
					}
					if (count($where) > 1) {
						$workUser = WorkUser::find()->where(['corp_id' => $reclaim->corp_id, 'is_del' => 0])->andWhere($where)->select('id,userid,name')->all();
					}
				}
				$nowDate = date('Y-m-d');
				$ruleArr = json_decode($reclaim->reclaim_rule, 1);
				foreach ($ruleArr as $rule) {
					$followId    = $rule['follow_id'];
					$day         = $rule['day'] + $delayDay;
					$reclaimDay  = $rule['reclaim_day'];
					$reclaimTime = strtotime($nowDate) - ($day - $reclaimDay + 1) * 86400;//提醒时间
					$overTime    = strtotime($nowDate) - $day * 86400;//过期时间
					/**@var WorkUser $user * */
					foreach ($workUser as $user) {
						\Yii::error($user->id, 'user_id');
						if (!empty($reclaimDay)) {
							static::recallRemind($reclaim, $user, ['follow_id' => $followId, 'day' => $day, 'reclaimTime' => $reclaimTime, 'reclaimDay' => $reclaimDay, 'is_send' => 1]);
						}

						static::recallSea($reclaim, $user, ['follow_id' => $followId, 'day' => $day, 'overTime' => $overTime, 'is_send' => 1]);
					}
				}
			} catch (InvalidDataException $e) {
				\Yii::error($e->getMessage(), 'claimSet');
			}
		}

		//每天执行脚本
		public static function reclaimCustomer ()
		{
			$workCorp = WorkCorp::find()->select('id')->all();
			foreach ($workCorp as $corp) {
				\Yii::$app->queue->push(new SyncPublicReclaimJob([
					'corp_id'    => $corp->id,
					'reclaim_id' => 0
				]));
			}
		}

		//客户回收处理
		public static function syncDealData ($corpId)
		{
			try {
				$workUser = WorkUser::find()->where(['corp_id' => $corpId, 'is_del' => 0, 'status' => 1])->select('id,userid,name')->all();
				$nowDate  = date('Y-m-d');
				if (!empty($workUser)) {
					/**@var WorkUser $user * */
					foreach ($workUser as $user) {
						$reclaim = static::getClaimRule($corpId, $user->id);
						if (empty($reclaim)) {
							continue;
						}
						$ruleArr  = json_decode($reclaim->reclaim_rule, 1);
						$delayDay = !empty($reclaim->is_delay) ? intval($reclaim->delay_day) : 0;
						foreach ($ruleArr as $rule) {
							$followId    = $rule['follow_id'];
							$day         = $rule['day'] + $delayDay;
							$reclaimDay  = $rule['reclaim_day'];
							$reclaimTime = strtotime($nowDate) - ($day - $reclaimDay + 1) * 86400;//提醒时间
							$overTime    = strtotime($nowDate) - $day * 86400;//过期时间
							if (!empty($reclaimDay)) {
								static::recallRemind($reclaim, $user, ['follow_id' => $followId, 'day' => $day, 'reclaimTime' => $reclaimTime, 'reclaimDay' => $reclaimDay]);
							}

							static::recallSea($reclaim, $user, ['follow_id' => $followId, 'day' => $day, 'overTime' => $overTime]);
						}
					}
				}
			} catch (InvalidDataException $e) {

			}
		}

		//客户回收提醒
		public static function recallRemind ($reclaim, $user, $otherData)
		{
			/**@var PublicSeaReclaimSet $reclaim * */
			/**@var WorkUser $user * */
			$followId   = $otherData['follow_id'];
			$day        = $otherData['day'];
			$startTime  = $otherData['reclaimTime'];
			$endTime    = $otherData['reclaimTime'] + 86400;
			$webUrl     = \Yii::$app->params['web_url'];
			$webPathUrl = $webUrl . self::H5_URL . '?user_id=' . $user->id . '&follow_id=' . $followId . '&remind_type=0&agent_id=' . $reclaim->agent_id;
			$followInfo = Follow::findOne($followId);
			$customNum  = 0;
			//企微客户
			$followUser = WorkExternalContactFollowUser::find()->where(['user_id' => $user->id, 'follow_id' => $followId, 'is_reclaim' => 0, 'del_type' => WorkExternalContactFollowUser::WORK_CON_EX, 'is_protect' => 0])->andWhere(['between', 'update_time', $startTime, $endTime])->select('id,external_userid,user_id')->all();
			/**@var WorkExternalContactFollowUser $userInfo * */
			foreach ($followUser as $userInfo) {
				//查询是否有待办
				$isTask = WaitTask::getTaskById(1, $userInfo->external_userid);
				if (!empty($isTask)) {
					continue;
				}
				$customNum++;
//				$seaClaim = PublicSeaClaim::find()->where(['corp_id' => $reclaim->corp_id, 'type' => 1, 'user_id' => $userInfo->user_id, 'external_userid' => $userInfo->external_userid])->orderBy(['id' => SORT_DESC])->one();
//				if (empty($seaClaim)) {//进公海池
//					$customNum++;
//				}
			}
			//发送消息
			if (!empty($customNum)) {
				if (!empty($followInfo)) {
					$messageContent = $user->name . '，在【' . $followInfo->title . '】阶段，您有' . $customNum . '位企微客户，即将达到共享客户条件（超过' . $day . '天未添加跟进情况），请尽快跟进。<a href="' . $webPathUrl . '&type=1' . '">查看即将回收客户</a>';
					if (!empty($otherData['is_send'])) {
						$corpInfo = WorkCorp::findOne($reclaim->corp_id);
						PublicSeaCustomer::messageSend([$user->userid], $reclaim->agent_id, $messageContent, $corpInfo);
					} else {
						$sendData = ['corp_id' => $reclaim->corp_id, 'agent_id' => $reclaim->agent_id, 'userid' => $user->userid, 'messageContent' => $messageContent];
						$second   = strtotime(date('Y-m-d 09:00')) - time();
						if ($second < 0) {
							$second = 0;
						}
						\Yii::$app->queue->delay($second)->push(new SyncPublicReclaimJob([
							'sendData' => $sendData,
						]));
					}
				}
			}

			$noCustomNum = 0;
			//非企微客户
			$seaUser = PublicSeaContactFollowUser::find()->where(['user_id' => $user->id, 'follow_id' => $followId, 'is_reclaim' => 0, 'is_protect' => 0, 'follow_user_id' => 0])->andWhere(['between', 'last_follow_time', $startTime, $endTime])->select('*')->all();
			/**@var PublicSeaContactFollowUser $seaInfo * */
			foreach ($seaUser as $seaInfo) {
				//查询是否有待办
				$isTask = WaitTask::getTaskById(0, $seaInfo->sea_id);
				if (!empty($isTask)) {
					continue;
				}
				/**@var PublicSeaClaim $seaClaim * */
				$seaClaim = PublicSeaClaim::find()->where(['corp_id' => $reclaim->corp_id, 'type' => 0, 'user_id' => $seaInfo->user_id, 'sea_id' => $seaInfo->sea_id])->orderBy(['id' => SORT_DESC])->one();
				if (!empty($seaClaim) && $seaClaim->claim_type == 1 && empty($seaClaim->reclaim_time)) {
					$noCustomNum++;
				}
			}
			//发送消息
			if (!empty($noCustomNum)) {
				if (!empty($followInfo)) {
					$messageContent = $user->name . '，在【' . $followInfo->title . '】阶段，您有' . $noCustomNum . '位非企微客户，即将达到回收客户条件（超过' . $day . '天未添加跟进情况），请尽快跟进。<a href="' . $webPathUrl . '&type=0' . '">查看即将回收客户</a>';
					if (!empty($otherData['is_send'])) {
						$corpInfo = WorkCorp::findOne($reclaim->corp_id);
						PublicSeaCustomer::messageSend([$user->userid], $reclaim->agent_id, $messageContent, $corpInfo);
					} else {
						$sendData = ['corp_id' => $reclaim->corp_id, 'agent_id' => $reclaim->agent_id, 'userid' => $user->userid, 'messageContent' => $messageContent];
						$second   = strtotime(date('Y-m-d 09:00')) - time();
						if ($second < 0) {
							$second = 0;
						}
						\Yii::$app->queue->delay($second)->push(new SyncPublicReclaimJob([
							'sendData' => $sendData,
						]));
					}
				}
			}
		}

		//客户回收公海池
		public static function recallSea ($reclaim, $user, $otherData)
		{
			/**@var PublicSeaReclaimSet $reclaim * */
			/**@var WorkUser $user * */
			$followId = $otherData['follow_id'];
			$day      = $otherData['day'];
			$overTime = $otherData['overTime'];
			\Yii::error($otherData, 'otherData');
			$webUrl     = \Yii::$app->params['web_url'];
			$webPathUrl = $webUrl . self::H5_URL . '?user_id=' . $user->id . '&follow_id=' . $followId . '&remind_type=1&agent_id=' . $reclaim->agent_id;
			$followInfo = Follow::findOne($followId);
			$workCorp   = WorkCorp::findOne($reclaim->corp_id);
			try {
				$time      = time();
				$customNum = 0;
				//企微客户
				$followUser = WorkExternalContactFollowUser::find()->where(['user_id' => $user->id, 'follow_id' => $followId, 'is_reclaim' => 0, 'del_type' => WorkExternalContactFollowUser::WORK_CON_EX, 'is_protect' => 0])->andWhere(['<', 'update_time', $overTime])->select('id,external_userid,user_id')->all();
				/**@var WorkExternalContactFollowUser $userInfo * */
				foreach ($followUser as $userInfo) {
					//同一客户归属多个员工跟进时，是否能退回公海池
					if (empty($workCorp->is_return)) {
						//判断是否还有其他成员正在跟进
						$otherCount = WorkExternalContactFollowUser::find()->where(['external_userid' => $userInfo->external_userid, 'del_type' => WorkExternalContactFollowUser::WORK_CON_EX])->andWhere(['!=', 'id', $userInfo->id])->count();
						if (!empty($otherCount)) {
							continue;
						}
					}
					//查询是否有待办
					$isTask = WaitTask::getTaskById(1, $userInfo->external_userid);
					if (!empty($isTask)) {
						continue;
					}

					$userInfo->is_reclaim = 1;
					$userInfo->update();
					$customNum++;

					//进入回收
					$customer = PublicSeaCustomer::findOne(['corp_id' => $reclaim->corp_id, 'type' => 1, 'user_id' => $userInfo->user_id, 'external_userid' => $userInfo->external_userid]);
					if (empty($customer)) {
						$customer                  = new PublicSeaCustomer();
						$customer->uid             = $reclaim->uid;
						$customer->corp_id         = $reclaim->corp_id;
						$customer->type            = 1;
						$customer->external_userid = $userInfo->external_userid;
						$customer->user_id         = $userInfo->user_id;
						$customer->add_time        = $time;
					}
					$customer->follow_user_id = $userInfo->id;
					$customer->reclaim_time   = $time;
					$customer->is_del         = 0;
					$customer->reclaim_rule   = '【' . $followInfo->title . '】，' . $day . '天未跟进';
					if (!$customer->validate() || !$customer->save()) {
						\Yii::error(SUtils::modelError($customer), 'customer_error');
						continue;
					}

					//创建回收记录
					$seaClaim = PublicSeaClaim::findOne(['corp_id' => $reclaim->corp_id, 'sea_id' => $customer->id, 'type' => 1, 'user_id' => $userInfo->user_id, 'external_userid' => $userInfo->external_userid, 'claim_type' => 0]);
					if (empty($seaClaim)) {
						$claimInfo                  = new PublicSeaClaim();
						$claimInfo->uid             = $reclaim->uid;
						$claimInfo->corp_id         = $reclaim->corp_id;
						$claimInfo->sea_id          = $customer->id;
						$claimInfo->type            = 1;
						$claimInfo->claim_type      = 0;
						$claimInfo->user_id         = $userInfo->user_id;
						$claimInfo->external_userid = $userInfo->external_userid;
					}

					$claimInfo->follow_user_id  = $userInfo->id;
					$claimInfo->reclaim_time    = $time;
					if (!$claimInfo->validate() || !$claimInfo->save()) {
						\Yii::error(SUtils::modelError($claimInfo), 'claimInfo_error');
						continue;
					}
					//轨迹
					$belongName     = '';//归属
					$belongWorkUser = WorkUser::findOne($userInfo->user_id);
					if (!empty($belongWorkUser)) {
						$belongName = '（原归属于' . $belongWorkUser->name . '）';
					}
					$reclaimRule = '【' . $followInfo->title . '，' . $day . '天未跟进】';
					$remark      = '达到回收条件' . $reclaimRule . '，该客户' . $belongName . '自动回收至公海池';
					ExternalTimeLine::addExternalTimeLine(['uid' => $reclaim->uid, 'external_id' => $userInfo->external_userid, 'user_id' => $userInfo->user_id, 'event' => 'give_up_custom', 'remark' => $remark]);
				}
				//发送消息
				if (!empty($customNum)) {
					if (!empty($followInfo)) {
						$messageContent = $user->name . '，在【' . $followInfo->title . '】阶段，您有' . $customNum . '位企微客户，由于长时间沟通无果，已进入【可认领】公海池，如有其他同事认领，请您将客户共享给同事，谢谢。<a href="' . $webPathUrl . '&type=1' . '">查看可认领客户</a>';
						\Yii::error($messageContent, 'customNum');
						if (!empty($otherData['is_send'])) {
							$corpInfo = WorkCorp::findOne($reclaim->corp_id);
							PublicSeaCustomer::messageSend([$user->userid], $reclaim->agent_id, $messageContent, $corpInfo);
						} else {
							$sendData = ['corp_id' => $reclaim->corp_id, 'agent_id' => $reclaim->agent_id, 'userid' => $user->userid, 'messageContent' => $messageContent];
							$second   = strtotime(date('Y-m-d 09:00')) - time();
							if ($second < 0) {
								$second = 0;
							}
							\Yii::$app->queue->delay($second)->push(new SyncPublicReclaimJob([
								'sendData' => $sendData,
							]));
						}
					}
				}

				//非企微客户
				$noCustomNum = 0;
				$seaUser     = PublicSeaContactFollowUser::find()->where(['user_id' => $user->id, 'follow_id' => $followId, 'is_reclaim' => 0, 'is_protect' => 0, 'follow_user_id' => 0])->andWhere(['<', 'last_follow_time', $overTime])->select('*')->all();
				/**@var PublicSeaContactFollowUser $seaInfo * */
				foreach ($seaUser as $seaInfo) {
					\Yii::error($seaInfo->id, 'follow_user_id');
					//查询是否有待办
					$isTask = WaitTask::getTaskById(0, $seaInfo->sea_id);
					if (!empty($isTask)) {
						continue;
					}
					/**@var PublicSeaClaim $seaClaim * */
					$seaClaim = PublicSeaClaim::find()->where(['corp_id' => $reclaim->corp_id, 'type' => 0, 'user_id' => $seaInfo->user_id, 'sea_id' => $seaInfo->sea_id])->orderBy(['id' => SORT_DESC])->one();
					if (!empty($seaClaim) && $seaClaim->claim_type == 1 && empty($seaClaim->reclaim_time)) {
						$noCustomNum++;
						//更改回收状态
						$seaInfo->is_reclaim  = 1;
						$seaInfo->update_time = $time;
						$seaInfo->update();
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
							$seaCustomer->reclaim_rule = '【' . $followInfo->title . '】，' . $day . '天未跟进';
							$seaCustomer->update();
							//创建回收记录
							$claimInfo                 = new PublicSeaClaim();
							$claimInfo->uid            = $reclaim->uid;
							$claimInfo->corp_id        = $reclaim->corp_id;
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

						//查看是否有待办如果有删除
						$waitCustom = WaitCustomerTask::find()->where(['type' => 1, 'sea_id' => $seaInfo->sea_id])->all();
						if (!empty($waitCustom)) {
							/** @var WaitCustomerTask $wait */
							foreach ($waitCustom as $wait) {
								if (!empty($wait->queue_id)) {
									\Yii::$app->queue->remove($wait->queue_id);
								}
								$wait->delete();
							}
						}
						//轨迹
						$belongName = '';//归属
						$belongWorkUser = WorkUser::findOne($seaInfo->user_id);
						if (!empty($belongWorkUser)) {
							$belongName = '【' . $belongWorkUser->name . '】';
						}
						$remark = '达到【' . $followInfo->title . '】，' . $day . '天未跟进，属于' . $belongName . '的该客户自动回收至公海池';
						PublicSeaTimeLine::addExternalTimeLine(['uid' => $reclaim->uid, 'sea_id' => $seaInfo->sea_id, 'user_id' => $seaInfo->user_id, 'event' => 'give_up_custom', 'remark' => $remark]);
					}
				}

				//发送消息
				if (!empty($noCustomNum)) {
					if (!empty($followInfo)) {
						$messageContent = $user->name . '，在【' . $followInfo->title . '】阶段，您有' . $noCustomNum . '位非企微客户，达到回收客户条件（超过' . $day . '天未添加跟进情况），已重新归入至【客户公海池】，您无法再对其进行跟进。<a href="' . $webPathUrl . '&type=0' . '">查看回收客户</a>';
						\Yii::error($messageContent, 'customNum');
						if (!empty($otherData['is_send'])) {
							$corpInfo = WorkCorp::findOne($reclaim->corp_id);
							PublicSeaCustomer::messageSend([$user->userid], $reclaim->agent_id, $messageContent, $corpInfo);
						} else {
							$sendData = ['corp_id' => $reclaim->corp_id, 'agent_id' => $reclaim->agent_id, 'userid' => $user->userid, 'messageContent' => $messageContent];
							$second   = strtotime(date('Y-m-d 09:00')) - time();
							if ($second < 0) {
								$second = 0;
							}
							\Yii::$app->queue->delay($second)->push(new SyncPublicReclaimJob([
								'sendData' => $sendData,
							]));
						}

					}
				}
			} catch (InvalidDataException $e) {
				\Yii::error($e->getMessage(), 'recallSea');
			}
		}

		//更近跟进id获取回收提醒
		public static function getSeaRule ($corpId, $userId, $otherData = [])
		{
			if (empty($corpId) || empty($userId)) {
				return '';
			}
			$overStr = '';
			try {
				$reclaim = static::getClaimRule($corpId, $userId);
				if (empty($reclaim) || empty($otherData['follow_id']) || empty($otherData['last_follow_time'])) {
					return '';
				}

				$followId       = $otherData['follow_id'];
				$lastFollowTime = $otherData['last_follow_time'];

				$ruleArr  = json_decode($reclaim->reclaim_rule, 1);
				$delayDay = !empty($reclaim->is_delay) ? intval($reclaim->delay_day) : 0;

				$nowDate = date('Y-m-d');

				foreach ($ruleArr as $rule) {
					if (($rule['follow_id'] == $followId) && !empty($rule['reclaim_day'])) {
						$reclaimDay = $rule['reclaim_day'];
						$overTime   = $lastFollowTime + ($rule['day'] + $delayDay) * 86400;
						$startTime  = $overTime - ($reclaimDay - 1) * 86400;
						$startDate  = date('Y-m-d', $startTime);
						$overDate   = date('Y-m-d', $overTime);
						if ($startDate <= $nowDate && $nowDate <= $overDate) {
							if ($startDate == $overDate) {
								$overStr = '今日';
							} else {
								$overStr = '【' . $nowDate . '~' . $overDate . '期间】';
							}
						}
						break;
					}
				}
			} catch (InvalidDataException $e) {

			}

			return $overStr;
		}

		//定时发送消息方法
		public static function messageSend ($sendData)
		{
			if (empty($sendData['corp_id']) || empty($sendData['agent_id']) || empty($sendData['messageContent']) || empty($sendData['userid'])) {
				return [];
			}
			$corpId         = $sendData['corp_id'];
			$agentId        = $sendData['agent_id'];
			$messageContent = $sendData['messageContent'];
			$userId         = $sendData['userid'];
			$corpInfo       = WorkCorp::findOne($corpId);
			PublicSeaCustomer::messageSend([$userId], $agentId, $messageContent, $corpInfo);
		}
	}
