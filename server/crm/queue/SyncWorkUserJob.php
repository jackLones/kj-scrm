<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2020/1/7
	 * Time: 17:30
	 */

	namespace app\queue;

	use app\models\WorkCorp;
	use app\models\WorkDepartment;
	use app\models\WorkUser;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;

	class SyncWorkUserJob extends BaseObject implements JobInterface
	{
		/** @var WorkCorp */
		public $corp;
		/** @var int */
		public $userId;

		public function execute ($queue)
		{
			try {
				WorkUser::getUserSuite($this->corp->id, $this->userId);
			} catch (\Exception $e) {
				return false;
			}
		}
	}