<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2020/1/7
	 * Time: 15:50
	 */

	namespace app\queue;

	use app\models\WorkCorp;
	use app\models\WorkDepartment;
	use app\util\SUtils;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;

	class SyncWorkDepartmentListJob extends BaseObject implements JobInterface
	{
		/** @var WorkCorp */
		public $corp;
		/** @var String */
		public $departId;
		public $from = 1;
		public $need_external = false;

		public function execute ($queue)
		{
			ini_set('memory_limit', '4096M');
			set_time_limit(0);

			try {
				$departmentInfo = WorkDepartment::getDepartmentList($this->corp->id, $this->departId);

				if (!empty($departmentInfo)) {
					foreach ($departmentInfo as $department) {
						$department = SUtils::Object2Array($department);

						try {
							$department['is_del'] = WorkDepartment::PARTY_NO_DEL;
							$departmentId         = WorkDepartment::setDepartment($this->corp->id, $department);

							$jobId = \Yii::$app->work->push(new GetUserListJob([
								'partyId'    => $departmentId,
								'fetchChild' => WorkDepartment::NOT_FETCH_CHILD,
							]));
						} catch (\Exception $e) {
							continue;
						}
					}

					if ($this->from == 1) {
						$followUserJobId = \Yii::$app->work->push(new SyncWorkFollowUserJob([
							'corp'          => $this->corp,
							'need_external' => $this->need_external,
						]));
					}
				}
			} catch (\Exception $e) {
				return false;
			}
		}
	}