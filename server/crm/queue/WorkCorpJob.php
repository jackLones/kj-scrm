<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2020/1/3
	 * Time: 18:21
	 */

	namespace app\queue;

	use app\models\WorkCorp;
	use app\models\WorkCorpAuth;
	use app\models\WorkSuiteConfig;
	use app\util\DateUtil;
	use app\util\SUtils;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;

	class WorkCorpJob extends BaseObject implements JobInterface
	{
		public $xml;

		public function execute ($queue)
		{
			$corpInfo = SUtils::Xml2Array($this->xml);
			SUtils::arrayCase($corpInfo);

			$workSuiteConfig = WorkSuiteConfig::findOne(['suite_id' => $corpInfo['suiteid']]);

			if (empty($workSuiteConfig)) {
				return false;
			}

			try {
				if ($corpInfo['infotype'] == WorkCorpAuth::CREATE_AUTH) {
					$authCode = $corpInfo['authcode'];

					$permanentInfo = WorkSuiteConfig::getPermanentCode($authCode, $workSuiteConfig->id);
				} elseif ($corpInfo['infotype'] == WorkCorpAuth::CHANGE_AUTH) {
					$authCorp = WorkCorp::findOne(['corpid' => $corpInfo['authcorpid']]);

					if (empty($authCorp)) {
						return false;
					}

					$corpAuthInfo = WorkCorpAuth::findOne(['suite_id' => $workSuiteConfig->id, 'corp_id' => $authCorp->id]);

					if (empty($corpAuthInfo)) {
						$corpAuthInfo              = new WorkCorpAuth();
						$corpAuthInfo->suite_id    = $workSuiteConfig->id;
						$corpAuthInfo->corp_id     = $authCorp->id;
						$corpAuthInfo->create_time = DateUtil::getCurrentTime();

						if (!$corpAuthInfo->validate() || !$corpAuthInfo->save()) {
							\Yii::error(SUtils::modelError($corpAuthInfo), 'WorkCorpJob-CHANGE_AUTH');

							return false;
						}
					}

					$corpAuthInfo->refreshCorp();
					$corpAuthInfo->auth_type = WorkCorpAuth::CHANGE_AUTH;
					$corpAuthInfo->update();
				}
			} catch (\Exception $e) {
				\Yii::error($e->getMessage(), 'WorkCorpJob-E');

				return false;
			}
		}

	}