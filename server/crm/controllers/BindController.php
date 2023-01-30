<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2019-09-07
	 * Time: 17:28
	 */

	namespace app\controllers;

	use app\components\ForbiddenException;
	use app\components\InvalidParameterException;
	use app\controllers\common\BaseController;
	use app\models\State;
	use app\models\UserAuthorRelation;
	use app\models\WxAuthorize;
	use app\models\WxAuthorizeInfo;
	use app\util\apiOauth;
	use app\util\StringUtil;
	use yii\db\Expression;
	use yii\helpers\Url;

	class BindController extends BaseController
	{
		/**
		 * 公众号和小程序的授权
		 *
		 * @param cnf_id 可选 string 授权平台ID
		 * @param uid 必选 string 用户ID
		 * @param state_id 可选 int 回调ID（须与%26nbsp;redirect_uri%26nbsp;至少存在一个）
		 * @param redirect_uri 可选 string 授权回调地址（须与%26nbsp;state_id%26nbsp;至少存在一个）
		 * @param auth_type 可选 int 授权模式（1则商户扫码后，手机端仅展示公众号、2表示仅展示小程序、3表示公众号和小程序都展示。如果为未制定，则默认公众号。）
		 *
		 * @return string
		 * @throws InvalidParameterException
		 * @throws \app\components\NotAllowException
		 */
		public function actionIndex ()
		{
			$this->layout = 'redirect';

			$stateId  = isset($_GET['state_id']) ? trim($_GET['state_id']) : '';
			$configId = isset($_GET['cnf_id']) ? trim($_GET['cnf_id']) : 1;
			$uid      = isset($_GET['uid']) ? trim($_GET['uid']) : '';
			$state    = isset($_GET['redirect_uri']) ? trim($_GET['redirect_uri']) : '';
			$authType = isset($_GET['auth_type']) ? trim($_GET['auth_type']) : apiOauth::BIND_WECHAT;

			if (empty($uid)) {
				throw new InvalidParameterException('缺少必要参数：uid');
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

			$headers = \Yii::$app->response->getHeaders();
			$headers->set('Referer', [Url::to('', true)]);

			$apiOauth = new apiOauth($uid, $configId);

			$redirectUri = Url::to(['bind/oauth-back', 'uid' => $uid, 'stateId' => $stateId, 'cnf_id' => $configId], true);
			$oauthUrl    = $apiOauth->startAuthorization($redirectUri, $authType);

			return $this->render('@app/views/site/redirect', [
				'redirectUrl' => $oauthUrl,
			]);
		}

		/**
		 * @throws ForbiddenException
		 * @throws InvalidParameterException
		 * @throws \app\components\InvalidDataException
		 * @throws \app\components\NotAllowException
		 */
		public function actionOauthBack ()
		{
			if (!isset($_GET['uid']) || !isset($_GET['auth_code']) || !isset($_GET['expires_in']) || !isset($_GET['stateId'])) {
				throw new InvalidParameterException('授权错误！');
			}

			$uid       = $_GET['uid'];
			$authCode  = $_GET['auth_code'];
			$expiresIn = $_GET['expires_in'];
			$stateId   = $_GET['stateId'];
			$configId  = $_GET['cnf_id'];

			$apiOauth          = new apiOauth($uid, $configId);
			$authorizationInfo = $apiOauth->getAuthorizationInfo($authCode);

			if ($authorizationInfo['status']) {
				$authorizationInfoId = $authorizationInfo['authorization_id'];
				$authorizationInfo   = WxAuthorize::findOne(['author_id' => $authorizationInfoId]);

				$appid          = $authorizationInfo->authorizer_appid;
				$authorizerInfo = WxAuthorizeInfo::getAuthorizerInfo($appid, $uid, true);

				if (in_array($authorizerInfo->verify_type_info, [-1, 1, 2])) {
					UserAuthorRelation::deleteAll(['uid' => $uid, 'author_id' => $authorizationInfoId]);

					throw new ForbiddenException(($authorizerInfo->auth_type == WxAuthorizeInfo::AUTH_TYPE_APP ? '公众号' : '小程序') . '尚未认证，无法使用系统');
				}

				if (!empty($authorizerInfo)) {
					$stateInfo   = State::findOne(['id' => $stateId]);
					$redirectUrl = $stateInfo->redirect_url;

					if (!strpos($redirectUrl, '?')) {
						$redirectUrl .= '?auth_appid=' . $appid . '&uname=' . $authorizerInfo->user_name;
					} else {
						$redirectUrl .= '&auth_appid=' . $appid . '&uname=' . $authorizerInfo->user_name;
					}
					$this->redirect($redirectUrl);
				} else {
					throw new InvalidParameterException('授权错误！');
				}
			} else {
				throw new InvalidParameterException('授权错误！');
			}
		}
	}