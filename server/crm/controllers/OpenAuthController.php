<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2019-09-07
	 * Time: 17:23
	 */

	namespace app\controllers;

	use app\controllers\common\BaseController;
	use app\models\WxAuthorize;
	use app\models\WxAuthorizeConfig;
	use app\util\DateUtil;
	use callmez\wechat\sdk\components\MessageCrypt;

	class OpenAuthController extends BaseController
	{
		const COMPONENT_VERIFY_TICKET = 'component_verify_ticket';

		/**
		 * @param $action
		 *
		 * @return bool
		 * @throws \yii\web\BadRequestHttpException
		 */
		public function beforeAction ($action)
		{
			defined('YII_BEGIN_NORMAL_TIME') or define('YII_BEGIN_NORMAL_TIME', time());

			$this->enableCsrfValidation = false;

			return parent::beforeAction($action);
		}

		public function actionIndex ()
		{
			$encryptMsg = file_get_contents("php://input");

			\Yii::info($encryptMsg);

			if (!empty($encryptMsg)) {
				/*
				 * 返回示例
				 * <xml>
				 * <AppId><![CDATA[wxc2a93796105b0cd4]]></AppId>
				 * <Encrypt><![CDATA[Yo5y0wxFucEvFmjM7CxytXGBhBiMr9z/U2Pm**************************Njf/E+307WjVuGpA==]]></Encrypt>
				 * </xml>
				 */

				$xml = new \DOMDocument();
				$xml->loadXML($encryptMsg);

				// 判断是否为第三方平台安全TICKET
				$appIdArray = $xml->getElementsByTagName("AppId");
				$appid      = $appIdArray->item(0)->nodeValue;

				$postData = '';

				$wxAuthorizeData = WxAuthorizeConfig::findOne(['appid' => $appid]);

				// 对返回的第三方平台安全TICKET进行解密
				$messageCrypt = new MessageCrypt($wxAuthorizeData->token, $wxAuthorizeData->encode_aes_key, $wxAuthorizeData->appid);
				$decryptCode  = $messageCrypt->decryptMsg($_GET['msg_signature'], $_GET['timestamp'], $_GET['nonce'], $encryptMsg, $postData, true);

				/*
				 * 解密失败返回错误代码
				 *
				 * 解密成功返回示例
				 *
				 * 授权
				 * Array
				 * (
				 *      [0] => 0
				 *      [1] => <xml>
				 *              <AppId>第三方平台appid</AppId>
				 *              <CreateTime>1413192760</CreateTime>
				 *              <InfoType>authorized</InfoType>
				 *              <AuthorizerAppid>公众号appid</AuthorizerAppid>
				 *              <AuthorizationCode>授权码（code）</AuthorizationCode>
				 *              <AuthorizationCodeExpiredTime>过期时间</AuthorizationCodeExpiredTime>
				 *              <PreAuthCode>预授权码</PreAuthCode>
				 *             </xml>
				 * )
				 *
				 * 取消授权
				 * Array
				 * (
				 *      [0] => 0
				 *      [1] => <xml>
				 *              <AppId>第三方平台appid</AppId>
				 *              <CreateTime>1413192760</CreateTime>
				 *              <InfoType>unauthorized</InfoType>
				 *              <AuthorizerAppid>公众号appid</AuthorizerAppid>
				 *             </xml>
				 * )
				 *
				 * 更新授权
				 * Array
				 * (
				 *      [0] => 0
				 *      [1] => <xml>
				 *              <AppId>第三方平台appid</AppId>
				 *              <CreateTime>1413192760</CreateTime>
				 *              <InfoType>updateauthorized</InfoType>
				 *              <AuthorizerAppid>公众号appid</AuthorizerAppid>
				 *              <AuthorizationCode>授权码（code）</AuthorizationCode>
				 *              <AuthorizationCodeExpiredTime>过期时间</AuthorizationCodeExpiredTime>
				 *              <PreAuthCode>预授权码</PreAuthCode>
				 *             </xml>
				 * )
				 *
				 * 自动推送
				 * Array
				 * (
				 *      [0] => 0
				 *      [1] => <xml>
				 *                <AppId><![CDATA[wxc2a93796105b0cd4]]></AppId>
				 *                <CreateTime>1471593718</CreateTime>
				 *                <InfoType><![CDATA[component_verify_ticket]]></InfoType>
				 *                <ComponentVerifyTicket><![CDATA[ticket@@@XYBRoTPsDXdKUewfhDpMoZl5QNA2Vdg_E7TyYKQmnZWXTzWLOy1A7vOZNIimav0TpbphFG1rzqNn8a3nvxi1uA]]></ComponentVerifyTicket>
				 *             </xml>
				 * )
				 */
				if ($decryptCode == 0) {
					$msg = $postData;
					$xml = new \DOMDocument();
					$xml->loadXML($msg);

					// 判断是否为第三方平台安全TICKET
					$infoTypeArray = $xml->getElementsByTagName("InfoType");
					$infoType      = $infoTypeArray->item(0)->nodeValue;

					switch ($infoType) {
						// 授权成功通知
						case WxAuthorize::AUTH_TYPE_AUTH:
							$authorizerAppidArray = $xml->getElementsByTagName("AuthorizerAppid");
							$authorizerAppid      = $authorizerAppidArray->item(0)->nodeValue;
							$authorization        = WxAuthorize::findOne(['config_id' => $wxAuthorizeData->id, 'authorizer_appid' => $authorizerAppid]);

							if (!empty($authorization)) {
								$authorizationCodeArray = $xml->getElementsByTagName("AuthorizationCode");
								$authorizationCode      = $authorizationCodeArray->item(0)->nodeValue;

								$now                          = YII_BEGIN_NORMAL_TIME;
								$authorizationCodeExpireArray = $xml->getElementsByTagName("AuthorizationCodeExpiredTime");
								$authorizationCodeExpire      = $authorizationCodeExpireArray->item(0)->nodeValue;
								$authorizationCodeExpire      += $now;

								$preAuthCodeArray = $xml->getElementsByTagName("PreAuthCode");
								$preAuthCode      = $preAuthCodeArray->item(0)->nodeValue;

								$authorization->authorizer_type         = WxAuthorize::AUTH_TYPE_AUTH;
								$authorization->authorizer_code         = $authorizationCode;
								$authorization->authorizer_code_expires = (string) $authorizationCodeExpire;
								$authorization->pre_auth_code           = $preAuthCode;
								$authorization->update();
							}

							echo "SUCCESS";

							break;
						// 取消授权通知
						case WxAuthorize::AUTH_TYPE_UNAUTH:
							$authorizerAppidArray = $xml->getElementsByTagName("AuthorizerAppid");
							$authorizerAppid      = $authorizerAppidArray->item(0)->nodeValue;
							$authorization        = WxAuthorize::findOne(['config_id' => $wxAuthorizeData->id, 'authorizer_appid' => $authorizerAppid]);

							if (!empty($authorization)) {
								$authorization->authorizer_type         = WxAuthorize::AUTH_TYPE_UNAUTH;
								$authorization->authorizer_code         = '';
								$authorization->authorizer_code_expires = '';
								$authorization->update();
							}

							echo "SUCCESS";

							break;
						// 授权更新通知
						case WxAuthorize::AUTH_TYPE_UPDATEAUTH:
							$authorizerAppidArray = $xml->getElementsByTagName("AuthorizerAppid");
							$authorizerAppid      = $authorizerAppidArray->item(0)->nodeValue;
							$authorization        = WxAuthorize::findOne(['config_id' => $wxAuthorizeData->id, 'authorizer_appid' => $authorizerAppid]);

							if (!empty($authorization)) {
								$authorizationCodeArray = $xml->getElementsByTagName("AuthorizationCode");
								$authorizationCode      = $authorizationCodeArray->item(0)->nodeValue;

								$now                          = YII_BEGIN_NORMAL_TIME;
								$authorizationCodeExpireArray = $xml->getElementsByTagName("AuthorizationCodeExpiredTime");
								$authorizationCodeExpire      = $authorizationCodeExpireArray->item(0)->nodeValue;
								$authorizationCodeExpire      += $now;

								$preAuthCodeArray = $xml->getElementsByTagName("PreAuthCode");
								$preAuthCode      = $preAuthCodeArray->item(0)->nodeValue;

								$authorization->authorizer_type         = WxAuthorize::AUTH_TYPE_UPDATEAUTH;
								$authorization->authorizer_code         = $authorizationCode;
								$authorization->authorizer_code_expires = (string) $authorizationCodeExpire;
								$authorization->pre_auth_code           = $preAuthCode;
								$authorization->update();
							}

							echo "SUCCESS";

							break;
						// 对返回的第三方平台安全TICKET进行解密
						case static::COMPONENT_VERIFY_TICKET:
							$componentVerifyTicketArray = $xml->getElementsByTagName("ComponentVerifyTicket");
							$componentVerifyTicket      = $componentVerifyTicketArray->item(0)->nodeValue;

							$wxAuthorizeData->component_verify_ticket = $componentVerifyTicket;
							$wxAuthorizeData->update_time             = DateUtil::getCurrentTime();
							if ($wxAuthorizeData->update()) {
								echo "SUCCESS";
							} else {

								echo "FAILED";
							}

							break;
						default:

							echo "FAILED";

							break;
					}
				} else {
					echo $decryptCode;
				}
			}
		}
	}