<?php

	namespace app\queue;

	use app\models\UserCorpRelation;
	use app\models\WaitCustomerTask;
	use app\models\WaitProjectRemind;
	use app\models\WaitStatus;
	use app\models\WaitTask;
	use app\models\WaitUserRemind;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;

	class WaitStartTaskJob extends BaseObject implements JobInterface
	{
		public $taskId;
		public $userTaskId;
		public $type;

		public function execute ($queue)
		{
			try {
				if($this->type == 1){
					$waitTask     = WaitTask::findOne($this->taskId);
					$time = '';
					\Yii::error($this->taskId,'taskIdNew');
					$waitUserTask = WaitCustomerTask::find()->where(['id' => $this->userTaskId])->all();
					if (!empty($waitUserTask)) {
						/** @var WaitCustomerTask $task */
						foreach ($waitUserTask as $task) {
							//N天后 如果当前未开启 则开启
							if (!empty($waitUserTask) && empty($task->start_time)) {
								$task->start_time = time();
								$userCorp         = UserCorpRelation::findOne(['corp_id' => $waitTask->project->corp_id]);
								$waitStatus       = WaitStatus::find()->where(['uid' => $userCorp->uid, 'is_del' => 0])->orderBy(['sort' => SORT_ASC])->asArray()->all();
								$task->status     = $waitStatus[1]['id'];//处理中 第二个执行状态的ID
								$endTime          = ($waitTask->project->finish_time - 1) * 24 * 3600 + time();
								$endTime          = strtotime(date('Y-m-d', $endTime) . ' 23:59:59');
								$task->end_time   = $endTime;
								$task->open_time  = time();
								$task->save();
								$time = $endTime;
								\Yii::error($endTime, '$endTime');
							}
						}
					}
					if (!empty($time)) {
						WaitUserRemind::addMind($this->userTaskId, $waitTask->project_id, $waitTask->id, $waitTask->project->user_id, $time);
					}
				}else{
					$customerTask = WaitCustomerTask::find()->where(['task_id' => $this->taskId, 'is_finish' => 0])->all();
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

			} catch (\Exception $e) {
				\Yii::error($e->getMessage(), 'WaitUserTaskJob');
			}
		}

	}