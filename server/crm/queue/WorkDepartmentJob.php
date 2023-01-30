<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2020/1/3
	 * Time: 18:40
	 */

	namespace app\queue;

	use app\components\InvalidDataException;
	use app\models\WorkCorp;
	use app\models\WorkCorpAgent;
	use app\models\WorkCorpAuth;
	use app\models\WorkCorpBind;
	use app\models\WorkDepartment;
	use app\models\WorkSuiteConfig;
	use app\util\SUtils;
	use app\util\WorkUtils;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;

	class WorkDepartmentJob extends BaseObject implements JobInterface
	{
		public $xml;
		public $from = WorkUtils::FROM_SERVICE;

		public function execute ($queue)
		{
			$departmentInfoData = SUtils::Xml2Array($this->xml);
			SUtils::arrayCase($departmentInfoData);

			if ($this->from == WorkUtils::FROM_SERVICE) {
				$workSuiteConfig = WorkSuiteConfig::findOne(['suite_id' => $departmentInfoData['suiteid']]);

				if (empty($workSuiteConfig)) {
					return false;
				}

				$authCorp = WorkCorp::findOne(['corpid' => $departmentInfoData['authcorpid']]);

				if (empty($authCorp) || (!empty($authCorp->workCorpBind) && $authCorp->workCorpBind->book_status == WorkCorpBind::BOOK_OPEN)) {
					return false;
				}

				$authCorpAuth = WorkCorpAuth::findOne(['suite_id' => $workSuiteConfig->id, 'corp_id' => $authCorp->id]);

				if (empty($authCorpAuth)) {
					return false;
				}
			} elseif ($this->from == WorkUtils::FROM_AGENT) {
				$authCorp = WorkCorp::findOne(['corpid' => $departmentInfoData['tousername']]);

				$agentInfo = WorkCorpAgent::findOne(['corp_id' => $authCorp->id, 'agent_type' => WorkCorpAgent::CUSTOM_AGENT, 'close' => WorkCorpAgent::AGENT_NOT_CLOSE, 'is_del' => WorkCorpAgent::AGENT_NO_DEL]);

				if (empty($authCorp) || empty($agentInfo)) {
					return false;
				}
			} else {
				$authCorp = WorkCorp::findOne(['corpid' => $departmentInfoData['tousername']]);

				if (empty($authCorp) || empty($authCorp->workCorpBind) || $authCorp->workCorpBind->book_status == WorkCorpBind::BOOK_CLOSE) {
					return false;
				}
			}

			try {
				if ($departmentInfoData['changetype'] == WorkDepartment::DELETE_PARTY) {
					$workDepartment = WorkDepartment::findOne(['corp_id' => $authCorp->id, 'department_id' => $departmentInfoData['id']]);
					if (empty($workDepartment)) {
						return false;
					}

					$workDepartment->is_del = WorkDepartment::PARTY_IS_DEL;
					$workDepartment->update();
				} else {
					$departmentInfo = WorkDepartment::getDepartmentList($authCorp->id, $departmentInfoData['id']);

					if (!empty($departmentInfo)) {
						foreach ($departmentInfo as $department) {
							$department = SUtils::Object2Array($department);

							try {
								$department['is_del'] = WorkDepartment::PARTY_NO_DEL;
								$departmentId         = WorkDepartment::setDepartment($authCorp->id, $department);

								$jobId = \Yii::$app->work->push(new GetUserListJob([
									'partyId'    => $departmentId,
									'fetchChild' => WorkDepartment::NOT_FETCH_CHILD,
								]));
							} catch (\Exception $e) {
								continue;
							}
						}

						\Yii::$app->work->push(new SyncWorkFollowUserJob([
							'corp'          => $authCorp,
							'need_external' => false,
						]));
					}
				}
			} catch (InvalidDataException $e) {
				return false;
			}
		}
	}