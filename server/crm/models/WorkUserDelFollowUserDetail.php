<?php

	namespace app\models;

	use app\util\WorkUtils;
	use dovechen\yii2\weWork\src\dataStructure\Message;
	use dovechen\yii2\weWork\src\dataStructure\TextMesssageContent;
	use Matrix\Exception;
	use Yii;

	/**
	 * This is the model class for table "{{%work_user_del_follow_user_detail}}".
	 *
	 * @property int      $id
	 * @property int      $corp_id         企业应用id
	 * @property int      $agent           应用id
	 * @property int      $user_id         员工id
	 * @property int      $external_userid 外部联系人id
	 * @property int      $repetition      是否重复删除
	 * @property int      $create_time
	 * @property int      $update_time
	 * @property int      $del_type        删除时候状态
	 *
	 * @property WorkCorp $corp
	 * @property WorkUser $user
	 */
	class WorkUserDelFollowUserDetail extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_user_del_follow_user_detail}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_id', 'agent', 'user_id', 'external_userid', 'repetition', 'create_time', 'update_time', 'del_type'], 'integer'],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
				[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkUser::className(), 'targetAttribute' => ['user_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'              => Yii::t('app', 'ID'),
				'corp_id'         => Yii::t('app', '企业应用id'),
				'agent'           => Yii::t('app', '应用id'),
				'user_id'         => Yii::t('app', '员工id'),
				'external_userid' => Yii::t('app', '外部联系人id'),
				'repetition'      => Yii::t('app', '是否重复删除'),
				'create_time'     => Yii::t('app', 'Create Time'),
				'update_time'     => Yii::t('app', 'Update Time'),
				'del_type'        => Yii::t('app', '删除时候状态'),
			];
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
		public function getUser ()
		{
			return $this->hasOne(WorkUser::className(), ['id' => 'user_id']);
		}

		public static function UserDelFollowUser ($corp_id, $user_id, $workExternalUserId)
		{
			try {
				if (Yii::$app->cache->exists("$corp_id" . "$user_id" . "$workExternalUserId")) {
					return;
				}
				Yii::$app->cache->set("$corp_id" . "$user_id" . "$workExternalUserId", 1, 5);
				$res        = self::findOne(["corp_id" => $corp_id, "user_id" => $user_id, "external_userid" => $workExternalUserId]);
				$followUser = WorkExternalContactFollowUser::findOne(["external_userid" => $workExternalUserId, "user_id" => $user_id]);
				if (empty($res)) {
					$res                  = new self();
					$res->corp_id         = $corp_id;
					$res->repetition      = 0;
					$res->user_id         = $user_id;
					$res->external_userid = $workExternalUserId;
				} else {
					$res->repetition = 1;
				}
				$res->del_type    = empty($followUser) ? 0 : $followUser->del_type;
				$res->create_time = time();
				$res->save();
				self::SendTimeMessage($corp_id, $user_id, $workExternalUserId, $res, false, 1, $res->del_type);
//				Yii::$app->cache->delete("$corp_id"."$user_id"."$workExternalUserId");
			} catch (\Exception $e) {
				Yii::error($e->getLine(), "UserDelFollowUser");
				Yii::error($e->getMessage(), "UserDelFollowUser");
			}

		}

		public static function sendTimingMessageDelFollowUser ()
		{
			$time1                 = strtotime(date("Y-m-d", strtotime("-1 day")));
			$time2                 = strtotime(date("Y-m-d", time())) - 1;
			$WorkUserDelFollowUser = WorkUserDelFollowUser::find()->alias("a")
				->leftJoin("{{%work_user}} as b", "a.user_id = b.id")
				->leftJoin("{{%work_follow_user}} as c", "c.corp_id = a.corp_id and c.user_id = a.user_id ")
				->where(["a.open_status" => 1, "c.status" => 1])
				->select("a.*,b.userid")
				->asArray()->all();
			Yii::error(json_encode($WorkUserDelFollowUser), '$WorkUserDelFollowUser');
			foreach ($WorkUserDelFollowUser as $activeRecord) {
				$department    = explode(",", $activeRecord['department']);
				$inform_user   = explode(",", $activeRecord['inform_user']);
				$user_ids      = self::getWorkUserDelLists($department, $inform_user, $activeRecord, $activeRecord["type"]);
				$UserDelDetail = WorkUserDelFollowUserDetail::find()
					->where(["corp_id" => $activeRecord["corp_id"]])
					->andWhere(["in", "user_id", $user_ids])
					->andFilterWhere(["between", "create_time", $time1, $time2])
					->groupBy("user_id,external_userid,del_type")
					->orderBy("create_time desc");
				$UserDelDetail  = $UserDelDetail->asArray()->all();
				$messageContent = "昨日（" . date("Y-m-d", strtotime("-1 day")) . "）员工删人数据汇总:\r\n\r\n";
				if (empty($UserDelDetail)) {
					continue;
				}
				try {
					foreach ($UserDelDetail as $key => $item) {
						$messageContent .= ($key + 1) . "、" . self::SendTimeMessage($item["corp_id"], $item["user_id"], $item['external_userid'], 0, $item["create_time"], 2, $item["del_type"]);
					}
					self::SendAgentMessage([$activeRecord['userid']], $activeRecord["agent"], $messageContent, $activeRecord["corp_id"], 0);
				} catch (\Exception $e) {
					Yii::error($e->getLine(), '$WorkUserDelFollowUser');
					Yii::error($e->getMessage(), '$WorkUserDelFollowUser');
				}
			}

		}

		public static function getWorkUserDelLists ($department, $inform_user, $activeRecord, $type)
		{
			if ($type == 1) {
				$WorkUser = WorkUser::find()
					->andWhere(["corp_id" => $activeRecord["corp_id"]])
					->select("id")->asArray()->all();

				return array_column($WorkUser, "id");
			}
			$user_ids = [];
			if (!empty($department)) {
//				$department = WorkDepartment::getDepartmentChildren($department, $activeRecord["corp_id"]);
//				foreach ($department as $item) {
//					$WorkUser = WorkUser::find()
//						->where("FIND_IN_SET('$item',department)")
//						->andWhere(["corp_id" => $activeRecord["corp_id"]])
//						->select("id")->asArray()->all();
//					if (!empty($WorkUser)) {
//						$user_ids = array_merge($user_ids, array_column($WorkUser, "id"));
//					}
//				}
				$user_ids = WorkDepartment::GiveDepartmentReturnUserData($activeRecord["corp_id"], $department, [], 0, true,0);
			}
			$user_ids = array_merge($inform_user, $user_ids);

			return array_unique($user_ids);
		}

		public static function SendTimeMessage ($corp_id, $user_id, $workExternalUserId, $res, $time = false, $frequency = 1, $del_type = 0)
		{
			$WorkUser            = WorkUser::findOne($user_id);
			$WorkExternalContact = WorkExternalContact::findOne($workExternalUserId);
			if ($frequency == 2) {
				if ($del_type == 2) {
					return date("H:i", $time) . '成员【' . $WorkUser->name . "】删除客户【" . $WorkExternalContact->name . "】;删除原因：客户已将员工删除\r\n\r\n";
				} else {
					$nowTime   = time();
					$claimUser = PublicSeaClaimUser::find()->where(['corp_id' => $corp_id, 'old_user_id' => $user_id, 'external_userid' => $workExternalUserId, 'status' => 0])->andWhere(['between', 'add_time', $nowTime - 600, $nowTime + 600])->one();
					if (!empty($claimUser)) {
						$newWorkUser = WorkUser::findOne($claimUser->new_user_id);
						if (!empty($newWorkUser)) {
							return date("H:i", $time) . "成员【" . $WorkUser->name . "】的客户【" . $WorkExternalContact->name . "】在公海池，被员工【" . $newWorkUser->name . "】认领走了\r\n\r\n";
						}
					}

					return date("H:i", $time) . '成员【' . $WorkUser->name . "】删除客户【" . $WorkExternalContact->name . "】\r\n\r\n";
				}
			}
			if ($time == false) {
				$time = date("Y-m-d H:i:s", time());
			} else {
				$time = date("Y-m-d H:i:s", $time);
			}
			$data       = [];
			$department = explode(",", $WorkUser->department);
			if (!empty($department)) {
				foreach ($department as $item) {
					$delFollowUser = WorkUserDelFollowUser::find()->alias("a")
						->leftJoin("{{%work_user}} as b", "a.user_id=b.id")
						->leftJoin("{{%work_follow_user}} as c", "c.corp_id = a.corp_id and c.user_id = a.user_id ")
						->where("FIND_IN_SET('$item',a.department)")
						->andWhere(["a.corp_id" => $corp_id, "a.open_status" => 1, "c.status" => 1])
						->andWhere("FIND_IN_SET('1',a.frequency)")
						->select("b.name,b.userid,a.agent")->asArray()->all();
					if (!empty($delFollowUser)) {
						$data = array_merge($data, $delFollowUser);
					}
				}
			}
			$delFollowUser = WorkUserDelFollowUser::find()->alias("a")
				->leftJoin("{{%work_user}} as b", "a.user_id=b.id")
				->leftJoin("{{%work_follow_user}} as c", "c.corp_id = a.corp_id and c.user_id = a.user_id ")
				->where("FIND_IN_SET('$user_id',a.inform_user)")
				->andWhere(["a.corp_id" => $corp_id, "a.open_status" => 1, "c.status" => 1])
				->andWhere("FIND_IN_SET('$frequency',a.frequency)")
				->select("b.name,b.userid,a.agent")->asArray()->all();
			if (!empty($delFollowUser)) {
				$data = array_merge($data, $delFollowUser);
			}
			$delFollowUser = WorkUserDelFollowUser::find()->alias("a")
				->leftJoin("{{%work_user}} as b", "a.user_id=b.id")
				->leftJoin("{{%work_follow_user}} as c", "c.corp_id = a.corp_id and c.user_id = a.user_id ")
				->where(["a.corp_id" => $corp_id, "a.open_status" => 1, "a.type" => 1, "c.status" => 1])
				->andWhere("FIND_IN_SET('$frequency',a.frequency)")->select("b.name,b.userid,a.agent")->asArray()->all();
			if (!empty($delFollowUser)) {
				$data = array_merge($data, $delFollowUser);
			}
			if (empty($data)) {
				return 1;
			}
			Yii::error($data, '$userids');
			$sendRule = [];
			foreach ($data as $datum) {
				if (empty($sendRule[$datum['agent']])) {
					$sendRule[$datum['agent']] = [];
				}

				array_push($sendRule[$datum['agent']], $datum['userid']);
			}

			if (!empty($sendRule)) {
				if ($del_type == 2) {
					$messageContent = '成员【' . $WorkUser->name . "】刚刚 删除客户【" . $WorkExternalContact->name . "】\r删除原因：客户已将员工删除";
				} else {
					$messageContent = '成员【' . $WorkUser->name . "】刚刚 删除客户【" . $WorkExternalContact->name . "】";
					$nowTime        = time();
					$claimUser      = PublicSeaClaimUser::find()->where(['corp_id' => $corp_id, 'old_user_id' => $user_id, 'external_userid' => $workExternalUserId, 'status' => 0])->andWhere(['between', 'add_time', $nowTime - 600, $nowTime + 600])->one();
					if (!empty($claimUser)) {
						$newWorkUser = WorkUser::findOne($claimUser->new_user_id);
						if (!empty($newWorkUser)) {
							$messageContent = "成员【" . $WorkUser->name . "】的客户【" . $WorkExternalContact->name . "】在公海池，被成员【" . $newWorkUser->name . "】认领走了\r\n\r\n";
						}
					}
				}
				foreach ($sendRule as $agentId => $sendUser) {
					try {
						self::SendAgentMessage($sendUser, $agentId, $messageContent, $corp_id, $res);
					} catch (\Exception $e) {
						Yii::error($e->getMessage(), __CLASS__ . '-' . __FUNCTION__ . ':sendAgentMessage');
					}
				}
			}

			return 1;
		}

		public static function SendAgentMessage ($toUser, $agentId, $messageContent, $corp_id, $res)
		{

			$workApi = WorkUtils::getAgentApi($corp_id, $agentId);

			$messageContent = [
				'content' => $messageContent,
			];
			$messageContent = TextMesssageContent::parseFromArray($messageContent);
			$agent          = WorkCorpAgent::findOne($agentId);
			$message        = [
				'touser'                   => $toUser,
				'agentid'                  => $agent->agentid,
				'messageContent'           => $messageContent,
				'toparty'                  => [],
				'totag'                    => [],
				'duplicate_check_interval' => 10,
			];
			$message        = Message::pareFromArray($message);
			try {
				$workApi->MessageSend($message, $invalidUserIdList, $invalidPartyIdList, $invalidTagIdList);
			} catch (\Exception $e) {
				Yii::error($e->getLine(), "symLine");
				Yii::error($e->getMessage(), "symMessage");
			}
		}
	}
