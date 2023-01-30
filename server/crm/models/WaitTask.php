<?php

namespace app\models;

use app\components\InvalidDataException;
use app\queue\WaitStartTaskJob;
use app\queue\WaitUserTaskJob;
use app\util\ShortUrlUtil;
use app\util\SUtils;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "{{%wait_task}}".
 *
 * @property int         $id
 * @property int         $project_id  待办项目ID
 * @property int         $follow_id   跟进状态ID
 * @property int         $type        1手动开启2自动开启3N天后开启
 * @property int         $days        多少天后启动
 * @property int         $queue_id    队列ID
 * @property string      $content     用于前端传值
 * @property int         $is_del      是否删除 0未删除 1已删除
 * @property int         $create_time 创建时间
 *
 * @property Follow      $follow
 * @property WaitProject $project
 */
class WaitTask extends \yii\db\ActiveRecord
{
	const IS_DEL = 1;

	/**
	 * {@inheritdoc}
	 */
	public static function tableName ()
	{
		return '{{%wait_task}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules ()
	{
		return [
			[['project_id', 'follow_id', 'type', 'days', 'is_del', 'queue_id', 'create_time'], 'integer'],
			[['follow_id'], 'exist', 'skipOnError' => true, 'targetClass' => Follow::className(), 'targetAttribute' => ['follow_id' => 'id']],
			[['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => WaitProject::className(), 'targetAttribute' => ['project_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels ()
	{
		return [
			'id'          => 'ID',
			'project_id'  => '待办项目ID',
			'follow_id'   => '跟进状态ID',
			'type'        => '1手动开启2自动开启3N天后开启',
			'days'        => '多少天后启动',
			'queue_id'    => '队列ID',
			'content'     => '用于前端传值',
			'is_del'      => '是否删除 0未删除 1已删除',
			'create_time' => '创建时间',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getFollow ()
	{
		return $this->hasOne(Follow::className(), ['id' => 'follow_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getProject ()
	{
		return $this->hasOne(WaitProject::className(), ['id' => 'project_id']);
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
		return [
			'project_id' => $this->project_id,
			'type'       => $this->type,
			'days'       => $this->days,
		];
	}

	/**
	 * @param $project
	 * @param $corpId
	 *
	 * @return array
	 *
	 */
	private static function getPro ($project, $corpId)
	{
		$projectOne = [];
		if (!empty($project)) {
			foreach ($project as $one) {
				$wait = WaitProject::findOne(['corp_id' => $corpId, 'title' => $one, 'is_del' => 0]);
				if (!empty($wait)) {
					array_push($projectOne, $wait->id);
				}
			}
		}

		return $projectOne;
	}

	/**
	 * @param $data
	 * @param $corpId
	 *
	 * @return bool
	 *
	 * @throws InvalidDataException
	 */
	public static function add ($data, $corpId)
	{
		$taskId = [];
		foreach ($data as $dt) {
			if (!empty($dt['content']) && !empty($dt['content']['task_id'])) {
				array_push($taskId, $dt['content']['task_id']);
			}
		}

		$ids       = array_unique(array_column($data, 'follow_id'));
		$waitTask  = WaitTask::find()->where(['is_del' => 0, 'follow_id' => $ids])->all();
		$taskIdNew = [];
		if (!empty($waitTask)) {
			/** @var WaitTask $ta */
			foreach ($waitTask as $ta) {
				if (!empty($taskId)) {
					if (!in_array($ta->id, $taskId)) {
						array_push($taskIdNew, $ta->id);
						$ta->is_del = 1;
						$ta->save();
					}
				} else {
					array_push($taskIdNew, $ta->id);
					$ta->is_del = 1;
					$ta->save();
				}
			}
		}
		\Yii::error($taskIdNew, '$taskIdNew');
		if (!empty($taskIdNew)) {
			WaitUserRemind::deleteAll(['task_id' => $taskIdNew]);
			//任务被删 待办客户也要删除
//			$jobId = \Yii::$app->queue->push(new WaitStartTaskJob([
//				'taskId' => $taskIdNew,
//				'type'   => 2,
//			]));

			$customerTask = WaitCustomerTask::find()->where(['task_id' => $taskIdNew, 'is_finish' => 0])->all();
			\Yii::error($customerTask, '$customerTask');
			if (!empty($customerTask)) {
				/** @var WaitCustomerTask $task */
				foreach ($customerTask as $task) {
					if (!empty($task->queue_id)) {
						\Yii::$app->queue->remove($task->queue_id);
					}
					$task->delete();
				}
			}

		}
		foreach ($data as $val) {
			$follow = Follow::findOne($val['follow_id']);

			if (!empty($follow)) {
				$follow->is_change   = $val['is_change'];
				$follow->way         = !empty($val['way']) ? Json::encode($val['way']) : '';
				$projectOne          = self::getPro($val['project_one'], $corpId);
				$projectTwo          = self::getPro($val['project_two'], $corpId);
				$follow->project_one = !empty($projectOne) ? Json::encode($projectOne) : '';
				$follow->project_two = !empty($projectTwo) ? Json::encode($projectTwo) : '';
				$follow->type        = $val['type'];
				$follow->num         = $val['num'];
				$follow->save();
			}
			$content = $val['content'];
			if (!empty($content)) {
				$con = $content;
				$dataCon   = $con;
				if (empty($con['project_id'])) {
					\Yii::error($con['title'],'title');
					$waitProject = WaitProject::findOne(['title' => $con['title'], 'corp_id' => $corpId, 'is_del' => 0]);
					\Yii::error($waitProject,'$waitProject');
					if (!empty($waitProject)) {
						$projectId = $waitProject->id;
					}else{
						throw new InvalidDataException('任务中存在已删除的项目');
					}
				} else {
					$projectId = $con['project_id'];
				}
				$dataCon['project_id'] = $projectId;
				if (empty($projectId)) {
					throw new InvalidDataException('参数错误');
				}
				\Yii::error($projectId,'$projectId');
				$waitTask = self::findOne(['follow_id' => $val['follow_id'], 'project_id' => $projectId]);
				if (empty($waitTask)) {
					$waitTask              = new WaitTask();
					$waitTask->create_time = time();
				}
				$waitTask->content    = Json::encode($dataCon);
				$waitTask->project_id = $projectId;
				$waitTask->follow_id  = $val['follow_id'];
				$waitTask->type       = $con['type'];
				$waitTask->days       = $con['days'];
				$waitTask->is_del     = 0;
				if (!$waitTask->validate() || !$waitTask->save()) {
					throw new InvalidDataException('创建失败：' . SUtils::modelError($waitTask));
				}

				$followId           = $waitTask->follow_id;
				$jobId              = \Yii::$app->queue->push(new WaitUserTaskJob([
					'taskId'   => $waitTask->id,
					'followId' => $followId,
					'type'     => 5,
					'corpId'   => $corpId,
					'daysNew'  => $con['days'],
				]));
				$waitTask->queue_id = $jobId;
				$waitTask->save();

			} else {
				$follow->is_change   = 0;
				$follow->way         = '';
				$follow->project_one = '';
				$follow->project_two = '';
				$follow->type        = 0;
				$follow->num         = '';
				$follow->save();
				//如果全部任务为空
				$dataResult = self::find()->where(['follow_id' => $val['follow_id']])->all();
				if (!empty($dataResult)) {
					$taskId = [];
					/** @var WaitTask $res */
					foreach ($dataResult as $res) {
						$res->is_del = 1;
						$res->save();
						array_push($taskId, $res->id);
					}
					if (!empty($taskId)) {
						WaitUserRemind::deleteAll(['task_id' => $taskId]);

						//任务被删 待办客户也要删除
//						$jobId = \Yii::$app->queue->push(new WaitStartTaskJob([
//							'taskId' => $taskId,
//							'type'   => 2,
//						]));
						$customerTask = WaitCustomerTask::find()->where(['task_id' => $taskIdNew, 'is_finish' => 0])->all();
						if (!empty($customerTask)) {
							/** @var WaitCustomerTask $task */
							foreach ($customerTask as $task) {
								if (!empty($task->queue_id)) {
									\Yii::$app->queue->remove($task->queue_id);
								}
								$task->delete();
							}
						}
					}
				}
			}

		}

		return true;
	}

	/**
	 * @param $uid
	 *
	 * @return array
	 *
	 */
	public static function getData ($uid)
	{
		$data   = [];
		$follow = Follow::find()->where(['uid' => $uid, 'status' => 1])->orderBy(['status' => SORT_DESC, 'sort' => SORT_ASC, 'id' => SORT_ASC])->all();
		if (!empty($follow)) {
			/** @var Follow $foll */
			foreach ($follow as $key => $foll) {
				$projectOne = !empty($foll->project_one) ? Json::decode($foll->project_one) : [];
				$projectTwo = !empty($foll->project_two) ? Json::decode($foll->project_two) : [];
				$nameOne    = [];
				$nameTwo    = [];
				if (!empty($projectOne)) {
					$one = WaitProject::find()->where(['id' => $projectOne])->select('title')->asArray()->all();
					if (!empty($one)) {
						$nameOne = array_column($one, 'title');
					}
				}
				if (!empty($projectTwo)) {
					$two = WaitProject::find()->where(['id' => $projectTwo])->select('title')->asArray()->all();
					if (!empty($two)) {
						$nameTwo = array_column($two, 'title');
					}
				}
				$content = self::getTask($foll->id);
				if (empty($content)) {
					$content                = [];
					$content['follow_id']   = $foll->id;
					$content['projectName'] = $foll->title;
					$content['type']        = $foll->type;
					$content['is_change']   = $foll->is_change;
					$content['way']         = !empty($foll->way) ? Json::decode($foll->way) : [];
					$content['num']         = $foll->num;
					$content['project_one'] = $nameOne;
					$content['project_two'] = $nameTwo;
					$content['rowSpan']     = 1;
					$con['task_id']         = 0;
					$con['title']           = '';
					$con['project_id']      = 0;
					$con['userInfo']        = '';
					$con['descTitle']       = [];
					$con['type']            = 0;
					$con['days']            = "";
					$con['date']            = 1;
					$content['content']     = $con;

					array_push($data, $content);
				} else {
					$waitCount = WaitTask::find()->where(['follow_id' => $foll->id, 'is_del' => 0])->count();
					foreach ($content as $key => $con) {
						$content                = [];
						$content['follow_id']   = $foll->id;
						$content['projectName'] = $foll->title;
						$content['type']        = $foll->type;
						$content['is_change']   = $foll->is_change;
						$content['way']         = !empty($foll->way) ? Json::decode($foll->way) : [];
						$content['num']         = $foll->num;
						$content['project_one'] = $nameOne;
						$content['project_two'] = $nameTwo;
						$content['content']     = $con;
						if ($key == 0) {
							$content['rowSpan'] = intval($waitCount);
						} else {
							$content['rowSpan'] = 0;
						}
						array_push($data, $content);
					}
				}

			}
		}

		return $data;
	}

	/**
	 * @param $followId
	 *
	 * @return array
	 *
	 */
	public static function getTask ($followId)
	{
		$data = [];
		$task = self::find()->where(['follow_id' => $followId, 'is_del' => 0])->all();
		if (!empty($task)) {
			/** @var WaitTask $val */
			foreach ($task as $val) {
				$content = !empty($val->content) ? Json::decode($val->content) : [];
				$content['task_id'] = $val->id;
				array_push($data, $content);
			}
		}

		return $data;
	}

	/**
	 * @param int $type 0非企微1企微
	 * @param     $id
	 *
	 * @return int
	 */
	public static function getTaskById ($type = 0, $id)
	{
		$flag       = 0;
		$customTask = WaitCustomerTask::find()->alias('wt')->leftJoin('{{%wait_task}} t', 'wt.task_id=t.id');
		if ($type == 0) {
			$customTask = $customTask->where(['t.is_del' => 0, 'wt.is_del' => 0, 'wt.sea_id' => $id, 'wt.type' => 1])->count();
		} else {
			$customTask = $customTask->where(['t.is_del' => 0, 'wt.is_del' => 0, 'wt.external_userid' => $id, 'wt.type' => 0])->count();
		}
		if ($customTask > 0) {
			$flag = 1;
		}

		return $flag;
	}

	/**
	 * 汇总数据
	 *
	 * @param $userId  array 员工ID
	 * @param $endTime int 截止时间传时间戳
	 * @param $agentId  应用ID
	 * @param $type    1时间段2每日9点3每月
	 *
	 * @return string
	 *
	 */
	public static function getAllData ($userId, $endTime, $agentId, $type = 2)
	{
		\Yii::error($userId, '$userId');
		\Yii::error($endTime, '$endTime');
		\Yii::error($type, '$type');
		$userIdNew = [];
		if (!empty($userId)) {
			foreach ($userId as $id) {
				if (!empty($id)) {
					array_push($userIdNew, $id);
				}
			}
		}
		$userId = $userIdNew;
		if (empty($userId)) {
			return '';
		}

		if ($type == 2 || $type == 3) {
			$endTime = $endTime - 1;
		}
		$customIdNew = [];
		if (!empty($userId)) {
			$waitProject = WaitProject::find()->where(['user_id' => $userId])->groupBy('user_id')->asArray()->all();
			if (!empty($waitProject)) {
				foreach ($waitProject as $pro) {
					array_push($customIdNew, $pro['user_id']);
				}
			}
		}

		$date = '';
		switch ($type) {
			case 1:
				$date = '截止当前' . date('Y-m-d H:i', $endTime);
				break;
			case 2:
				$date = '截止昨日（' . date('Y-m-d', $endTime) . '）';
				break;
			case 3:
				$firstDay = date('Y-m-d', strtotime(date('Y-m-01') . ' -1 month'));
				$lastDay  = date('Y-m-d', strtotime(date('Y-m-01') . ' -1 day'));
				$date     = '截止上月（' . $firstDay . '至' . $lastDay . '）';
				break;
		}

		$str           = '';
		$str           .= $date . '，员工整体服务待办情况：<br/>';
		$workUser      = WorkUser::findOne($userId[0]);
		$corpId        = $workUser->corp_id;
		$userCorp      = UserCorpRelation::findOne(['corp_id' => $workUser->corp_id]);
		$waitStatusAsc = WaitStatus::find()->where(['uid' => $userCorp->uid, 'is_del' => 0])->orderBy(['sort' => SORT_ASC])->asArray()->all();
		$flag          = false;
		if (!empty($waitStatusAsc)) {
			foreach ($waitStatusAsc as $key => $status) {
				$waitCount  = 0;
				$customTask = WaitCustomerTask::find()->alias('wt')->leftJoin('{{%wait_task}} t', 'wt.task_id=t.id');
				$customTask = $customTask->leftJoin('{{%wait_project}} p', 't.project_id=p.id');
				$customTask = $customTask->where(['p.is_del' => 0, 'wt.status' => $status['id'], 't.is_del' => 0, 'p.user_id' => $userId])->andWhere(['<=', 'wt.create_time', $endTime]);
				if ($type == 2 || $type == 3) {
					$cc         = 0;
					$customTask = $customTask->asArray()->all();
					if (!empty($customTask)) {
						$flag = true;
						foreach ($customTask as $task) {
							if ($task['open_time'] > 0 && $task['open_time'] > $endTime) {
								$waitCount++;
							} else {
								$cc++;
							}
						}
					}


				} else {
					$cc = $customTask->count();
					if (!empty($cc)) {
						$flag = true;
					}
				}
				if ($key == 0) {
					$cc += $waitCount;
				}
				$str .= $cc . '个' . $status['title'] . '，';
			}
		}
		if (!$flag) {
			return '';
		}

//		$customTask = WaitCustomerTask::find()->alias('wt')->leftJoin('{{%wait_task}} t', 'wt.task_id=t.id');
//		$customTask = $customTask->leftJoin('{{%wait_project}} p', 't.project_id=p.id');
//		$customTask = $customTask->where(['p.is_del' => 0, 't.is_del' => 0, 'p.user_id' => $userId])->andWhere(['<=', 'wt.create_time', $endTime]);
//		$customTask = $customTask->select('wt.task_id,wt.status,p.user_id,count(wt.id) cc');
//		$customTask = $customTask->groupBy('wt.status');
//		\Yii::error($customTask->createCommand()->getRawSql(), 'sqlWait');
//		$customTask = $customTask->asArray()->all();
//		$corpId     = 0;
//		if (!empty($customTask)) {
//			$date = '';
//			switch ($type) {
//				case 1:
//					$date = '截止当前' . date('Y-m-d H:i', $endTime);
//					break;
//				case 2:
//					$date = '截止昨日（' . date('Y-m-d', $endTime) . '）';
//					break;
//				case 3:
//					$firstDay = date('Y-m-d', strtotime(date('Y-m-01') . ' -1 month'));
//					$lastDay  = date('Y-m-d', strtotime(date('Y-m-01') . ' -1 day'));
//					$date     = '截止上月（' . $firstDay . '至' . $lastDay . '）';
//					break;
//			}
//			$workUser      = WorkUser::findOne($userId[0]);
//			$corpId        = $workUser->corp_id;
//			$userCorp      = UserCorpRelation::findOne(['corp_id' => $workUser->corp_id]);
//			$uid           = $userCorp->uid;
//			$waitStatusAsc = WaitStatus::find()->where(['uid' => $userCorp->uid, 'is_del' => 0])->orderBy(['sort' => SORT_ASC])->asArray()->all();
//			if (!empty($waitStatusAsc)) {
//				$str .= $date . '，员工整体服务待办情况：<br/>';
//				foreach ($waitStatusAsc as $status) {
//					$cc = 0;
//					foreach ($customTask as $task) {
//						if ($status['id'] == $task['status']) {
//							$cc = $task['cc'];
//						}
//					}
//					$str .= $cc . '个' . $status['title'] . '，';
//				}
//			}
//
//		}

		$str = trim($str, '，');
		$str = trim($str, '<br/>');
		\Yii::error($str, '$str');

		if (!empty($str)) {
			//$web_url = ' http://liyunli.tpscrm-mob.51lick.com';
			\Yii::error($customIdNew, '$customIdNew');
			$showProject   = $showStatus = 1;
			$waitTaskCount = WaitCustomerTask::find()->alias('c')->leftJoin('{{%wait_task}} t', 'c.task_id=t.id');
			$waitTaskCount = $waitTaskCount->leftJoin('{{%wait_project}} p', 'p.id=t.project_id');
			$waitTaskCount = $waitTaskCount->where(['t.is_del' => 0, 'p.is_del' => 0, 'p.user_id' => $customIdNew]);
			$waitTaskCount = $waitTaskCount->groupBy('c.task_id');
			\Yii::error($waitTaskCount->createCommand()->getRawSql(), 'sql456');
			$waitTaskCount = $waitTaskCount->count();
			\Yii::error($waitTaskCount, '$waitTaskCount');
			if ($waitTaskCount <= 1) {
				$showProject = 0;
			}
			\Yii::error($showProject, '$showProject');

			$waitStatusCount = WaitCustomerTask::find()->alias('c')->leftJoin('{{%wait_task}} t', 'c.task_id=t.id');
			$waitStatusCount = $waitStatusCount->leftJoin('{{%wait_project}} p', 'p.id=t.project_id');
			$waitStatusCount = $waitStatusCount->where(['t.is_del' => 0, 'p.is_del' => 0, 'p.user_id' => $customIdNew]);
			$waitStatusCount = $waitStatusCount->groupBy('c.status')->count();
			if ($waitStatusCount <= 1) {
				$showStatus = 0;
			}

			$corpInfo  = WorkCorp::findOne($corpId);
			$web_url   = \Yii::$app->params['web_url'];
			$customId  = implode(',', $customIdNew);
			$corpAgent = WorkCorpAgent::findOne(['is_del' => WorkCorpAgent::AGENT_NO_DEL, 'close' => WorkCorpAgent::AGENT_NOT_CLOSE, 'agent_type' => WorkCorpAgent::CUSTOM_AGENT, 'id' => $agentId]);
			$url       = WaitCustomerTask::H5_URL . '?corp_id=' . $corpAgent->corp_id . '&corpid=' . $corpInfo->corpid . '&agent_id=' . $agentId . '&show_project=' . $showProject . '&show_status=' . $showStatus . '&custom_id=-1&user_ids=' . $customId;
			$url       = ShortUrlUtil::setShortUrl($url);
			$shortUrl  = $web_url . '/h5/I/' . $url;
			\Yii::error($url, '$url');
			$str = $str . '，<a href="' . $shortUrl . '">查看详情</a>';
		}

		return $str;
	}

	/**
	 * @param       $followId
	 * @param       $type
	 * @param       $corpId
	 * @param array $followUserId
	 *
	 * @return bool
	 *
	 */
	public static function publicTask ($followId, $type, $corpId, $followUserId = [])
	{
		$waitTask = WaitTask::find()->alias('w')->leftJoin('{{%wait_project}} p', 'w.project_id=p.id')->where(['w.follow_id' => $followId, 'p.is_del' => 0, 'w.is_del' => 0])->all();
		if (!empty($waitTask)) {
			$data = [
				'followId' => $followId,
				'type'     => $type,
				'corpId'   => $corpId,
				'daysNew'  => 0,
			];
			if (!empty($followUserId)) {
				$data['followUserId'] = $followUserId;
			}
			$jobId = \Yii::$app->queue->push(new WaitUserTaskJob($data));

			return true;
		}
	}

	/**
	 * @param     $userId
	 * @param     $agentId
	 * @param     $customIdNew
	 * @param     $uid
	 * @param int $taskId
	 *
	 * @return bool
	 *
	 * @throws InvalidDataException
	 * @throws \ParameterError
	 * @throws \QyApiError
	 * @throws \yii\base\InvalidConfigException
	 */
	public static function sendData ($userId, $agentId, $customIdNew, $uid, $taskId = 0)
	{
		if (!empty($userId) && !empty($agentId)) {
			$webUrl = \Yii::$app->params['web_url'];
			foreach ($userId as $uId) {
				$name     = '';
				$workUser = WorkUser::findOne($uId);
				if (!empty($workUser)) {
					$name = $workUser->name;
				}
				$showProject = 1;//显示项目
				$showStatus  = 1;//显示状态

				$waitTaskCount = WaitCustomerTask::find()->alias('c')->leftJoin('{{%wait_task}} t', 'c.task_id=t.id');
				$waitTaskCount = $waitTaskCount->leftJoin('{{%wait_project}} p', 'p.id=t.project_id');
				$waitTaskCount = $waitTaskCount->where(['t.is_del' => 0, 'p.is_del' => 0, 'c.id' => $customIdNew, 'p.user_id' => $workUser->id]);
				$waitTaskCount = $waitTaskCount->groupBy('c.task_id')->count();
				if ($waitTaskCount <= 1) {
					$showProject = 0;
				}

				$waitStatusCount = WaitCustomerTask::find()->alias('c')->leftJoin('{{%wait_task}} t', 'c.task_id=t.id');
				$waitStatusCount = $waitStatusCount->leftJoin('{{%wait_project}} p', 'p.id=t.project_id');
				$waitStatusCount = $waitStatusCount->where(['t.is_del' => 0, 'p.is_del' => 0, 'c.id' => $customIdNew, 'p.user_id' => $workUser->id]);
				$waitStatusCount = $waitStatusCount->groupBy('c.status')->count();
				if ($waitStatusCount <= 1) {
					$showStatus = 0;
				}

				$customTask = WaitCustomerTask::find()->alias('c')->leftJoin('{{%wait_task}} t', 'c.task_id=t.id');
				$customTask = $customTask->leftJoin('{{%wait_project}} p', 'p.id=t.project_id');
				$customTask = $customTask->where(['t.is_del' => 0, 'p.is_del' => 0, 'c.id' => $customIdNew, 'p.user_id' => $workUser->id]);
				if (!empty($taskId)) {
					$customTask  = $customTask->andWhere(['t.id' => $taskId]);
					$showProject = 0;
					$showStatus  = 0;
				}
				$customTask = $customTask->select('c.id,c.status,t.type,t.days,p.title')->asArray()->all();
				\Yii::error($customTask, '$customTask');
				if (!empty($customTask)) {
					$count        = count($customTask);
					$projectTitle = $customTask[0]['title'];
					$status       = $customTask[0]['status'];
					$type         = $customTask[0]['type'];
					$days         = $customTask[0]['days'];
					if (!empty($taskId)) {
						$customIdNew = [$customTask[0]['id']];
					}
					$con        = '';
					$waitStatus = WaitStatus::findOne(['id' => $status, 'is_del' => 0]);
					$title      = $waitStatus->title;
					switch ($type) {
						case 1:
							$con .= '有' . $count . '个' . $title . '任务，请尽快完成<br/>';
							break;
						case 2:
							$con .= '有' . $count . '个' . $title . '的任务，请尽快完成<br/>';
							break;
						case 3:
							$waitStatus = WaitStatus::find()->where(['uid' => $uid, 'is_del' => 0])->orderBy(['sort' => SORT_ASC])->asArray()->all();
							$title1     = $waitStatus[1]['title'];
							$con        .= '有' . $count . '个待办任务将在' . $days . '天后自动进入' . $title1 . '阶段';
							break;
					}
					if (!empty($con)) {
						$content = $name . '，' . $projectTitle . '，您当前' . $con;
						$content = trim($content, "<br/>");

						$corpAgent = WorkCorpAgent::findOne(['is_del' => WorkCorpAgent::AGENT_NO_DEL, 'close' => WorkCorpAgent::AGENT_NOT_CLOSE, 'agent_type' => WorkCorpAgent::CUSTOM_AGENT, 'id' => $agentId]);
						$corpInfo  = WorkCorp::findOne($corpAgent->corp_id);
						$customId = implode(',', $customIdNew);
						$url      = WaitCustomerTask::H5_URL . '?corp_id=' . $corpAgent->corp_id . '&corpid=' . $corpInfo->corpid . '&agent_id=' . $agentId . '&show_project=' . $showProject . '&show_status=' . $showStatus . '&custom_id=' . $customId . '&user_ids=' . $uId;
						$url      = ShortUrlUtil::setShortUrl($url);
						$shortUrl = $webUrl . '/h5/I/' . $url;
						\Yii::error($url, '$url');
						$content = $content . '，<a href="' . $shortUrl . '">查看详情</a>';
						\Yii::error($content, '$content');
						PublicSeaCustomer::messageSend([$workUser->userid], $agentId, $content, $corpInfo);

					}

				}

			}
		}

		return true;
	}


}
