<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\queue\WorkChatRemindSendJob;
	use app\util\DateUtil;
	use app\util\SUtils;
	use dovechen\yii2\weWork\Work;
	use Yii;

	/**
	 * This is the model class for table "{{%limit_word_remind}}".
	 *
	 * @property int      $id
	 * @property int      $corp_id          企业微信id
	 * @property int      $agent_id         应用id
	 * @property int      $limit_user_id    被监控成员id
	 * @property int      $is_leader        是否通知部门负责人：1是 0否
	 * @property string   $remind_user      接收成员
	 * @property string   $word_ids         敏感词id
	 * @property int      $status           0删除、1关闭、2开启
	 * @property string   $update_time      更新时间
	 * @property string   $add_time         创建时间
	 *
	 * @property WorkCorp $corp
	 */
	class LimitWordRemind extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%limit_word_remind}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_id', 'remind_user', 'word_ids'], 'required'],
				[['corp_id', 'agent_id', 'is_leader', 'status'], 'integer'],
				[['remind_user', 'word_ids'], 'string'],
				[['update_time', 'add_time'], 'safe'],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'            => Yii::t('app', 'ID'),
				'corp_id'       => Yii::t('app', '企业微信ID'),
				'agent_id'      => Yii::t('app', '应用ID'),
				'limit_user_id' => Yii::t('app', '被监控成员id'),
				'is_leader'     => Yii::t('app', '是否通知部门负责人：1是 0否'),
				'remind_user'   => Yii::t('app', '接收成员'),
				'word_ids'      => Yii::t('app', '敏感词id'),
				'status'        => Yii::t('app', '0删除、1关闭、2开启'),
				'update_time'   => Yii::t('app', '修改改时间'),
				'add_time'      => Yii::t('app', '添加时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getCorp ()
		{
			return $this->hasOne(WorkCorp::className(), ['id' => 'corp_id']);
		}

		//设置数据
		public static function setData ($postData)
		{
			$corp_id     = !empty($postData['corp_id']) ? $postData['corp_id'] : 0;
			$agent_id    = !empty($postData['agent_id']) ? $postData['agent_id'] : 0;
			$userIdArr   = !empty($postData['userIdArr']) ? $postData['userIdArr'] : [];
			$is_leader   = !empty($postData['is_leader']) ? $postData['is_leader'] : 0;
			$remind_user = !empty($postData['remind_user']) ? $postData['remind_user'] : [];
			$word_ids    = !empty($postData['word_ids']) ? $postData['word_ids'] : [];
			$uid         = !empty($postData['uid']) ? $postData['uid'] : '';
			$audit_id    = !empty($postData['audit_id']) ? $postData['audit_id'] : [];
			if (empty($corp_id) || empty($agent_id) || empty($userIdArr)) {
				throw new InvalidDataException('参数不正确');
			}

			//检查监控人员是否创建过
			$tempRemind = static::findOne(['corp_id' => $corp_id, 'limit_user_id' => $userIdArr]);
			if (!empty($tempRemind)) {
				throw new InvalidDataException('成员已设置过，不能重复设置');
			}
			if (empty($remind_user) && empty($is_leader)) {
				throw new InvalidDataException('请设置提醒通知成员');
			}

			$addTime = DateUtil::getCurrentTime();

			$transaction = \Yii::$app->db->beginTransaction();
			try {
				foreach ($userIdArr as $userId) {
					if (empty($remind_user) && !empty($is_leader)) {
						$leaderUserId = WorkUser::getLeaderUserId($userId);
						if (empty($leaderUserId)) {
							throw new InvalidDataException('指定的成员没有部门负责人，请重新设置');
						}
					}

					$remindInfo                = new LimitWordRemind();
					$remindInfo->corp_id       = $corp_id;
					$remindInfo->agent_id      = $agent_id;
					$remindInfo->add_time      = $addTime;
					$remindInfo->limit_user_id = $userId;
					$remindInfo->is_leader     = $is_leader;
					$remindInfo->remind_user   = json_encode($remind_user, JSON_UNESCAPED_UNICODE);
					$remindInfo->word_ids      = implode(',', $word_ids);
					if (!$remindInfo->validate() || !$remindInfo->save()) {
						throw new InvalidDataException(SUtils::modelError($remindInfo));
					}
					//同步以前的消息敏感词监控
					LimitWordMsg::pushJob($word_ids, ['corp_id' => $corp_id, 'user_id' => $userId, 'audit_id' => $audit_id, 'uid' => $uid]);
				}
				$transaction->commit();
			} catch (InvalidDataException $e) {
				$transaction->rollBack();
				throw new InvalidDataException($e->getMessage());
			}

			return true;
		}

		//修改数据
		public static function updateData ($postData)
		{
			$id          = !empty($postData['id']) ? $postData['id'] : 0;
			$agent_id    = !empty($postData['agent_id']) ? $postData['agent_id'] : 0;
			$is_leader   = !empty($postData['is_leader']) ? $postData['is_leader'] : 0;
			$remind_user = !empty($postData['remind_user']) ? $postData['remind_user'] : [];
			$word_ids    = !empty($postData['word_ids']) ? $postData['word_ids'] : [];
			if (empty($id) || empty($agent_id)) {
				throw new InvalidDataException('参数不正确');
			}
			$remindInfo = static::findOne($id);
			if (empty($remindInfo)) {
				throw new InvalidDataException('参数不正确');
			}
			$remindInfo->agent_id    = $agent_id;
			$remindInfo->is_leader   = $is_leader;
			$remindInfo->remind_user = json_encode($remind_user, JSON_UNESCAPED_UNICODE);
			$remindInfo->word_ids    = implode(',', $word_ids);
			if (!$remindInfo->validate() || !$remindInfo->save()) {
				throw new InvalidDataException(SUtils::modelError($remindInfo));
			}

			//同步以前的消息敏感词监控
			$workCorp = WorkCorp::findOne($remindInfo->corp_id);
			$uid      = $workCorp->userCorpRelations[0]->uid;
			$audit_id = $workCorp->workMsgAudit->id;
			LimitWordMsg::pushJob($word_ids, ['corp_id' => $remindInfo->corp_id, 'user_id' => $remindInfo->limit_user_id, 'audit_id' => $audit_id, 'uid' => $uid]);

			return true;
		}

		//获取提醒数据
		public static function getData ($remind)
		{
			/**@var LimitWordRemind $remind * */
			$data                    = [];
			$data['id']              = $remind->id;
			$data['key']             = $remind->id;
			$data['status']          = $remind->status;
			$workUser                = WorkUser::findOne($remind->limit_user_id);
			$data['name']            = !empty($workUser) ? $workUser->name : '';
			$data['avatar']          = !empty($workUser) ? $workUser->avatar : '';
			$data['gender']          = !empty($workUser) ? $workUser->gender : '';
			$departName              = WorkDepartment::getDepartNameByUserId($workUser->department, $workUser->corp_id);
			$data['department_name'] = $departName;

			//敏感词
			$wordIds   = explode(',', $remind->word_ids);
			$limitWord = LimitWord::getList('', $wordIds);
			if (!empty($limitWord)) {
				$data['word_name'] = array_column($limitWord, 'title');
			} else {
				$data['word_name'] = [];
			}

			//通知人
			$nameArr    = [];
			$remindUser = json_decode($remind->remind_user, 1);
			if (!empty($remindUser)) {
				$nameArr = array_column($remindUser, 'title');
			}
			if (!empty($remind->is_leader)) {
				$leaderUserId = WorkUser::getLeaderUserId($remind->limit_user_id);
				if (!empty($leaderUserId)) {
					$userList = WorkUser::find()->where(['id' => $leaderUserId])->all();
					/**@var WorkUser $user * */
					foreach ($userList as $user) {
						array_push($nameArr, $user->name);
					}
					$nameArr = array_unique($nameArr);
				}
			}
			$data['user_name'] = $nameArr;

			return $data;
		}

		/*
		 * 记录提醒发送数据
		 *
		 * $auditInfo 会话消息数据
		 * $content  发送内容
		 */
		public static function setSendData ($auditInfo, $content)
		{
			try {
				/**@var WorkMsgAuditInfo $auditInfo * */
				$corpId   = $auditInfo->audit->corp_id;
				$userCorp = UserCorpRelation::findOne(['corp_id' => $corpId]);
				if (empty($userCorp)) {
					return false;
				}
				$uid = $userCorp->uid;

				$fromType = $auditInfo->from_type;
				$toType   = $auditInfo->to_type;
				//员工对员工的不做通知
				if ($fromType == SUtils::IS_WORK_USER && $toType == SUtils::IS_WORK_USER) {
					return false;
				}
				//机器人的不做统计
				if ($fromType == SUtils::IS_ROBOT_USER) {
					return false;
				}

				$userId = '';//成员id
				//发送人
				if ($fromType == SUtils::IS_WORK_USER) {
					$userId = $auditInfo->user_id;
				}
				//接收人
				switch ($toType) {
					case SUtils::IS_WORK_USER:
						$userInfo = WorkUser::findOne(['corp_id' => $corpId, 'id' => $auditInfo->to_user_id]);
						if (!empty($userInfo)) {
							$userId = $userInfo->id;
						}
						break;
				}

				if (empty($userId)) {
					return false;
				}

				$remindInfo = LimitWordRemind::findOne(['corp_id' => $corpId, 'limit_user_id' => $userId, 'status' => 2]);
				if (empty($remindInfo)) {
					return false;
				}

				$wordIds = explode(',', $remindInfo->word_ids);
				//系统敏感词
				$wordData  = LimitWord::checkWord(['ids' => $wordIds, 'is_system' => 1, 'content' => $content]);
				$idData    = $wordData['idData'];
				$titleData = $wordData['titleData'];
				if (empty($idData)) {
					return false;
				}

				//提醒人
				$sendUser = [];
				if (!empty($remindInfo->remind_user)) {
					$remind_user = json_decode($remindInfo->remind_user, true);
					foreach ($remind_user as $v) {
						array_push($sendUser, $v['id']);
					}
				}
				if (!empty($remindInfo->is_leader)) {
					$leaderUserId = WorkUser::getLeaderUserId($remindInfo->limit_user_id);
					if (!empty($leaderUserId)) {
						$sendUser = array_merge($sendUser, $leaderUserId);
					}
				}

				if (empty($sendUser)) {
					return false;
				}

				$sendUser = array_unique($sendUser);
				$sendUser = array_values($sendUser);

				//通知记录
				$remindSend                = new WorkChatRemindSend();
				$remindSend->corp_id       = $corpId;
				$remindSend->audit_info_id = $auditInfo->id;
				$remindSend->remind_id     = $remindInfo->id;
				$remindSend->from_type     = $auditInfo->from_type;
				$remindSend->user_id       = $auditInfo->user_id;
				$remindSend->external_id   = $auditInfo->external_id;
				$remindSend->tolist        = $auditInfo->tolist;
				$remindSend->send_user_id  = !empty($sendUser) ? json_encode($sendUser) : '';
				$remindSend->msgtype       = WorkMsgAuditInfoText::MSG_TYPE;
				$remindSend->content       = implode('/', $titleData);
				$remindSend->time          = time();

				if ($remindSend->save()) {
					\Yii::$app->work->push(new WorkChatRemindSendJob([
						'work_chat_remind_send_id' => $remindSend->id
					]));
				}

				//违规监控
				foreach ($idData as $word_id) {
					LimitWordMsg::setMsg(['corp_id' => $corpId, 'word_id' => $word_id, 'audit_info_id' => $auditInfo->id, 'from_type' => $fromType, 'uid' => $uid]);
				}

			} catch (\Exception $e) {

			}
		}
	}
