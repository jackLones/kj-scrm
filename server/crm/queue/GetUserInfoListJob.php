<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2020/1/7
	 * Time: 17:44
	 */

	namespace app\queue;

	use app\models\WorkDepartment;
	use app\models\WorkUser;
	use app\util\SUtils;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;

	class GetUserInfoListJob extends BaseObject implements JobInterface
	{
		/** @var string */
		public $partyId;
		/** @var int */
		public $fetchChild;

		public function execute ($queue)
		{
			$department = WorkDepartment::findOne($this->partyId);

			if (empty($department)) {
				return false;
			}

			try {
				$departUserList = $department->getDepartUser(WorkDepartment::PARTY_USER_INFO, $this->fetchChild);

				if (!empty($departUserList)) {
					foreach ($departUserList as $userInfo) {
						$userInfo = SUtils::Object2Array($userInfo);

						try {
							WorkUser::setUser($department->corp->corpid, $userInfo);

							$jobId = \Yii::$app->work->push(new SyncWorkUserJob([
								'corp'   => $department->corp,
								'userId' => $userInfo['userid'],
							]));
						} catch (\Exception $e) {
							continue;
						}
					}
				}
			} catch (\Exception $e) {
				return false;
			}
		}
	}