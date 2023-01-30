<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\components\InvalidParameterException;
	use app\util\DateUtil;
	use app\util\SUtils;
	use Yii;
	use yii\helpers\Json;

	/**
	 * This is the model class for table "{{%work_group_clock_activity}}".
	 *
	 * @property int                  $id
	 * @property int                  $corp_id     企业微信ID
	 * @property int                  $agent_id    应用ID
	 * @property int                  $type        类型 1永久有效 2 固定区间
	 * @property string               $title       活动名称
	 * @property int                  $start_time  开始时间
	 * @property int                  $end_time    结束时间
	 * @property string               $rule        活动规则
	 * @property int                  $choose_type 打卡类型：1连续打卡 2累计打卡
	 * @property int                  $status      活动状态：0未开始1进行中2时间结束3手动结束
	 * @property int                  $is_del      0未删除 1已删除
	 * @property int                  $update_time 更新时间
	 * @property int                  $create_time 创建时间
	 * @property int                  $short_url   短连接
	 * @property int                  $url         原始连接
	 *
	 * @property WorkCorpAgent        $agent
	 * @property WorkCorp             $corp
	 * @property WorkGroupClockTask[] $workGroupClockTasks
	 */
	class WorkGroupClockActivity extends \yii\db\ActiveRecord
	{
		const H5_URL = '/h5/pages/group/clockIn';
		const NAME = 'punch';

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_group_clock_activity}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_id', 'agent_id', 'type', 'start_time', 'end_time', 'choose_type', 'status', 'is_del', 'update_time', 'create_time'], 'integer'],
				[['title'], 'string', 'max' => 50],
				[['rule'], 'string', 'max' => 255],
				[['agent_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorpAgent::className(), 'targetAttribute' => ['agent_id' => 'id']],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => 'ID',
				'corp_id'     => '企业微信ID',
				'agent_id'    => '应用ID',
				'type'        => '类型 1永久有效 2 固定区间',
				'title'       => '活动名称',
				'start_time'  => '开始时间',
				'end_time'    => '结束时间',
				'rule'        => '活动规则',
				'choose_type' => '打卡类型：1连续打卡 2累计打卡',
				'status'      => '活动状态：0未开始1进行中2时间结束3手动结束',
				'is_del'      => '0未删除 1已删除',
				'update_time' => '更新时间',
				'create_time' => '创建时间',
				'short_url'   => '短连接',
				'url'         => '原始连接',
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
		public function getAgent ()
		{
			return $this->hasOne(WorkCorpAgent::className(), ['id' => 'agent_id']);
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
		public function getWorkGroupClockTasks ()
		{
			return $this->hasMany(WorkGroupClockTask::className(), ['activity_id' => 'id']);
		}

		/**
		 * @param int $flag
		 *
		 * @return array
		 *
		 */
		public function dumpData ($flag = 0, $otherData = [])
		{
			$result               = [
				'key'         => $this->id,
				'id'          => $this->id,
				'title'       => $this->title,
				'corp_id'     => $this->corp_id,
				'agent_id'    => $this->agent_id,
				'type'        => $this->type,
				'start_time'  => $this->start_time,
				'end_time'    => $this->end_time,
				'rule'        => $this->rule,
				'choose_type' => $this->choose_type,
			];
			$corp                 = WorkCorp::findOne($this->corp_id);
			$result['corp_id']    = $corp->corpid;
			$result['start_time'] = date('Y-m-d H:i', $this->start_time);
			$result['end_time']   = date('Y-m-d H:i', $this->end_time);
			$task                 = [];
			$clockTask            = WorkGroupClockTask::find()->where(['activity_id' => $this->id]);
			if (!empty($otherData['is_open'])) {
				$clockTask = $clockTask->andWhere(['is_open' => 1]);
			}
			$clockTask = $clockTask->asArray()->all();
			if (!empty($clockTask)) {
				foreach ($clockTask as $key => $ta) {
					$task[$key]['id']   = $ta['id'];
					$task[$key]['days'] = intval($ta['days']);
					$task[$key]['type'] = $ta['type'];
					if ($ta['type'] == 1) {
						$moneyAmount = '0.3';//默认值
					} else {
						$moneyAmount = $ta['money_amount'];
					}
					$task[$key]['is_open']      = (boolean) $ta['is_open'];
					$task[$key]['reward_name']  = $ta['reward_name'];
					$task[$key]['money_amount'] = $moneyAmount;
					$task[$key]['reward_type']  = $ta['reward_type'];
					$user_keys = Json::decode($ta['user_key'], true);
					foreach ($user_keys as &$value) {
						if(isset($value["user_key"])){
							$value["key"] = isset($value["user_key"]) ? $value["user_key"] :'';
						}
						if(isset($key["name"])){
							$value["title"] = isset($value["user_key"]) ? $value["user_key"] : '';
						}
						if(!isset($key["scopedSlots"])){
							$value['scopedSlots'] = ['title' => 'custom'];
						}
					}
					$task[$key]['user_keys'] = $user_keys;
				}
			}
			$result['task'] = $task;
			if ($flag == 1) {
				$num           = WorkGroupClockJoin::find()->where(['activity_id' => $this->id])->count();//参与打卡人数
				$result['num'] = $num;
			}
			$webUrl        = \Yii::$app->params['web_url'];
			$result['url'] = $webUrl . '/h5/I/' . $this->short_url . '?type=2';
			$status        = $this->status;
			if ($this->status == 2 || $this->status == 3) {
				$status = 2;
			}
			$result['status'] = $status;

			return $result;
		}

		/**
		 * @param $data
		 *
		 * @return bool
		 * @throws InvalidParameterException
		 */
		public static function verifyData ($data)
		{
			if (empty($data['agent_id'])) {
				throw new InvalidParameterException('请选择应用！');
			}
			if (empty($data['type']) || !in_array($data['type'], [1, 2])) {
				throw new InvalidParameterException('请选择活动类型！');
			}
			if ($data['type'] == 2) {
				if (empty($data['start_time'])) {
					throw new InvalidParameterException('活动开始时间不能为空！');
				}
				if (empty($data['end_time'])) {
					throw new InvalidParameterException('活动结束时间不能为空！');
				}
			}
			if (empty($data['title'])) {
				throw new InvalidParameterException('请填写活动名称！');
			}
			if (!empty($data['title']) && mb_strlen($data['title'], 'utf-8') > 20) {
				throw new InvalidParameterException('活动名称不能超过20个字！');
			}
			if (empty($data['id'])) {
				$clockAct = WorkGroupClockActivity::findOne(['corp_id' => $data['corp_id'], 'title' => $data['title'], 'is_del' => 0]);
				if (!empty($clockAct)) {
					throw new InvalidParameterException('活动名称已存在！');
				}
			} else {
				$clockAct = WorkGroupClockActivity::find()->where(['corp_id' => $data['corp_id'], 'title' => $data['title'], 'is_del' => 0])->andWhere(['!=', 'id', $data['id']])->one();
				if (!empty($clockAct)) {
					throw new InvalidParameterException('活动名称已存在！');
				}
			}
			if (!empty($data['rule']) && mb_strlen($data['rule'], 'utf-8') > 200) {
				throw new InvalidParameterException('活动规则不能超过200个字！');
			}
			if (empty($data['choose_type']) || !in_array($data['choose_type'], [1, 2])) {
				throw new InvalidParameterException('请选择打卡类型！');
			}
			if (empty($data['task'])) {
				throw new InvalidParameterException('请设置打卡任务！');
			}
			if (!empty($data['task'])) {
				$task = $data['task'];
				if (SUtils::checkRepeatArray($task, 'days')) {
					throw new InvalidParameterException('打卡天数存在');
				}
				foreach ($task as $ta) {
					if (empty($ta['days'])) {
						throw new InvalidParameterException('打卡天数不能为空！');
					}
					if ($data['type'] == 2) {
						$startTime = explode(' ', $data['start_time']);
						$endTime   = explode(' ', $data['end_time']);
						$range     = DateUtil::getDateFromRange($startTime[0], $endTime[0]);
						$cardCount = count($range);
						if ($ta['days'] > $cardCount) {
							throw new InvalidParameterException('打卡天数不能超过' . $cardCount . '天！');
						}
					}
					if (empty($ta['type']) || !in_array($ta['type'], [1, 2])) {
						throw new InvalidParameterException('请选择奖品类型！');
					}
					if ($ta['type'] == 1) {
						if (empty($ta['reward_name'])) {
							throw new InvalidParameterException('请填写奖品名称！');
						}
						if (!empty($data['reward_name']) && mb_strlen($data['reward_name'], 'utf-8') > 20) {
							throw new InvalidParameterException('奖品名称不能超过20个字！');
						}
						if (empty($ta['reward_type']) || !in_array($ta['reward_type'], [1, 2])) {
							throw new InvalidParameterException('请选择领奖方式！');
						}
						if ($ta['reward_type'] == 1 && empty($ta['user_keys'])) {
							throw new InvalidParameterException('请选择员工！');
						}
					} else {
						if (empty(floatval($ta['money_amount']))) {
							throw new InvalidParameterException('请填写红包金额！');
						}
						if ($ta['money_amount'] < 0.3 || $ta['money_amount'] > 5000) {
							throw new InvalidParameterException('红包金额必须在0.3到5000元之间！');
						}
						if (empty($ta['user_keys'])) {
							throw new InvalidParameterException('请选择员工！');
						}
					}
				}
			}

			return true;
		}

		/**
		 * @param $data
		 *
		 * @return bool
		 *
		 * @throws InvalidDataException
		 * @throws InvalidParameterException
		 */
		public static function add ($data)
		{
			self::verifyData($data);
			$transaction = \Yii::$app->mdb->beginTransaction();
			try {
				if (empty($data['id'])) {
					$clockAct              = new WorkGroupClockActivity();
					$clockAct->create_time = time();
				} else {
					$clockAct              = self::findOne($data['id']);
					$clockAct->update_time = time();
				}
				$clockAct->corp_id  = $data['corp_id'];
				$clockAct->agent_id = $data['agent_id'];
				$clockAct->type     = $data['type'];
				$clockAct->title    = $data['title'];
				if ($data['type'] == 2) {
					$clockAct->start_time = strtotime($data['start_time']);
					$clockAct->end_time   = strtotime($data['end_time']);
				} else {
					$clockAct->start_time = 0;
					$clockAct->end_time   = 0;
				}
				$clockAct->rule        = $data['rule'];
				$clockAct->choose_type = $data['choose_type'];
				if ($clockAct->dirtyAttributes) {
					if (!$clockAct->validate() || !$clockAct->save()) {
						throw new InvalidDataException(SUtils::modelError($clockAct));
					}
				}

				if (empty($data['id']) || (!empty($clockAct) && ($clockAct->status == 0))) {
					//获取链接地址
					$corpAgent = WorkCorpAgent::findOne($clockAct->agent_id);
					$workCorp  = WorkCorp::findOne($clockAct->corp_id);
					$state     = $clockAct->id;
					if (!empty($corpAgent) && $corpAgent->agent_type == WorkCorpAgent::AUTH_AGENT) {
						$baseUrl = static::H5_URL . '?suite_id=' . $corpAgent->suite->suite_id . '&corp_id=' . $corpAgent->corp_id . '&corpid=' . $workCorp->corpid . '&agent_id=' . $clockAct->agent_id . '&assist=' . $state;
					} else {
						$baseUrl = static::H5_URL . '?corp_id=' . $corpAgent->corp_id . '&corpid=' . $workCorp->corpid . '&agent_id=' . $clockAct->agent_id . '&assist=' . $state;
					}
					$clockAct->short_url = self::setShortUrl($baseUrl);
					$clockAct->url       = $baseUrl;
					$clockAct->update();
				}

				WorkGroupClockTask::add($data['task'], $clockAct->id, $data['corp_id']);
				$transaction->commit();
			} catch (\Exception $e) {
				$transaction->rollBack();
				throw new InvalidDataException($e->getMessage());
			}

			return true;
		}

		/**
		 * @param $baseUrl  链接地址
		 * @param $ref      是否打乱
		 */
		public static function setShortUrl ($baseUrl, $ref = false)
		{
			$url  = md5($baseUrl);
			$url1 = strtoupper(substr($url, 0, 5));
			$url2 = strtolower(substr($url, 5, 5));
			$url  = $url1 . $url2;
			if ($ref) {
				$url = str_shuffle($url);
			}
			$short = self::findOne(["short_url" => $url]);
			if (!empty($short)) {
				if ($short->url != $baseUrl) {
					$url = self::setShortUrl($baseUrl, true);
				}
			}

			return $url;
		}
	}
