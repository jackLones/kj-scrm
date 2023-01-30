<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2020/1/3
	 * Time: 21:14
	 */

	namespace app\queue;

	use app\components\InvalidDataException;
	use app\models\WorkCorp;
	use app\models\WorkCorpAgent;
	use app\models\WorkCorpAuth;
	use app\models\WorkCorpBind;
	use app\models\WorkSuiteConfig;
	use app\models\WorkTag;
	use app\util\SUtils;
	use app\util\WorkUtils;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;

	class WorkTagJob extends BaseObject implements JobInterface
	{
		public $xml;
		public $from = WorkUtils::FROM_SERVICE;

		public function execute ($queue)
		{

			$tagInfoData = SUtils::Xml2Array($this->xml);
			SUtils::arrayCase($tagInfoData);

			if ($this->from == WorkUtils::FROM_SERVICE) {
				$workSuiteConfig = WorkSuiteConfig::findOne(['suite_id' => $tagInfoData['suiteid']]);

				if (empty($workSuiteConfig)) {
					return false;
				}

				$authCorp = WorkCorp::findOne(['corpid' => $tagInfoData['authcorpid']]);

				if (empty($authCorp) || (!empty($authCorp->workCorpBind) && $authCorp->workCorpBind->book_status == WorkCorpBind::BOOK_OPEN)) {
					return false;
				}

				$authCorpAuth = WorkCorpAuth::findOne(['suite_id' => $workSuiteConfig->id, 'corp_id' => $authCorp->id]);

				if (empty($authCorpAuth)) {
					return false;
				}
			} elseif ($this->from == WorkUtils::FROM_AGENT) {
				$authCorp = WorkCorp::findOne(['corpid' => $tagInfoData['tousername']]);

				$agentInfo = WorkCorpAgent::findOne(['corp_id' => $authCorp->id, 'agent_type' => WorkCorpAgent::CUSTOM_AGENT, 'close' => WorkCorpAgent::AGENT_NOT_CLOSE, 'is_del' => WorkCorpAgent::AGENT_NO_DEL]);

				if (empty($authCorp) || empty($agentInfo)) {
					return false;
				}
			} else {
				$authCorp = WorkCorp::findOne(['corpid' => $tagInfoData['tousername']]);

				if (empty($authCorp) || empty($authCorp->workCorpBind) || $authCorp->workCorpBind->book_status == WorkCorpBind::BOOK_CLOSE) {
					return false;
				}
			}

			try {
				if ($tagInfoData['changetype'] == WorkTag::UPDATE_TAG) {
					$workTag = WorkTag::findOne(['tagid' => $tagInfoData['tagid']]);

					if (!empty($workTag)) {
						WorkTag::changeTagRelation($workTag->id, $tagInfoData);
					}
				}
			} catch (InvalidDataException $e) {
				return false;
			}
		}
	}