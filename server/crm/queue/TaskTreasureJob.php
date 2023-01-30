<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2019/10/14
	 * Time: 16:43
	 */

	namespace app\queue;

	use app\models\WorkPublicActivity;
	use app\models\WorkPublicActivityFansUser;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;

	class TaskTreasureJob extends BaseObject implements JobInterface
	{

		public $activity;
		public $fromUserName;
		public $subscribe;
		public $type = true;

		public function execute ($queue)
		{
			\Yii::error("sym", "sym222222");
			try {
				if ($this->type) {
					$PublicActivity = WorkPublicActivity::find()->where(["id" => $this->activity[1]])->one()->toArray();

					WorkPublicActivityFansUser::wechatPublicScanSendActivity($this->fromUserName, $PublicActivity, $this->activity, $this->subscribe);
				} else {
					$PublicActivity = WorkPublicActivity::find()->where(["id" => $this->activity['id']])->one()->toArray();
					WorkPublicActivityFansUser::WechatActivityTextMsg($this->fromUserName, $PublicActivity, $this->subscribe);
				}

			} catch (\Exception $e) {
				\Yii::error($e->getLine(), "sym-job");
				\Yii::error($e->getMessage(), "sym-job");
			}

		}
	}