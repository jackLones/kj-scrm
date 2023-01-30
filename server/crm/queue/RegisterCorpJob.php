<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2020/12/24
	 * Time: 17:00
	 */

	namespace app\queue;

	use app\models\WorkProviderConfig;
	use app\models\WorkProviderTemplate;
	use app\models\WorkRegisterCode;
	use app\util\SUtils;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;

	class RegisterCorpJob extends BaseObject implements JobInterface
	{
		public $xml;

		/**
		 * @param \yii\queue\Queue $queue
		 *
		 * @return false|mixed|void
		 */
		public function execute ($queue)
		{
			$registerCorpInfoData = SUtils::Xml2Array($this->xml);
			SUtils::arrayCase($registerCorpInfoData);

			$serviceCorpId = $registerCorpInfoData['servicecorpid'];

			if (empty($serviceCorpId)) {
				return false;
			}

			$workProviderConfig = WorkProviderConfig::findOne(['provider_corpid' => $serviceCorpId]);
			if (empty($workProviderConfig) || empty($workProviderConfig->workProviderTemplates)) {
				return false;
			}

			$templateId = $registerCorpInfoData['templateid'];
			if (empty($templateId)) {
				return false;
			}
			$templateInfo = WorkProviderTemplate::findOne(['id' => $templateId, 'provider_id' => $workProviderConfig->id, 'status' => WorkProviderTemplate::TEMPLATE_OPEN]);

			if (empty($templateInfo)) {
				return false;
			}

			$registerCode = $registerCorpInfoData['registercode'];
			$state        = $registerCorpInfoData['state'];

			$registerCodeInfo = WorkRegisterCode::findOne(['template_id' => $templateInfo->id, 'register_code' => $registerCode, 'state' => $state]);
			if (empty($registerCodeInfo)) {
				return false;
			}

			$registerInfo           = [];
			$registerInfo['corpid'] = $registerCorpInfoData['authcorpid'];
			if (!empty($registerCorpInfoData['contactsync']) && !empty($registerCorpInfoData['contactsync']['accesstoken'])) {
				if (empty($registerInfo['contact_sync'])) {
					$registerInfo['contact_sync'] = [];
				}

				$registerInfo['contact_sync']['access_token'] = $registerCorpInfoData['contactsync']['accesstoken'];
			}
			if (!empty($registerInfo['contactsync']) && !empty($registerInfo['contactsync']['expiresin'])) {
				if (empty($registerInfo['contact_sync'])) {
					$registerInfo['contact_sync'] = [];
				}

				$registerInfo['contact_sync']['expires_in'] = $registerInfo['contactsync']['expiresin'];
			}
			if (!empty($registerInfo['authuserinfo']) && !empty($registerInfo['authuserinfo']['userid'])) {
				if (empty($registerInfo['auth_user_info'])) {
					$registerInfo['auth_user_info'] = [];
				}

				$registerInfo['auth_user_info']['userid'] = $registerInfo['authuserinfo']['userid'];
			}
			try {
				$registerCodeInfo->setData($registerInfo);
			} catch (\Exception $e) {
				\Yii::error($e->getMessage(), __CLASS__ . '-' . __FUNCTION__ . ':setData');

				return false;
			}
		}
	}