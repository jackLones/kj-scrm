<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2020/1/1
	 * Time: 17:18
	 */

	namespace app\controllers;

	use app\components\InvalidParameterException;
	use app\controllers\common\BaseController;
	use app\models\State;
	use app\models\User;
	use app\models\WorkCorp;
	use app\models\WorkSuiteConfig;
	use app\util\StringUtil;
	use app\util\WorkUtils;
	use dovechen\yii2\weWork\ServiceWork;
	use yii\db\Expression;
	use yii\helpers\Url;

	class WorkBindController extends BaseController
	{
		public function actionIndex ()
		{
			$this->layout = 'redirect';

			$stateId  = isset($_GET['state_id']) ? trim($_GET['state_id']) : '';
			$configId = isset($_GET['cnf_id']) ? trim($_GET['cnf_id']) : 1;
			$uid      = isset($_GET['uid']) ? trim($_GET['uid']) : '';
			$state    = isset($_GET['redirect_uri']) ? trim($_GET['redirect_uri']) : '';
			$authType = isset($_GET['auth_type']) ? trim($_GET['auth_type']) : 0;
			$fromType = \Yii::$app->request->get('from_type', 0);

			if ($fromType == 0 && empty($uid)) {
				throw new InvalidParameterException('缺少必要参数：uid');
			}

			if (!empty($uid)) {
				/** @var User $userData */
				$userData = User::findOne($uid);
				if (empty($userData)) {
					throw new InvalidParameterException('参数不合法！');
				}
			}

			if (empty($state) && empty($stateId)) {
				throw new InvalidParameterException('缺少必要参数：redirect_uri');
			}
			if (!preg_match("/^(http:\/\/|https:\/\/).*$/", $state)) {
				throw new InvalidParameterException('必要参数格式错误：redirect_uri');
			}

			if (empty($stateId)) {
				// create the short state
				$shortUrl    = StringUtil::randomStr(6);
				$shortPrefix = substr($shortUrl, 0, 1);

				$stateModel               = new State();
				$stateModel->short_prefix = $shortPrefix;
				$stateModel->short_url    = $shortUrl;
				$stateModel->redirect_url = $state;
				$stateModel->create_time  = new Expression('CURRENT_TIMESTAMP');
				$stateModel->save();
				$stateId = $stateModel->id;
			}

			$workSuiteConfig = WorkSuiteConfig::findOne($configId);
			if (!empty($workSuiteConfig)) {

				$headers = \Yii::$app->response->getHeaders();
				$headers->set('Referer', [Url::to('', true)]);

				/** @var ServiceWork $serviceWork */
				$serviceWork = \Yii::createObject([
					'class'        => ServiceWork::className(),
					'suite_id'     => $workSuiteConfig->suite_id,
					'suite_secret' => $workSuiteConfig->suite_secret,
					'suite_ticket' => $workSuiteConfig->suite_ticket,
				]);

				$suiteAccessTokenInfo = WorkSuiteConfig::getSuiteAccessTokenInfo($configId);

				$serviceWork->SetSuiteAccessToken($suiteAccessTokenInfo);

				$redirectUri = Url::to(['work-bind/oauth-back', 'uid' => $uid, 'stateId' => $stateId, 'cnf_id' => $configId, 'from_type' => $fromType], true);
				$oauthUrl    = $serviceWork->getAuthUrl(urlencode($redirectUri), $stateId, $authType, true);

				return $this->render('@app/views/site/redirect', [
					'redirectUrl' => $oauthUrl,
				]);
			} else {
				throw new InvalidParameterException('cnf_id 参数不正确');
			}
		}

		public function actionOauthBack ()
		{
			$fromType = \Yii::$app->request->get('from_type', 0);

			if (($fromType == 0 && !isset($_GET['uid'])) || !isset($_GET['auth_code']) || !isset($_GET['expires_in']) || !isset($_GET['stateId'])) {
				throw new InvalidParameterException('授权错误！');
			}

			$uid       = $_GET['uid'];
			$authCode  = $_GET['auth_code'];
			$expiresIn = $_GET['expires_in'];
			$stateId   = $_GET['stateId'];
			$configId  = $_GET['cnf_id'];

			$permanentInfo = WorkSuiteConfig::getPermanentCode($authCode, $configId, $uid);

			if ($permanentInfo['status']) {
				$corpId   = $permanentInfo['corp_id'];
				$corpInfo = WorkCorp::findOne($corpId);

				$stateInfo   = State::findOne(['id' => $stateId]);
				$redirectUrl = rawurldecode($stateInfo->redirect_url);

				if ($fromType == 0) {
					if (!strpos($redirectUrl, '?')) {
						$redirectUrl .= '?auth_corpid=' . $corpInfo->corpid . '&uname=' . $corpInfo->corp_name;
					} else {
						$redirectUrl .= '&auth_corpid=' . $corpInfo->corpid . '&uname=' . $corpInfo->corp_name;
					}
				} else {
					$cacheKey = md5($corpInfo->corpid);
					\Yii::$app->cache->set($cacheKey, $corpInfo->id, 86400);

					if (!strpos($redirectUrl, '?')) {
						$redirectUrl .= '?auth_corpid=' . $cacheKey;
					} else {
						$redirectUrl .= '&auth_corpid=' . $cacheKey;
					}
				}
				$this->redirect($redirectUrl);
			} else {
				throw new InvalidParameterException('授权错误！');
			}
		}
	}