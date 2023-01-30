<?php
	/**
	 * Title: ChangeUserDelJob
	 * User: sym
	 * Date: 2021/3/30
	 * Time: 17:35
	 *
	 * @remark
	 */

	namespace app\queue;

	use app\models\WorkExternalContactFollowUser;
	use app\models\WorkUserDelFollowUserDetail;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;
	use yii\queue\Queue;

	class ChangeUserDelJob extends BaseObject implements JobInterface
	{
		public $data = [];

		public function execute ($queue)
		{
			ini_set('memory_limit', '1024M');
			set_time_limit(0);
			if (!empty($this->data)) {
				$exists = WorkExternalContactFollowUser::find()->where(["delete_type" => 2, "external_userid" => $this->data["external_userid"], "user_id" => $this->data["user_id"]])->exists();
				if ($exists) {
					WorkUserDelFollowUserDetail::updateAll(["del_type" => 2], ["id" => $this->data["id"]]);

					return;
				}
			} else {
				$delDetailData = WorkUserDelFollowUserDetail::find()->where(["del_type" => NULL])->all();
				if (empty($delDetailData)) {
					return;
				}
				/**@var $detail WorkUserDelFollowUserDetail* */
				foreach ($delDetailData as $detail) {
					\Yii::$app->queue->push(new ChangeUserDelJob(["data" => $detail]));
				}
			}
		}
	}