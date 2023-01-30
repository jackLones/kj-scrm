<?php
	/**
	 * Create by PhpStorm
	 * User: wangpan
	 * Date: 2020/11/16
	 * Time: 15:42
	 */

	namespace app\queue;

	use app\models\WorkUser;
	use app\models\WorkUserStatistic;
	use app\util\DateUtil;
	use app\util\SUtils;
	use app\util\WorkUtils;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;

	class WorkUserStatisticJob extends BaseObject implements JobInterface
	{
		public $corpId;
		public $sTime;
		public $eTime;
		public $type;

		public function execute ($queue)
		{
			$corpId = $this->corpId;
			\Yii::error($corpId, '$corpId');
			\Yii::error($this->sTime, '$sTime');
			\Yii::error($this->eTime, '$eTime');
			$workUserData = WorkUser::find()->alias('w');
			$workUserData = $workUserData->leftJoin('{{%work_follow_user}} wf', 'w.id=wf.user_id');
			$workUserData = $workUserData->andWhere(['w.corp_id' => $corpId, 'w.status' => 1, 'wf.status' => 1]);
			$workUser     = $workUserData->select('w.id,w.userid,w.new_apply_cnt,w.new_contact_cnt,w.negative_feedback_cnt,w.chat_cnt,w.message_cnt,w.reply_percentage,w.avg_reply_time')->orderBy(['w.id' => SORT_ASC]);
			$workUser     = $workUser->all();

			/**
			 * @var k        $kk
			 * @var WorkUser $vv
			 */
			foreach ($workUser as $kk => $vv) {
				\Yii::$app->queue->push(new WorkGetUserBeheviorJob([
					'corp_id'   => $corpId,
					'user_data' => $vv,
					'stime'     => $this->sTime,
					'etime'     => $this->eTime,
					'type'      => $this->type,
				]));
			}
		}
	}