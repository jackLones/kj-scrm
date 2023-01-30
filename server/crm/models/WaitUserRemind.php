<?php

namespace app\models;

use app\components\InvalidDataException;
use app\util\ShortUrlUtil;
use app\util\SUtils;
use Yii;

/**
 * This is the model class for table "{{%wait_user_remind}}".
 *
 * @property int $id
 * @property int $task_id     任务ID
 * @property int $custom_id   客户ID
 * @property int $user_id     员工ID
 * @property int $end_time    截止时间
 * @property int $days        天数
 * @property int $type        1 预计结束时间前 2 项目超时
 * @property int $create_time 创建时间
 */
class WaitUserRemind extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%wait_user_remind}}';
    }

    /**
     * {@inheritdoc}
     */
	public function rules ()
	{
		return [
			[['task_id', 'custom_id', 'user_id', 'end_time', 'days', 'type', 'create_time'], 'integer'],
			[['custom_id'], 'exist', 'skipOnError' => true, 'targetClass' => WaitCustomerTask::className(), 'targetAttribute' => ['custom_id' => 'id']],
		];
	}

	/**
	 * @return object|\yii\db\Connection|null
	 *
	 * @throws \yii\base\InvalidConfigException
	 */
	public static function getDb ()
	{
		return Yii::$app->get('mdb');
	}

    /**
     * {@inheritdoc}
     */
	public function attributeLabels ()
	{
		return [
			'id'          => 'ID',
			'task_id'     => '任务ID',
			'custom_id'   => '客户ID',
			'user_id'     => '员工ID',
			'end_time'    => '截止时间',
			'days'        => '天数',
			'type'        => '1 预计结束时间前 2 项目超时',
			'create_time' => '创建时间',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getCustom()
	{
		return $this->hasOne(WaitCustomerTask::className(), ['id' => 'custom_id']);
	}

	/**
	 * @param $customId
	 * @param $projectId
	 * @param $taskId
	 * @param $userId
	 * @param $time
	 *
	 * @return bool
	 *
	 */
	public static function addMind ($customId,$projectId, $taskId, $userId, $time)
	{
		\Yii::error($customId,'$customId123');
		\Yii::error($time,'$time');
		\Yii::error($taskId,'$taskId');
		\Yii::error($userId,'$userId');
		if(!empty($customId) && !empty($time)){
			foreach ($customId as $cus){
				$projectRemind = WaitProjectRemind::find()->where(['project_id' => $projectId])->all();
				if (!empty($projectRemind)) {
					/** @var WaitProjectRemind $mind */
					foreach ($projectRemind as $mind) {
						$data              = [];
						$data['task_id']   = $taskId;
						$data['user_id']   = $userId;
						$data['end_time']  = $time;
						$data['days']      = $mind->days;
						$data['type']      = $mind->type;
						$data['custom_id'] = $cus;
						try {
							WaitUserRemind::deleteAll(['task_id'=>$taskId,'custom_id'=>$cus]);
							WaitUserRemind::addData($data);
						} catch (\Exception $e) {
							\Yii::error($e->getMessage(), 'WaitUserRemind');
						}
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
	 */
	public static function addData ($data)
	{
		$remind              = new WaitUserRemind();
		$remind->create_time = time();
		$remind->task_id     = $data['task_id'];
		$remind->user_id     = $data['user_id'];
		$remind->end_time    = $data['end_time'];
		$remind->days        = $data['days'];
		$remind->type        = $data['type'];
		$remind->custom_id   = $data['custom_id'];
		if (!$remind->validate() || !$remind->save()) {
			throw new InvalidDataException('失败：' . SUtils::modelError($remind));
		}

		return true;
	}

	/**
	 * 每日9点提醒 超时的和提前
	 */
	public static function getEveryDayData ()
	{
		$date = strtotime(date('Y-m-d'));
		\Yii::error($date, 'date');
		//$date = strtotime('2020-09-30');
		$project = WaitProject::find()->alias('p')->leftJoin('{{%wait_task}} t', 't.project_id=p.id');
		$project = $project->leftJoin('{{%wait_customer_task}} c', 'c.task_id=t.id');
		$project = $project->where(['p.is_del' => 0, 't.is_del' => 0, 'c.is_finish' => 0])->select('p.user_id,p.corp_id')->groupBy('p.user_id');
		\Yii::error($project->createCommand()->getRawSql(), 'sql123');
		$project = $project->asArray()->all();
		if (!empty($project)) {
			foreach ($project as $pro) {
				$name     = '';
				$workUser = WorkUser::findOne($pro['user_id']);
				if (!empty($workUser)) {
					$name = $workUser->name;
				}
				$preCount  = 0;
				$delCount  = 0;
				$pre       = [];
				$del       = [];
				$preRemind = WaitUserRemind::find()->where(['user_id' => $pro['user_id'], 'type' => 1])->all();
				if (!empty($preRemind)) {
					/** @var WaitUserRemind $remind */
					foreach ($preRemind as $remind) {
						$dateEnd = strtotime(date('Y-m-d', $remind->end_time + 1));
						if ($dateEnd > $date) {
							$time = ($dateEnd - $date) / 86400;
							$time = $time - 1;
							if ($time == $remind->days) {
								$preCount++;
								array_push($pre, $remind->custom_id);
							}
						}
					}
				}
				$delRemind = WaitUserRemind::find()->where(['user_id' => $pro['user_id'], 'type' => 2])->all();
				if (!empty($delRemind)) {
					/** @var WaitUserRemind $remind */
					foreach ($delRemind as $remind) {
						$dateEnd = strtotime(date('Y-m-d', $remind->end_time + 1));
						if ($dateEnd <= $date) {
							$time = ($date - $dateEnd) / 86400;
							$time = $time + 1;
							if ($time == $remind->days) {
								$delCount++;
								array_push($del, $remind->custom_id);
							}
						}
					}
				}
				$str = '';
				if ($preCount > 0 && $delCount == 0) {
					$str .= $name . '，您当前有' . $preCount . '个待办任务需要尽快完成，请尽快处理';
				}
				if ($delCount > 0 && $preCount == 0) {
					$str .= $name . '，您当前有' . $delCount . '个待办任务已超时，请尽快处理';
				}
				if ($delCount > 0 && $preCount > 0) {
					$str .= $name . '，您当前有' . $preCount . '个待办任务需要尽快完成，请尽快处理<br/>';
					$str .= '有' . $delCount . '个待办任务已超时，请尽快处理';
				}
				\Yii::error($name, 'name');
				\Yii::error($str, 'str');

				if (!empty($str)) {
					$corpInfo    = WorkCorp::findOne($pro['corp_id']);
					$waitAgent   = WaitAgent::findOne(['corp_id' => $pro['corp_id']]);
					$agentId     = $waitAgent->agent_id;
					$web_url     = \Yii::$app->params['web_url'];
					$customIdNew = array_merge($pre, $del);
					$customId    = implode(',', $customIdNew);
					$corpAgent   = WorkCorpAgent::findOne(['is_del' => WorkCorpAgent::AGENT_NO_DEL, 'close' => WorkCorpAgent::AGENT_NOT_CLOSE, 'agent_type' => WorkCorpAgent::CUSTOM_AGENT, 'id' => $agentId]);
					$url         = WaitCustomerTask::H5_URL . '?corp_id=' . $corpAgent->corp_id . '&corpid=' . $corpInfo->corpid . '&agent_id=' . $agentId . '&custom_id=' . $customId . '&user_ids=' . $workUser->id;
					$url         = ShortUrlUtil::setShortUrl($url);
					$shortUrl    = $web_url . '/h5/I/' . $url;
					\Yii::error($url, '$url');
					$str = $str . '，<a href="' . $shortUrl . '">查看详情</a>';

					PublicSeaCustomer::messageSend([$workUser->userid], $agentId, $str, $corpInfo);
				}

//				if (!empty($str)) {
//					$corpInfo  = WorkCorp::findOne($pro['corp_id']);
//					$waitAgent = WaitAgent::findOne(['corp_id' => $pro['corp_id']]);
//					$agentId   = $waitAgent->agent_id;
//					if (\Yii::$app->cache->exists("$workUser->userid" . "$agentId")) {
//						return;
//					}
//					\Yii::$app->cache->set("$workUser->userid" . "$agentId", 1, 5);
//					PublicSeaCustomer::messageSend([$workUser->userid], $agentId, $str, $corpInfo);
//				}

			}
		}
	}

}
