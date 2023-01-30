<?php

namespace app\models;

use app\components\InvalidDataException;
use app\queue\WaitStartTaskJob;
use app\util\SUtils;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "{{%wait_customer_task}}".
 *
 * @property int                 $id
 * @property int                 $task_id         任务ID
 * @property int                 $external_userid 企微客户外部联系人
 * @property int                 $sea_id          公海客户
 * @property int                 $start_time      开始时间
 * @property int                 $end_time        结束时间
 * @property int                 $status          0未开始 其他代表项目执行状态表的ID
 * @property int                 $queue_id        队列ID
 * @property int                 $type            0企微客户1公海客户
 * @property int                 $is_finish       是否完成 0未完成 1已完成
 * @property int                 $finish_time     实际完成时间
 * @property string              $per             进度百分比
 * @property string              $per_desc        进度说明
 * @property int                 $is_del          是否删除 0未删除 1已删除
 * @property int                 $create_time     创建时间
 * @property int                 $open_time       启动时间
 *
 * @property PublicSeaCustomer   $sea
 * @property WorkExternalContact $externalUser
 * @property WaitTask            $task
 */
class WaitCustomerTask extends \yii\db\ActiveRecord
{
	const H5_URL = '/h5/pages/scrm/todo';
    /**
     * {@inheritdoc}
     */
	public static function tableName ()
	{
		return '{{%wait_customer_task}}';
	}

    /**
     * {@inheritdoc}
     */
	public function rules ()
	{
		return [
			[['task_id', 'external_userid', 'sea_id', 'start_time', 'end_time', 'status', 'queue_id', 'type', 'is_finish', 'finish_time', 'is_del', 'create_time'], 'integer'],
			[['per'], 'string', 'max' => 32],
			[['per_desc'], 'string', 'max' => 255],
			[['sea_id'], 'exist', 'skipOnError' => true, 'targetClass' => PublicSeaCustomer::className(), 'targetAttribute' => ['sea_id' => 'id']],
			[['external_userid'], 'exist', 'skipOnError' => true, 'targetClass' => WorkExternalContact::className(), 'targetAttribute' => ['external_userid' => 'id']],
			[['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => WaitTask::className(), 'targetAttribute' => ['task_id' => 'id']],
		];
	}

    /**
     * {@inheritdoc}
     */
	public function attributeLabels ()
	{
		return [
			'id'              => 'ID',
			'task_id'         => '任务ID',
			'external_userid' => '企微客户外部联系人',
			'sea_id'          => '公海客户',
			'start_time'      => '开始时间',
			'end_time'        => '结束时间',
			'status'          => '0未开始 其他代表项目执行状态表的ID',
			'queue_id'        => '队列ID',
			'type'            => '0企微客户1公海客户',
			'is_finish'       => '是否完成 0未完成 1已完成',
			'finish_time'     => '实际完成时间',
			'per'             => '进度百分比',
			'per_desc'        => '进度说明',
			'is_del'          => '是否删除 0未删除 1已删除',
			'create_time'     => '创建时间',
			'open_time'       => '启动时间',
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
    public function getSea()
    {
        return $this->hasOne(PublicSeaCustomer::className(), ['id' => 'sea_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExternalUser()
    {
        return $this->hasOne(WorkExternalContact::className(), ['id' => 'external_userid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTask()
    {
        return $this->hasOne(WaitTask::className(), ['id' => 'task_id']);
    }

	/**
	 * @param      $followId
	 * @param      $type
	 * @param null $externalUserId
	 * @param null $seaId
	 *
	 * @return int
	 *
	 */
	public static function isDone ($followId, $type, $externalUserId = NULL, $seaId = NULL)
	{
		$canEditFollow = 1;
		$waitTask      = WaitTask::findOne(['follow_id' => $followId, 'is_del' => 0]);
		if (!empty($waitTask)) {
			if ($type == 0) {
				$customerTask = WaitCustomerTask::findOne(['type' => $type, 'task_id' => $waitTask->id, 'external_userid' => $externalUserId, 'is_del' => 0]);
			} else {
				$customerTask = WaitCustomerTask::findOne(['type' => $type, 'task_id' => $waitTask->id, 'sea_id' => $seaId, 'is_del' => 0]);
			}
			if (!empty($customerTask)) {
				$followStatus = Follow::findOne($followId);
				//if ($followStatus->is_change == 1) {
				//需要完成待办事项才能更改状态
				if ($followStatus->type == 1) {
					//需要完成所有的
					$waitOne = WaitTask::find()->alias('t')->leftJoin('{{%wait_customer_task}} w', 'w.task_id=t.id')->where(['t.follow_id' => $followId, 't.is_del' => 0, 'w.is_finish' => 0]);
					if ($type == 0) {
						$waitOne = $waitOne->andWhere(['w.external_userid' => $externalUserId]);
					} else {
						$waitOne = $waitOne->andWhere(['w.sea_id' => $seaId]);
					}
					$waitOne = $waitOne->one();
					if (!empty($waitOne)) {
						//存在未完成的
						$canEditFollow = 0;
					}
				} else {
					$way = Json::decode($followStatus->way);
					if (!empty($way)) {
						if (count($way) == 2) {
							$projectOne = Json::decode($followStatus->project_one);
							$waitCount  = WaitTask::find()->alias('t')->leftJoin('{{%wait_customer_task}} w', 'w.task_id=t.id')->where(['t.follow_id' => $followId, 't.is_del' => 0, 'w.is_finish' => 1, 't.project_id' => $projectOne]);
							if ($type == 0) {
								$waitCount = $waitCount->andWhere(['w.external_userid' => $externalUserId]);
							} else {
								$waitCount = $waitCount->andWhere(['w.sea_id' => $seaId]);
							}
							$waitCount = $waitCount->groupBy(['t.project_id'])->count();
							if ($waitCount < $followStatus->num) {
								$canEditFollow = 0;
							}
							$projectTwo = Json::decode($followStatus->project_two);
							foreach ($projectTwo as $pro) {
								$wait = WaitTask::find()->alias('t')->leftJoin('{{%wait_customer_task}} w', 'w.task_id=t.id')->where(['t.follow_id' => $followId, 't.is_del' => 0, 'w.is_finish' => 0, 't.project_id' => $pro]);
								if ($type == 0) {
									$wait = $wait->andWhere(['w.external_userid' => $externalUserId]);
								} else {
									$wait = $wait->andWhere(['w.sea_id' => $seaId]);
								}
								$wait = $wait->one();
								if (!empty($wait)) {
									$canEditFollow = 0;
								}
							}
						} else {
							if ($way[0] == 1) {
								$projectOne = Json::decode($followStatus->project_one);
								$waitCount  = WaitTask::find()->alias('t')->leftJoin('{{%wait_customer_task}} w', 'w.task_id=t.id')->where(['t.follow_id' => $followId, 't.is_del' => 0, 'w.is_finish' => 1, 't.project_id' => $projectOne]);
								if ($type == 0) {
									$waitCount = $waitCount->andWhere(['w.external_userid' => $externalUserId]);
								} else {
									$waitCount = $waitCount->andWhere(['w.sea_id' => $seaId]);
								}
								$waitCount = $waitCount->groupBy(['t.project_id'])->count();
								if ($waitCount < $followStatus->num) {
									$canEditFollow = 0;
								}
							} else {
								$projectTwo = Json::decode($followStatus->project_two);
								foreach ($projectTwo as $pro) {
									$wait = WaitTask::find()->alias('t')->leftJoin('{{%wait_customer_task}} w', 'w.task_id=t.id')->where(['t.follow_id' => $followId, 't.is_del' => 0, 'w.is_finish' => 0, 't.project_id' => $pro]);
									if ($type == 0) {
										$wait = $wait->andWhere(['w.external_userid' => $externalUserId]);
									} else {
										$wait = $wait->andWhere(['w.sea_id' => $seaId]);
									}
									$wait = $wait->one();
									if (!empty($wait)) {
										$canEditFollow = 0;
									}
								}
							}
						}
					}
				}
				//}

			}
		}

		return $canEditFollow;
	}

	/**
	 * @param $data
	 * @param $days
	 * @param $finishTime
	 * @param $customId
	 * @param $cId
	 * @param $customIdNew
	 *
	 * @return array
	 *
	 * @throws InvalidDataException
	 */
	public static function add ($data, $days, &$finishTime, &$customId, &$cId, &$customIdNew)
	{
		if ($data['type'] == 0) {
			$customTask = static::findOne(['task_id' => $data['task_id'], 'type' => $data['type'], 'external_userid' => $data['external_userid']]);
		} else {
			$customTask = static::findOne(['task_id' => $data['task_id'], 'type' => $data['type'], 'sea_id' => $data['sea_id']]);
		}
		$flag = true;
		if (empty($customTask)) {
			$customTask              = new WaitCustomerTask();
			$customTask->create_time = time();
			$flag                    = false;
		}
		$customTask->task_id         = $data['task_id'];
		$customTask->external_userid = $data['external_userid'];
		$customTask->sea_id          = $data['sea_id'];
		if (!$flag || ($flag && empty($customTask->start_time))) {
			$customTask->type       = $data['type'];
			$customTask->status     = $data['status'];
			$customTask->start_time = $data['start_time'];
			$customTask->end_time   = $data['end_time'];
			array_push($finishTime, $customTask->end_time);
			if (!empty($customTask->queue_id)) {
				$customTask->queue_id = 0;
				\Yii::$app->queue->remove($customTask->queue_id);
			}
		}
		if (!empty($customTask->end_time)) {
			$waitTask = WaitTask::findOne($data['task_id']);
			if ($waitTask->project->old_days != $waitTask->project->finish_time) {
				$endTime              = ($waitTask->project->finish_time-1) * 24 * 3600 + time();
				$endTime              = strtotime(date('Y-m-d', $endTime) . ' 23:59:59');
				$customTask->end_time = $endTime;
				array_push($finishTime, $customTask->end_time);
			}

		}
		if (!$flag) {
			if (!empty($customTask->queue_id)) {
				$customTask->queue_id = 0;
				\Yii::$app->queue->remove($customTask->queue_id);
			}
		}
		if (!$customTask->validate() || !$customTask->save()) {
			throw new InvalidDataException('失败原因：' . SUtils::modelError($customTask));
		}
		if ($days > 0 && (!$flag || ($flag && empty($customTask->start_time)))) {
			array_push($customId, $customTask->id);
		}
		array_push($cId, $customTask->id);
		if (!$flag) {
			array_push($customIdNew, $customTask->id);
		}
		if ($data['type'] == 0) {
			$projectFollow = WaitProjectFollow::find()->where(['task_id' => $data['task_id'], 'external_userid' => $data['external_userid']])->all();
		} else {
			$projectFollow = WaitProjectFollow::find()->where(['task_id' => $data['task_id'], 'sea_id' => $data['sea_id']])->all();
		}
		if (!empty($projectFollow)) {
			/** @var WaitProjectFollow $foll */
			foreach ($projectFollow as $foll) {
				$foll->customer_task_id = $customTask->id;
				$foll->save();
			}
		}

		return true;
	}

	/**
	 * @param $type
	 * @param $id
	 * @param $uid
	 * @param $followId
	 *
	 * @return array
	 *
	 */
	public static function getDetail ($type, $id, $uid, $followId)
	{
		$data = [];
		if ($type == 1) {
			$customTask = WaitCustomerTask::find()->alias('c')->leftJoin('{{%wait_task}} w', 'w.id=c.task_id')->where(['c.sea_id' => $id, 'c.is_del' => 0,  'w.follow_id' => $followId])->all();
		} else {
			$customTask = WaitCustomerTask::find()->alias('c')->leftJoin('{{%wait_task}} w', 'w.id=c.task_id')->where(['c.external_userid' => $id, 'c.is_del' => 0, 'w.follow_id' => $followId]);
			$customTask = $customTask->all();
		}
		$waitStatus    = WaitStatus::find()->where(['uid' => $uid, 'is_del' => 0])->orderBy(['sort' => SORT_ASC])->asArray()->all();
		$waitStatusOne = WaitStatus::find()->where(['uid' => $uid, 'is_del' => 0])->orderBy(['sort' => SORT_DESC])->one();
		if (!empty($customTask)) {
			/** @var WaitCustomerTask $task */
			foreach ($customTask as $key => $task) {
				$status = 2;//处理中
				if ($task['status'] == $waitStatus[0]['id']) {
					$status = 1;//待处理
				}
				if ($task['status'] == $waitStatusOne->id) {
					$status = 3;//已完成
				}
				$data[$key]['start_time']  = !empty($task->start_time) ? date('Y-m-d', $task->start_time) : 0;
				$data[$key]['end_time']    = !empty($task->end_time) ? date('Y-m-d', $task->end_time) : 0;
				$data[$key]['finish_time'] = !empty($task->finish_time) ? date('Y-m-d', $task->finish_time) : 0;
				$waitTask                  = WaitTask::findOne($task->task_id);
				$userId                    = $waitTask->project->user_id;
				$name                      = '--';
				$workUser                  = WorkUser::findOne($userId);
				if (!empty($workUser)) {
					$name = $workUser->name;
				}
				$data[$key]['name'] = $name;
				$data[$key]['days'] = $waitTask->project->finish_time;
				$isFinish           = 0; //未完成
				$delayDays          = 0;
				if (!empty($task->finish_time) && $task->finish_time > $task->end_time) {
					//超时
					$delayDays = ceil(($task->finish_time - $task->end_time) / (24 * 3600));
					$isFinish  = 2;//超时完成
				}

				$preDays = 0;
				if (!empty($task->finish_time) && $task->finish_time <= $task->end_time) {
					$date1 = date('Y-m-d', $task->end_time);
					$date2 = date('Y-m-d', $task->finish_time);
					if ($date1 == $date2) {
						$isFinish = 1; //按时完成
					} else {
						//提前
						$preDays  = floor(($task->end_time - $task->finish_time) / (24 * 3600));
						$isFinish = 3; //提前
					}

				}
				$title = $waitTask->project->title;
				if ($waitTask->project->is_del == 1) {
					$title .= '（已删除）';
				}
				$data[$key]['project_name'] = $title;
				$data[$key]['delay_days']   = $delayDays;
				$data[$key]['pre_days']     = $preDays;
				$data[$key]['is_finish']    = $isFinish; //0 未完成 1按时完成 2 超时完成 3 提前完成
				$data[$key]['status']       = $status;
			}
		}
		return $data;
	}

	/**
	 * @param     $external_userid
	 * @param     $seaId
	 * @param     $oldFollowId
	 * @param int $type
	 *
	 * @return bool
	 *
	 * @throws \Throwable
	 * @throws \yii\db\StaleObjectException
	 */
	public static function deleteData ($external_userid, $seaId, $oldFollowId, $type = 0)
	{
		$customTask = WaitCustomerTask::find()->alias('c')->leftJoin('{{%wait_task}} w', 'w.id=c.task_id')->where(['w.follow_id' => $oldFollowId]);
		if (empty($type)) {
			$customTask = $customTask->andWhere(['c.external_userid' => $external_userid]);
		} else {
			$customTask = $customTask->andWhere(['c.sea_id' => $seaId]);
		}
		\Yii::error($customTask->createCommand()->getRawSql(),'sql');
		$customTask = $customTask->all();
		if (!empty($customTask)) {
			/** @var WaitCustomerTask $ta */
			foreach ($customTask as $ta) {
				WaitUserRemind::deleteAll(['custom_id' => $ta->id, 'task_id' => $ta->task_id]);
				if (!empty($ta->queue_id)) {
					\Yii::$app->queue->remove($ta->queue_id);
				}
				$ta->delete();
			}
		}

		return true;
	}

	/**
	 * @param $cId
	 *
	 * @return bool
	 *
	 */
	public static function sendMind ($cId)
	{
		if (!empty($cId)) {
			foreach ($cId as $id) {
				$wTask = WaitCustomerTask::findOne($id);
				if (!empty($wTask->end_time)) {
					WaitUserRemind::addMind([$id], $wTask->task->project_id, $wTask->task_id, $wTask->task->project->user_id, $wTask->end_time);
				}
			}
		}

		return true;
	}


}
