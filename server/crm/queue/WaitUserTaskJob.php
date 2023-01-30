<?php

	namespace app\queue;

	use app\models\Follow;
	use app\models\PublicSeaContactFollowUser;
	use app\models\PublicSeaCustomer;
	use app\models\UserCorpRelation;
	use app\models\WaitAgent;
	use app\models\WaitCustomerTask;
	use app\models\WaitStatus;
	use app\models\WaitTask;
	use app\models\WaitUserRemind;
	use app\models\WorkCorp;
	use app\models\WorkCorpAgent;
	use app\models\WorkExternalContactFollowUser;
	use app\models\WorkUser;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;

	class WaitUserTaskJob extends BaseObject implements JobInterface
	{
		public $taskId;
		public $followId;
		public $followUserId;
		public $type;
		public $corpId;
		public $daysNew;

		public function execute ($queue)
		{
			try {
				$customIdNew = []; //判断是否发应用消息
				\Yii::error($this->type, 'taskType');
				\Yii::error($this->followId, 'followId');
				switch ($this->type) {
					case 1:
						//企微客户主动进来时
						$waitTaskAll = WaitTask::find()->alias('w')->leftJoin('{{%wait_project}} p', 'w.project_id=p.id')->where(['w.follow_id' => $this->followId, 'p.is_del' => 0, 'w.is_del' => 0])->select('w.id,w.days')->asArray()->all();
						if (!empty($waitTaskAll)) {
							foreach ($waitTaskAll as $all) {
								$waitTask          = WaitTask::findOne($all['id']);
								$contactFollowUser = WorkExternalContactFollowUser::find()->alias('f')->leftJoin('{{%work_external_contact}} c', 'c.id=f.external_userid');
								$contactFollowUser = $contactFollowUser->where(['c.corp_id' => $this->corpId, 'f.del_type' => WorkExternalContactFollowUser::WORK_CON_EX, 'f.follow_id' => $this->followId]);
								$contactFollowUser = $contactFollowUser->groupBy('f.external_userid')->all();
								$customId          = [];
								$finishTime        = [];
								$cId               = [];
								//企微客户
								if (!empty($contactFollowUser) && !empty($waitTask)) {
									/** @var WorkExternalContactFollowUser $followUser */
									foreach ($contactFollowUser as $followUser) {
										$this->add($all['id'], $waitTask, 0, $finishTime, $customId, $cId, $followUser->external_userid,null, $customIdNew);
									}

								}
								\Yii::error($customId, '$customId1');
								\Yii::error($finishTime, '$finishTime1');
								\Yii::error($cId, '$cId1');
								\Yii::error($customIdNew, '$customIdNew1');
								if (!empty($customId)) {
									$this->addCustom($customId, $all['id']);
								}

								WaitCustomerTask::sendMind ($cId);
							}
						}
						break;
					case 2:
						//非企微客户主动进来时
						$waitTaskAll = WaitTask::find()->alias('w')->leftJoin('{{%wait_project}} p', 'w.project_id=p.id')->where(['w.follow_id' => $this->followId, 'p.is_del' => 0, 'w.is_del' => 0])->select('w.id,w.days')->asArray()->all();
						if (!empty($waitTaskAll)) {
							foreach ($waitTaskAll as $all) {
								$waitTask          = WaitTask::findOne($all['id']);
								$contactFollowUser = PublicSeaContactFollowUser::find()->where(['follow_id' => $this->followId, 'corp_id' => $this->corpId, 'is_reclaim' => 0])->groupBy('sea_id')->all();
								$customId          = [];
								$finishTime        = [];
								$cId               = [];
								if (!empty($contactFollowUser) && !empty($waitTask)) {
									/** @var PublicSeaContactFollowUser $followUser */
									foreach ($contactFollowUser as $followUser) {
										$this->add($all['id'], $waitTask, 1, $finishTime, $customId, $cId, NULL, $followUser->sea_id, $customIdNew);
									}
								}
								\Yii::error($customId, '$customId2');
								\Yii::error($finishTime, '$finishTime2');
								\Yii::error($cId, '$cId2');
								\Yii::error($customIdNew, '$customIdNew2');
								if (!empty($customId)) {
									$this->addCustom($customId, $all['id']);
								}

								WaitCustomerTask::sendMind ($cId);
							}
						}
						break;
					case 3:
						//企微客户修改跟进状态
						$followUser = WorkExternalContactFollowUser::findOne($this->followUserId);
						$waitTask   = WaitTask::find()->alias('w')->leftJoin('{{%wait_project}} p', 'w.project_id=p.id')->where(['w.follow_id' => $this->followId, 'p.is_del' => 0, 'w.is_del' => 0]);
						\Yii::error($waitTask->createCommand()->getRawSql(), 'sql3');
						$waitTask   = $waitTask->all();
						\Yii::error($waitTask, '$waitTask3');
						if (!empty($waitTask)) {
							$customId   = [];
							$finishTime = [];
							$cId        = [];
							/** @var WaitTask $task */
							foreach ($waitTask as $task) {
								$this->add($task->id, $task, 0, $finishTime, $customId, $cId, $followUser->external_userid,null, $customIdNew);
							}
							\Yii::error($customId, '$customId3');
							\Yii::error($finishTime, '$finishTime3');
							\Yii::error($cId, '$cId3');
							\Yii::error($customIdNew, '$customIdNew3');
							if (!empty($customId)) {
								$customTask = WaitCustomerTask::find()->where(['id' => $customId])->select('task_id')->asArray()->all();
								$taskId     = array_column($customTask, 'task_id');
								foreach ($waitTask as $task) {
									if (in_array($task->id, $taskId)) {
										$this->addCustom($customId, $task->id);
									}
								}
							}

							WaitCustomerTask::sendMind ($cId);

						}
						break;
					case 4:
						//非企微客户修改跟进状态
						$publicSea = PublicSeaContactFollowUser::findOne($this->followUserId);
						$waitTask  = WaitTask::find()->alias('w')->leftJoin('{{%wait_project}} p', 'w.project_id=p.id')->where(['w.follow_id' => $this->followId, 'p.is_del' => 0, 'w.is_del' => 0])->all();
						if (!empty($waitTask)) {
							$customId   = [];
							$finishTime = [];
							$cId        = [];
							/** @var WaitTask $task */
							foreach ($waitTask as $task) {
								$this->add($task->id, $task, 1, $finishTime, $customId, $cId, NULL, $publicSea->sea_id, $customIdNew);
							}
							\Yii::error($customId, '$customId4');
							\Yii::error($finishTime, '$finishTime4');
							\Yii::error($cId, '$cId4');
							\Yii::error($customIdNew, '$customIdNew4');
							if (!empty($customId)) {
								$customTask = WaitCustomerTask::find()->where(['id' => $customId])->select('task_id')->asArray()->all();
								$taskId     = array_column($customTask, 'task_id');
								foreach ($waitTask as $task) {
									if (in_array($task->id, $taskId)) {
										$this->addCustom($customId, $task->id);
									}
								}
							}

							WaitCustomerTask::sendMind ($cId);

						}
						break;
					case 5:
						$customId   = [];
						$finishTime = [];
						$cId        = [];
						//添加待办任务时
						$waitTask          = WaitTask::findOne($this->taskId);
						$contactFollowUser = WorkExternalContactFollowUser::find()->alias('f')->leftJoin('{{%work_external_contact}} c', 'c.id=f.external_userid');
						$contactFollowUser = $contactFollowUser->where(['c.corp_id' => $this->corpId, 'f.del_type' => WorkExternalContactFollowUser::WORK_CON_EX, 'f.follow_id' => $this->followId]);
						$contactFollowUser = $contactFollowUser->groupBy('f.external_userid')->all();
						//企微客户
						if (!empty($contactFollowUser) && !empty($waitTask)) {
							/** @var WorkExternalContactFollowUser $followUser */
							foreach ($contactFollowUser as $followUser) {
								$this->add($this->taskId, $waitTask, 0, $finishTime, $customId, $cId, $followUser->external_userid,null, $customIdNew);
							}

						}
						//非企微客户
						$contactFollowUser = PublicSeaContactFollowUser::find()->where(['follow_id' => $this->followId, 'corp_id' => $this->corpId, 'is_reclaim' => 0])->groupBy('sea_id')->all();
						if (!empty($contactFollowUser) && !empty($waitTask)) {
							/** @var PublicSeaContactFollowUser $followUser */
							foreach ($contactFollowUser as $followUser) {
								$this->add($this->taskId, $waitTask, 1, $finishTime, $customId, $cId, NULL, $followUser->sea_id,$customIdNew);
							}
						}
						\Yii::error($customId, '$customId5');
						\Yii::error($finishTime, '$finishTime5');
						\Yii::error($cId, '$cId5');
						\Yii::error($customIdNew, '$customIdNew5');
						if (!empty($customId)) {
							$this->addCustom($customId, $waitTask->id);
						}
						if (!empty($finishTime)) {
							$finishTime = array_unique($finishTime);
							WaitUserRemind::addMind($cId, $waitTask->project_id, $waitTask->id, $waitTask->project->user_id, $finishTime[0]);
						}
						break;
				}
				\Yii::error($customIdNew, '$customIdNew');
				//发送应用消息
				$waitTask  = WaitTask::find()->where(['follow_id' => $this->followId, 'is_del' => 0])->all();
				$follow    = Follow::findOne($this->followId);
				$uid       = $follow->uid;
				$waitAgent = WaitAgent::findOne(['corp_id' => $this->corpId]);
				$agentId   = $waitAgent->agent_id;
				if ($this->type == 5) {
					if (!empty($waitTask) && !empty($customIdNew)) {
						$userId = [];
						/** @var WaitTask $task */
						foreach ($waitTask as $task) {
							array_push($userId, $task->project->user_id);
						}
						$userId = array_unique($userId);
						\Yii::error($userId, '$userId');
						WaitTask::sendData($userId, $agentId, $customIdNew, $uid);
					}
				} else {
					if (!empty($waitTask) && !empty($customIdNew)) {
						/** @var WaitTask $task */
						foreach ($waitTask as $task) {
							WaitTask::sendData([$task->project->user_id], $agentId, $customIdNew, $uid, $task->id);
						}
					}
				}

			} catch (\Exception $e) {
				\Yii::error($e->getMessage(), 'WaitUserTaskJob');
			}

		}

		/**
		 * @param $customId
		 * @param $taskId
		 *
		 * @return bool
		 *
		 */
		public function addCustom ($customId, $taskId)
		{
			$waitCustom = WaitCustomerTask::find()->where(['id' => $customId, 'task_id' => $taskId])->all();
			$newId      = [];
			\Yii::error($waitCustom, '$waitCustom');
			if (!empty($waitCustom)) {
				/** @var WaitCustomerTask $cus */
				foreach ($waitCustom as $cus) {
					if (!empty($cus->queue_id)) {
						\Yii::$app->queue->remove($cus->queue_id);
						$cus->queue_id = 0;
						$cus->save();
					}
					array_push($newId, $cus->id);
				}
				$waitTask = WaitTask::findOne($taskId);
				$days     = $waitTask->days;
				//$second = $days * 24 * 3600;
				$endTime = strtotime(date('Y-m-d') . ' 23:59:59');
				$second  = $endTime - time() + ($days - 1) * 24 * 3600 + 1;
				\Yii::error($second, '$second');
				$jobId = \Yii::$app->queue->delay($second)->push(new WaitStartTaskJob([
					'userTaskId' => $newId,
					'taskId'     => $taskId,
					'type'       => 1,
				]));
				WaitCustomerTask::updateAll(['queue_id' => $jobId], ['id' => $customId, 'task_id' => $taskId]);
			}

			return true;
		}

		/**
		 * @param      $taskId
		 * @param      $waitTask
		 * @param      $type
		 * @param      $finishTime
		 * @param      $customId
		 * @param      $cId
		 * @param null $externalUserId
		 * @param null $seaId
		 * @param      $customIdNew
		 *
		 * @return bool
		 *
		 */
		public function add ($taskId, $waitTask, $type, &$finishTime, &$customId, &$cId, $externalUserId = NULL, $seaId = NULL, &$customIdNew)
		{
			try {
				$data                    = [];
				$data['task_id']         = $taskId;
				$data['type']            = $type;
				$data['external_userid'] = $externalUserId;
				$data['sea_id']          = $seaId;
				$userCorp                = UserCorpRelation::findOne(['corp_id' => $waitTask->project->corp_id]);
				if ($waitTask->type == 1 || $waitTask->type == 3) {
					//手动开启
					$waitStatus         = WaitStatus::find()->where(['uid' => $userCorp->uid, 'is_del' => 0])->orderBy(['sort' => SORT_ASC])->one();
					$data['status']     = $waitStatus->id; //待处理
					$data['start_time'] = 0;
					$data['end_time']   = 0;
				} elseif ($waitTask->type == 2) {
					//立即开启
					$data['start_time'] = time();
					$endTime            = ($waitTask->project->finish_time - 1) * 24 * 3600 + time();
					$endTime            = strtotime(date('Y-m-d', $endTime) . ' 23:59:59');
					$data['end_time']   = $endTime;
					$waitStatus         = WaitStatus::find()->where(['uid' => $userCorp->uid, 'is_del' => 0])->orderBy(['sort' => SORT_ASC])->asArray()->all();
					$data['status']     = $waitStatus[1]['id']; //处理中
				}
				$days = 0;
				if ($waitTask->type == 3) {
					$days = $waitTask->days;
				}
				\Yii::error($data, '$dataMsg');
				WaitCustomerTask::add($data, $days, $finishTime, $customId, $cId, $customIdNew);

				return true;
			} catch (\Exception $e) {
				\Yii::error($e->getMessage(), 'WaitUserTask');
			}
		}

	}