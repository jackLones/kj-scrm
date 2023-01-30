<?php
	/**
	 * Create by PhpStorm
	 * User: wangpan
	 * Date: 2020/2/21
	 * Time: 16:26
	 */

	namespace app\queue;

	use app\models\WorkCorp;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;
	use app\models\WorkTag;

	class SyncWorkTagUserJob extends BaseObject implements JobInterface
	{
		/** @var WorkCorp */
		public $corp_id;
		/** @var int */
		public $group_id;

		public function execute ($queue)
		{
			ini_set('memory_limit', '4096M');
			set_time_limit(0);
			try {
				//同步企业微信标签
				WorkTag::syncWorkTag($this->corp_id, $this->group_id);
				//同步标签客户
				WorkTag::getWorkTagUser($this->corp_id);

				$workCorp                = WorkCorp::findOne($this->corp_id);
				$workCorp->last_tag_time = time();
				$workCorp->save();
			} catch (\Exception $e) {
				\Yii::error($e->getMessage(), 'SyncWorkTagUserJob');
			}
		}
	}