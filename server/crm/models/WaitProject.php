<?php

namespace app\models;

use app\components\InvalidDataException;
use app\components\InvalidParameterException;
use app\queue\WaitStartTaskJob;
use app\util\SUtils;
use Yii;

/**
 * This is the model class for table "{{%wait_project}}".
 *
 * @property int                 $id
 * @property int                 $corp_id        企业ID
 * @property int                 $agent_id       应用ID
 * @property string              $title          项目名称
 * @property string              $desc           项目描述
 * @property int                 $user_id        员工ID
 * @property int                 $level_id       项目优先级
 * @property int                 $finish_time    项目需要在多少天完成
 * @property int                 $old_days       上一次设置的项目完成天数
 * @property int                 $sort           排序
 * @property int                 $is_del         0未删除1已删除
 * @property int                 $create_time    创建时间
 *
 * @property WorkUser            $user
 * @property WaitLevel           $level
 * @property WorkCorp            $corp
 * @property WaitProjectRemind[] $waitProjectReminds
 * @property WaitTask[]          $waitTasks
 */
class WaitProject extends \yii\db\ActiveRecord
{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName ()
	{
		return '{{%wait_project}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules ()
	{
		return [
			[['corp_id', 'user_id', 'finish_time', 'is_del', 'create_time', 'level_id', 'sort', 'agent_id'], 'integer'],
			[['desc'], 'string'],
			[['title'], 'string', 'max' => 200],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkUser::className(), 'targetAttribute' => ['user_id' => 'id']],
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
			'corp_id'     => '企业ID',
			'agent_id'    => '应用ID',
			'title'       => '项目名称',
			'desc'        => '项目描述',
			'user_id'     => '员工ID',
			'level_id'    => '项目优先级',
			'finish_time' => '项目需要在多少天完成',
			'old_days'    => '上一次设置的项目完成天数',
			'sort'        => '排序',
			'is_del'      => '0未删除1已删除',
			'create_time' => '创建时间',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getLevel ()
	{
		return $this->hasOne(WaitLevel::className(), ['id' => 'level_id']);
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
	public function getCorp ()
	{
		return $this->hasOne(WorkCorp::className(), ['id' => 'corp_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getWaitProjectReminds ()
	{
		return $this->hasMany(WaitProjectRemind::className(), ['project_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getWaitTasks ()
	{
		return $this->hasMany(WaitTask::className(), ['project_id' => 'id']);
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

	public function dumpData ()
	{
		$remind   = WaitProjectRemind::getData($this->id);
		$workUser = WorkUser::findOne($this->user_id);
		$name     = '';
		if (!empty($workUser)) {
			$name = $workUser->name;
		}
		$departName = WorkDepartment::getDepartNameByUserId($workUser->department, $workUser->corp_id);
		$level      = WaitLevel::findOne($this->level_id);

		return [
			'id'          => $this->id,
			'title'       => $this->title,
			'level'       => $level->title,
			'desc'        => $this->desc,
			'sort'        => $this->sort,
			'is_del'      => empty($workUser) ? $workUser->is_del : 0,
			'is_external' => empty($workUser) ? $workUser->is_external : 0,
			'user_id'     => $this->user_id,
			'finish_time' => $this->finish_time,
			'depart_name' => $departName,
			'user'        => $name,
			'remind'      => $remind,
		];
	}

	/**
	 * 待办项目验证
	 * @param $data
	 *
	 * @return bool
	 *
	 * @throws InvalidDataException
	 * @throws InvalidParameterException
	 */
	public static function verify ($data)
	{
		$uid           = $data['uid'];
		$projectStatus = $data['project_status'];
		$projectLevel  = $data['project_level'];
		$project       = $data['project'];
		$task          = $data['task'];
		if (empty($task) || empty($uid)) {
			throw new InvalidParameterException('参数不正确');
		}
		if ((!empty($project) && !is_array($project)) || !is_array($task)) {
			throw new InvalidParameterException('格式不正确');
		}

//		foreach ($projectStatus as $status) {
//			if (empty($status['title'])) {
//				throw new InvalidParameterException('阶段名称不能为空');
//			} elseif (mb_strlen($status['title'], 'utf-8') > 20) {
//				throw new InvalidDataException('阶段名称不能超过20个字');
//			}
//			if (empty($status['color'])) {
//				throw new InvalidParameterException('颜色不能为空');
//			}
//			if (!empty($status['desc']) && mb_strlen($status['desc'], 'utf-8') > 200) {
//				throw new InvalidDataException('阶段描述不能超过200个字');
//			}
//			if (empty(intval($status['sort']))) {
//				throw new InvalidParameterException('排序不能为空');
//			}
//		}
//		if (SUtils::checkRepeatArray($projectStatus, 'title')) {
//			throw new InvalidParameterException('阶段名称存在重复');
//		}
//		if (SUtils::checkRepeatArray($projectStatus, 'sort')) {
//			throw new InvalidParameterException('阶段排序存在重复');
//		}
//		foreach ($projectLevel as $level) {
//			if (empty($level['title'])) {
//				throw new InvalidParameterException('优先级名称不能为空');
//			} elseif (mb_strlen($level['title'], 'utf-8') > 20) {
//				throw new InvalidDataException('优先级名称不能超过20个字');
//			}
//			if (empty($level['color'])) {
//				throw new InvalidParameterException('颜色不能为空');
//			}
//			if (!empty($level['desc']) && mb_strlen($level['desc'], 'utf-8') > 200) {
//				throw new InvalidDataException('优先级描述不能超过200个字');
//			}
//			if (empty(intval($level['sort']))) {
//				throw new InvalidParameterException('优先级排序不能为空');
//			}
//		}
//		if (SUtils::checkRepeatArray($projectLevel, 'title')) {
//			throw new InvalidParameterException('优先级名称存在重复');
//		}
//		if (SUtils::checkRepeatArray($projectLevel, 'sort')) {
//			throw new InvalidParameterException('优先级排序存在重复');
//		}

		$projectTitle = [];
		if(!empty($project)){
			foreach ($project as $pro) {
				if (empty($pro['title'])) {
					throw new InvalidParameterException('项目名称不能为空');
				} elseif (mb_strlen($pro['title'], 'utf-8') > 50) {
					throw new InvalidDataException('项目名称不能超过50个字');
				}
				if (empty($pro['level'])) {
					throw new InvalidParameterException('优先级名称不能为空');
				}
				if (!empty($pro['desc']) && mb_strlen($pro['desc'], 'utf-8') > 200) {
					throw new InvalidDataException('项目描述不能超过200个字');
				}
				if (empty(intval($pro['user_id']))) {
					throw new InvalidParameterException('项目处理人不能为空');
				}
				if (empty(intval($pro['finish_time']))) {
					throw new InvalidParameterException('项目完成时间必须大于0');
				}
				if (empty($pro['sort'])) {
					throw new InvalidParameterException('项目排序不能为空');
				}
				if (!empty($pro['remind'])) {
					$day1 = [];
					$day2 = [];
					foreach ($pro['remind'] as $remind) {
						if (empty(intval($remind['type']))) {
							throw new InvalidParameterException('请选择任务提醒类型');
						}
						if ($remind['type'] == 2 && empty($remind['days'])) {
							throw new InvalidParameterException('超时天数必须大于0');
						}
						if ($remind['type'] == 1 && empty($remind['days'])) {
							throw new InvalidParameterException('预计截止结束时间前天数必须大于0');
						}
						if ($remind['type'] == 1) {
							if ($remind['days'] > intval($pro['finish_time'])) {
								throw new InvalidParameterException('预计结束前提醒的天数不能大于项目完成时间的天数');
							}
						}
						if ($remind['type'] == 1) {
							array_push($day1, $remind['days']);
						}
						if ($remind['type'] == 2) {
							array_push($day2, $remind['days']);
						}
					}
					if (count($day1) != count(array_unique($day1))) {
						throw new InvalidParameterException('预计结束时间前提醒存在重复项');
					}
					if (count($day2) != count(array_unique($day2))) {
						throw new InvalidParameterException('项目超时提醒重复项');
					}
				}
				array_push($projectTitle,$pro['title']);
			}

			if (SUtils::checkRepeatArray($project, 'title')) {
				throw new InvalidParameterException('项目名称存在重复');
			}

		}

		foreach ($task as $ta) {
			if (empty($ta['follow_id'])) {
				throw new InvalidParameterException('请选择跟进状态');
			}
			if (!empty($ta['content'])) {
//				if (empty($ta['type'])) {
//					throw new InvalidParameterException('任务中缺少必要参数');
//				}
				if ($ta['type'] == 2) {
					if (empty($ta['way'])) {
						throw new InvalidParameterException('非所有项目中必须选一项');
					}
					if (in_array(1, $ta['way']) && empty($ta['num'])) {
						throw new InvalidParameterException('至少完成项数不能为空');
					}
					if (in_array(1, $ta['way']) && empty($ta['project_one'])) {
						throw new InvalidParameterException('缺少必要参数');
					}
					if (in_array(2, $ta['way']) && empty($ta['project_two'])) {
						throw new InvalidParameterException('缺少必要参数');
					}
				}
				$con = $ta['content'];
				if (empty($con['title'])) {
					throw new InvalidParameterException('项目名称不能为空');
				}
				if (empty($con['type'])) {
					throw new InvalidParameterException('任务中缺少必要参数');
				}
				if ($con['type'] == 3 && empty($con['days'])) {
					throw new InvalidParameterException('任务规则天数必须大于0');
				}
				if(!in_array($con['title'],$projectTitle)){
					throw new InvalidParameterException('任务中存在已删除的项目');
				}

//				if (SUtils::checkRepeatArray($ta['content'], 'title')) {
//					throw new InvalidParameterException('任务中项目存在重复');
//				}
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
	public static function verifyCommon ($data)
	{
		$uid           = $data['uid'];
		$projectStatus = $data['project_status'];
		$projectLevel  = $data['project_level'];
		if (empty($projectLevel) || empty($projectStatus) || empty($uid)) {
			throw new InvalidParameterException('参数不正确');
		}
		if (!is_array($projectLevel) || !is_array($projectStatus)) {
			throw new InvalidParameterException('格式不正确');
		}
		foreach ($projectStatus as $status) {
			if (empty($status['title'])) {
				throw new InvalidParameterException('状态名称不能为空');
			} elseif (mb_strlen($status['title'], 'utf-8') > 20) {
				throw new InvalidDataException('状态名称不能超过20个字');
			}
			if (empty($status['color'])) {
				throw new InvalidParameterException('颜色不能为空');
			}
			if (!empty($status['desc']) && mb_strlen($status['desc'], 'utf-8') > 200) {
				throw new InvalidDataException('状态描述不能超过200个字');
			}
			if (empty(intval($status['sort']))) {
				throw new InvalidParameterException('排序不能为空');
			}
		}
		if (SUtils::checkRepeatArray($projectStatus, 'title')) {
			throw new InvalidParameterException('阶段名称存在重复');
		}
//		if (SUtils::checkRepeatArray($projectStatus, 'sort')) {
//			throw new InvalidParameterException('阶段排序存在重复');
//		}
		foreach ($projectLevel as $level) {
			if (empty($level['title'])) {
				throw new InvalidParameterException('优先级名称不能为空');
			} elseif (mb_strlen($level['title'], 'utf-8') > 20) {
				throw new InvalidDataException('优先级名称不能超过20个字');
			}
			if (empty($level['color'])) {
				throw new InvalidParameterException('颜色不能为空');
			}
			if (!empty($level['desc']) && mb_strlen($level['desc'], 'utf-8') > 200) {
				throw new InvalidDataException('优先级描述不能超过200个字');
			}
			if (empty(intval($level['sort']))) {
				throw new InvalidParameterException('优先级排序不能为空');
			}
		}
		if (SUtils::checkRepeatArray($projectLevel, 'title')) {
			throw new InvalidParameterException('优先级名称存在重复');
		}
//		if (SUtils::checkRepeatArray($projectLevel, 'sort')) {
//			throw new InvalidParameterException('优先级排序存在重复');
//		}

		return true;
	}

	/**
	 * @param $data
	 *
	 * @return bool
	 *
	 * @throws InvalidDataException
	 * @throws \Throwable
	 */
	public static function add ($data)
	{
		$transaction = \Yii::$app->db->beginTransaction();
		try {
			$projectStatus = $data['project_status'];
			$projectLevel  = $data['project_level'];
			$project       = $data['project'];
			$task          = $data['task'];
			$uid           = $data['uid'];
			$corpId        = $data['corp_id'];
			$agentId       = $data['agent_id'];
			//添加应用
			WaitAgent::addGent($corpId, $agentId);
			$status = WaitStatus::findOne(['uid' => $uid, 'is_del' => 0]);
			if (empty($status)) {
				//添加项目执行状态
				WaitStatus::add($projectStatus, $uid);
				//添加项目优先级
				WaitLevel::add($projectLevel, $uid);
			}
			//创建待办项目
			static::addData($project, $uid, $corpId);
			//创建任务
			WaitTask::add($task, $corpId);
			$transaction->commit();

			return true;
		} catch (\Exception $e) {
			$transaction->rollBack();
			throw new InvalidDataException('提交失败' . $e->getMessage());
		}
	}

	/**
	 * @param $data
	 * @param $uid
	 * @param $corpId
	 *
	 * @return bool
	 *
	 * @throws InvalidDataException
	 * @throws \Throwable
	 * @throws \yii\db\StaleObjectException
	 */
	public static function addData ($data, $uid, $corpId)
	{
		if (!empty($data)) {
			$ids        = array_column($data, 'id');
			$dataResult = self::find()->where(['corp_id' => $corpId])->all();
			if (!empty($dataResult)) {
				/** @var WaitProject $res */
				foreach ($dataResult as $res) {
					if (!in_array($res->id, $ids)) {
						$res->is_del = 1;
						$res->save();
						self::delContent($res->id);
					}
				}
			}
			foreach ($data as $key => $val) {
				$projectId = static::addProject($val, $uid, $corpId, $key + 1);
				WaitProjectRemind::add($val['remind'], $projectId);
			}

		} else {
			$dataResult = self::find()->where(['corp_id' => $corpId])->all();
			if (!empty($dataResult)) {
				/** @var WaitProject $res */
				foreach ($dataResult as $res) {
					$res->is_del = 1;
					$res->save();
					self::delContent($res->id);
				}
			}
		}

		return true;
	}

	/**
	 * @param $projectId
	 *
	 * @return bool
	 * @throws \Throwable
	 * @throws \yii\db\StaleObjectException
	 */
	private static function delContent ($projectId)
	{
		//项目提醒
		$projectRemind = WaitProjectRemind::find()->where(['project_id' => $projectId])->all();
		if (!empty($projectRemind)) {
			/** @var WaitProjectRemind $remind */
			foreach ($projectRemind as $remind) {
				$remind->delete();
			}
		}
		//项目任务
		$taskId   = [];
		$waitTask = WaitTask::find()->where(['project_id' => $projectId])->all();
		if (!empty($waitTask)) {
			/** @var WaitTask $task */
			foreach ($waitTask as $task) {
				if (!empty($task->queue_id)) {
					\Yii::$app->queue->remove($task->queue_id);
					$task->queue_id = 0;
				}
				$task->is_del = 1;
				$task->save();
				array_push($taskId, $task->id);
			}
		}
		if (!empty($taskId)) {
			WaitUserRemind::deleteAll(['task_id'=>$taskId]);
			//任务被删 待办客户也要删除
			$jobId = \Yii::$app->queue->push(new WaitStartTaskJob([
				'taskId' => $taskId,
				'type'   => 2,
			]));
		}
		return true;
	}

	/**
	 * @param $data
	 * @param $uid
	 * @param $corpId
	 * @param $sort
	 *
	 * @return int
	 *
	 * @throws InvalidDataException
	 */
	public static function addProject ($data, $uid, $corpId, $sort)
	{
		$userId  = 0;
		$oldDays = $data['finish_time'];
		if (isset($data['id']) && $data['id'] > 0) {
			$waitProject = WaitProject::findOne(['id' => $data['id'], 'is_del' => 0]);
			$userId      = $waitProject->user_id;
			$oldDays     = $waitProject->old_days;
		} else {
			$waitProject = static::findOne(['corp_id' => $corpId, 'title' => $data['title'], 'is_del' => 0]);
			if (empty($waitProject)) {
				$waitProject              = new WaitProject();
				$waitProject->create_time = time();
			}
		}
		if (is_numeric($data['level']) && intval($data['level']) > 0) {
			$waitProject->level_id = $data['level'];
		} else {
			$waitLevel = WaitLevel::findOne(['uid' => $uid, 'title' => $data['level']]);
			if (!empty($waitLevel)) {
				$waitProject->level_id = $waitLevel->id;
			}
		}
		if (empty($waitProject->level_id)) {
			throw new InvalidDataException('参数不正确');
		}
		$waitProject->corp_id     = $corpId;
		$waitProject->title       = $data['title'];
		$waitProject->desc        = $data['desc'];
		$waitProject->user_id     = $data['user_id'];
		$waitProject->sort        = $sort;
		$waitProject->finish_time = $data['finish_time'];
		$waitProject->old_days    = $oldDays;
		$waitProject->is_del      = 0;
		if (!$waitProject->validate() || !$waitProject->save()) {
			throw new InvalidDataException('创建失败：' . SUtils::modelError($waitProject));
		}

		if ($userId > 0 && $userId != $data['user_id']) {
			//变更老客户的项目负责人 让他们回到最初始状态
			$customerTask = WaitCustomerTask::find()->alias('c')->leftJoin('{{%wait_task}} t', 'c.task_id=t.id');
			$customerTask = $customerTask->leftJoin('{{%wait_project}} p', 't.project_id=p.id');
			$customerTask = $customerTask->where(['p.id' => $data['id'], 'c.is_finish' => 0])->select('c.id,t.id as task_id')->all();
			if (!empty($customerTask)) {
				$customIdNew = [];
				$status      = WaitStatus::find()->where(['uid' => $uid, 'is_del' => 0])->orderBy(['sort' => SORT_ASC])->one();
				/** @var WaitCustomerTask $ta */
				foreach ($customerTask as $ta) {
					$ta->status     = $status->id;
					$ta->end_time   = 0;
					$ta->start_time = 0;
					$ta->queue_id   = 0;
					$ta->per        = '0';
					$ta->per_desc   = '';
					if (!empty($ta->queue_id)) {
						\Yii::$app->queue->remove($ta->queue_id);
					}
					$ta->queue_id = 0;
					$ta->save();
					WaitUserRemind::deleteAll(['custom_id' => $ta->id, 'task_id' => $ta->task_id]);
					array_push($customIdNew, $ta->id);
				}
				$workUser  = WorkUser::findOne($data['user_id']);
				$waitAgent = WaitAgent::findOne(['corp_id' => $workUser->corp_id]);
				$agentId   = $waitAgent->agent_id;
				WaitTask::sendData([$data['user_id']], $agentId, $customIdNew, $uid, 0);
			}
		}

		return $waitProject->id;
	}

	/**
	 * @param $corpId
	 *
	 * @return array
	 *
	 */
	public static function getData ($corpId)
	{
		$data      = [];
		$projectId = [];
		$project   = self::find()->where(['corp_id' => $corpId, 'is_del' => 0])->orderBy(['sort' => SORT_ASC])->all();
		if (!empty($project)) {
			/** @var WaitProject $pro */
			foreach ($project as $pro) {
				array_push($data, $pro->dumpData());
				array_push($projectId, $pro->id);
			}
		}

		return [
			'data'       => $data,
			'project_id' => $projectId,
		];
	}

}
