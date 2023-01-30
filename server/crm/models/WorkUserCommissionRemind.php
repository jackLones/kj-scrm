<?php

	namespace app\models;

	use app\queue\UserRemindSendJob;
	use app\util\WorkUtils;
	use dovechen\yii2\weWork\src\dataStructure\Message;
	use dovechen\yii2\weWork\src\dataStructure\TextMesssageContent;
	use Yii;

	/**
	 * This is the model class for table "{{%work_user_commission_remind}}".
	 *
	 * @property int           $id
	 * @property int           $corp_id         企业应用id
	 * @property int           $agent           1全部
	 * @property int           $type            1全部2部门3员工
	 * @property int           $user_id         员工id
	 * @property string        $department      部门id
	 * @property string        $inform_user     可看员工删除被通知人
	 * @property string        $inform_user_key 可看员工删除被通知人old
	 * @property int           $open_status     状态
	 * @property string        $frequency       频率1每天分时段推送2每天早上9点汇总,3当月第一天推送上一月汇总
	 * @property int           $create_time
	 * @property int           $update_time
	 *
	 * @property WorkCorpAgent $agent0
	 * @property WorkCorp      $corp
	 * @property WorkUser      $user
	 */
	class WorkUserCommissionRemind extends \yii\db\ActiveRecord
	{
		const TIME_SELECT = 1;
		const TIME_DAY = 2;
		const TIME_MONTH = 3;

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_user_commission_remind}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_id', 'agent', 'type', 'user_id', 'open_status', 'create_time', 'update_time'], 'integer'],
				[['department', 'inform_user', 'frequency', 'inform_user_key'], 'string'],
				[['agent'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorpAgent::className(), 'targetAttribute' => ['agent' => 'id']],
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
				'agent'           => Yii::t('app', '1全部'),
				'type'            => Yii::t('app', '1全部2部门3员工'),
				'user_id'         => Yii::t('app', '员工id'),
				'department'      => Yii::t('app', '部门id'),
				'inform_user'     => Yii::t('app', '可看员工删除被通知人'),
				'inform_user_key' => Yii::t('app', '可看员工删除被通知人old'),
				'open_status'     => Yii::t('app', '状态'),
				'frequency'       => Yii::t('app', '频率1每天分时段推送2每天早上9点汇总,3当月第一天推送上一月汇总'),
				'create_time'     => Yii::t('app', 'Create Time'),
				'update_time'     => Yii::t('app', 'Update Time'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getAgent0 ()
		{
			return $this->hasOne(WorkCorpAgent::className(), ['id' => 'agent']);
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

		public static function getWorkUserDelLists ($department, $inform_user, $corp_id, $type)
		{
			if ($type == 1) {
				$WorkUser = WorkUser::find()
					->andWhere(["corp_id" => $corp_id, "status" => 1])
					->select("id")->asArray()->all();

				return array_column($WorkUser, "id");
			}
			if (!empty($department)) {
				$department = WorkDepartment::getDepartmentChildren($department, $corp_id);
				foreach ($department as $item) {
					$WorkUser = WorkUser::find()
						->where("FIND_IN_SET('$item',department)")
						->andWhere(["corp_id" => $corp_id, "status" => 1])
						->select("id")->asArray()->all();
					if (!empty($WorkUser)) {
						$inform_user = array_merge($inform_user, array_column($WorkUser, "id"));
					}
				}
			}
			$user_ids = array_merge($inform_user, $inform_user);

			return array_unique($user_ids);
		}

		public static function CreateZeroData ()
		{
			$result     = self::find()->where(["open_status" => 1])
				->andWhere("FIND_IN_SET('1',frequency)")
				->select("id")->asArray()->all();
			$ids        = array_column($result, "id");
			$remindData = WorkUserCommissionRemindTime::find()->where(["in", "remind_id", $ids])->asArray()->all();
			if (empty($remindData)) {
				return;
			}
			foreach ($remindData as $time) {
				if ($time["time"] == '00:00') {
					\Yii::$app->work->push(new UserRemindSendJob([
						'remindId' => $time["remind_id"],
						'time'     => $time["time"],
					]));
					continue;
				}
				$sendTime = strtotime(date('Y-m-d', time()) . ' ' . $time["time"]);
				$second   = $sendTime - time();
				if ($second < 0) {
					continue;
				}
				$jobId = \Yii::$app->work->delay($second)->push(new UserRemindSendJob([
					'remindId' => $time["remind_id"],
					'time'     => $time["time"],
				]));
				Yii::$app->cache->set($time["remind_id"] . $time["time"] . "remind-user", $jobId, $second);
			}
		}

		public static function sendMessage ($remindId = '', $time = '', $frequency = 1, $timeType = 1)
		{
			/** @var self $result */
			if (empty($remindId)) {
				$result = self::find()->alias("a")
					->leftJoin("{{%work_user}} as b", "a.user_id = b.id")
					->where(["a.open_status" => 1])
					->andWhere("FIND_IN_SET('$frequency',a.frequency)")
					->select("a.inform_user,a.agent,b.userid,a.corp_id,a.department,a.type")
					->asArray()->all();
			} else {
				$result = self::find()->alias("a")
					->leftJoin("{{%work_user}} as b", "a.user_id = b.id")
					->where(["a.id" => $remindId, "a.open_status" => 1])
					->andWhere("FIND_IN_SET('$frequency',a.frequency)")
					->select("a.inform_user,a.agent,b.userid,a.corp_id,a.department,a.type")
					->asArray()->all();
			}
			Yii::error($result, '$result');
			if (empty($result)) {
				return;
			}
			foreach ($result as $value) {
				$inform      = explode(",", $value["inform_user"]);
				$department  = explode(",", $value["department"]);
				$inform_user = WorkUserCommissionRemind::getWorkUserDelLists(empty($department) ? [] : $department, empty($value["inform_user"]) ? [] : $inform, $value["corp_id"], $value["type"]);
				if (empty($remindId)) {
					$str = WaitTask::getAllData($inform_user, $time, $value["agent"]);
				} else {
					$str = WaitTask::getAllData($inform_user, strtotime(date("Y-m-d", time()) . " " . $time), $value["agent"], $timeType);
				}
				Yii::error($str, '$str');
				if (!empty($str)) {
					self::SendAgentMessage([$value["userid"]], $value["agent"], $str, $value["corp_id"]);
				}
			}

		}

		public static function SendAgentMessage ($toUser, $agentId, $messageContent, $corp_id)
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

		public static function repeatConstructData (&$data, $user_id)
		{
			foreach ($data as &$item) {
				if (in_array($item["id"], $user_id)) {
					$item["disabled"] = true;
				}
				if (isset($item["children"])) {
					self::repeatConstructData($item["children"], $user_id);
				}
			}
		}
	}
