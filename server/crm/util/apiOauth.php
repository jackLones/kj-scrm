<?php
	/**
	 * Created by PhpStorm.
	 * User: Dove
	 * Date: 2019-09-07
	 * Time: 13:47
	 */

	namespace app\util;

	use app\components\InvalidParameterException;
	use app\components\NotAllowException;
	use app\models\WxAuthorize;
	use app\models\WxAuthorizeConfig;

	class apiOauth
	{
		const BIND_WECHAT = 1;
		const BIND_MINIAPP = 2;
		const BIND_ALL = 3;

		const API_COMPONENT_TOKEN = 'https://api.weixin.qq.com/cgi-bin/component/api_component_token';
		const API_CREATE_PRE_AUTH_CODE = 'https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode?component_access_token=';
		const API_QUERY_AUTH = 'https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token=';
		const API_AUTHORIZER_TOKEN = 'https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token=';
		const API_GET_AUTHORIZER_INFO = 'https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info?component_access_token=';
		const API_GET_AUTHORIZER_OPTION = 'https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_option?component_access_token=';
		const API_SET_AUTHORIZER_OPTION = 'https://api.weixin.qq.com/cgi-bin/component/api_set_authorizer_option?component_access_token=';
		const API_COMPONENT_LOGIN_PAGE = 'https://mp.weixin.qq.com/cgi-bin/componentloginpage';
		const API_FAST_REGISTER_AUTH = 'https://mp.weixin.qq.com/cgi-bin/fastregisterauth';
		const API_MINI_GET_SESSION_KEY = 'https://api.weixin.qq.com/sns/component/jscode2session';

		public $uid;
		public $configId;
		public $authorizeAppid;
		public $wxAuthorizeConfig;
		public $appId;
		public $appSecret;
		public $appToken;
		public $encodeAesKey;
		public $componentVerifyTicket;
		public $componentAccessToken;
		public $componentAccessTokenExpires;
		public $preAuthCode;
		public $preAuthCodeExpires;
		public $oauthData;

		/**
		 * 初始化方法
		 *
		 * @param     $uid
		 * @param int $configId
		 *
		 * @throws InvalidParameterException
		 */
		public function __construct ($uid, $configId = 1, $authorizeAppid = '')
		{
			defined('YII_BEGIN_NORMAL_TIME') or define('YII_BEGIN_NORMAL_TIME', time());

			$this->uid            = $uid;
			$this->configId       = $configId;
			$this->authorizeAppid = $authorizeAppid;

			if (empty($this->uid)) {
				throw new InvalidParameterException('绑定类初始化失败!');
			}

			$this->wxAuthorizeConfig = WxAuthorizeConfig::findOne(['id' => $this->configId]);

			if (empty($this->wxAuthorizeConfig)) {
				throw new InvalidParameterException('绑定类初始化失败！');
			}

			$this->appId                       = $this->wxAuthorizeConfig->appid;
			$this->appSecret                   = $this->wxAuthorizeConfig->appSecret;
			$this->appToken                    = $this->wxAuthorizeConfig->token;
			$this->encodeAesKey                = $this->wxAuthorizeConfig->encode_aes_key;
			$this->componentVerifyTicket       = $this->wxAuthorizeConfig->component_verify_ticket;
			$this->componentAccessToken        = $this->wxAuthorizeConfig->component_access_token;
			$this->componentAccessTokenExpires = $this->wxAuthorizeConfig->component_access_token_expires;
			$this->preAuthCode                 = $this->wxAuthorizeConfig->pre_auth_code;
			$this->preAuthCodeExpires          = $this->wxAuthorizeConfig->pre_auth_code_expires;
		}

		/**
		 * @param      $url
		 * @param null $data
		 *
		 * @return array|mixed
		 */
		public function https_request ($url, $data = NULL)
		{
			$ch     = curl_init();
			$header = ["Accept-Charset: utf-8"];
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

			if (!empty($data)) {
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			}

			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

			$output  = curl_exec($ch);
			$errorNo = curl_errno($ch);

			curl_close($ch);

			if ($errorNo) {
				return ['curl' => false, 'errorno' => $errorNo];
			} else {
				$res = json_decode($output, 1);

				if (isset($res['errcode'])) {
					\Yii::error(__METHOD__ . '_cashier_token:' . $this->uid);
					\Yii::error(__METHOD__ . '_errorcode:' . $res['errcode']);
					\Yii::error(__METHOD__ . '_errmsg:' . $res['errmsg']);

					return ['errcode' => $res['errcode'], 'errmsg' => $res['errmsg']];
				} else {
					$res['errcode'] = 0;

					return $res;
				}
			}
		}

		/**
		 * @param $funcInfo
		 *
		 * @return string
		 */
		public function getFuncInfo ($funcInfo)
		{
			$func = [];
			foreach ($funcInfo as $funcCate) {
				array_push($func, $funcCate['funcscope_category']['id']);
			}

			$funcString = implode(',', $func);

			return $funcString;
		}

		/**
		 * 获取第三方平台component_access_token
		 *
		 * @return mixed|string
		 * @throws NotAllowException
		 */
		public function getComponentAccessToken ()
		{
			if ($this->componentAccessToken == '' || $this->componentAccessTokenExpires == '' || $this->componentAccessTokenExpires < time()) {
				$data
					= '{
					"component_appid":"' . $this->appId . '" ,
					"component_appsecret": "' . $this->appSecret . '",
					"component_verify_ticket": "' . $this->componentVerifyTicket . '"
				}';

				$res = $this->https_request(static::API_COMPONENT_TOKEN, $data);

				if ($res['errcode'] > 0) {
					throw new NotAllowException('获取component_access_token错误:' . $res['errcode']);

				} else {
					$this->componentAccessToken        = $res['component_access_token'];
					$this->componentAccessTokenExpires = YII_BEGIN_NORMAL_TIME + $res['expires_in'];

					$this->wxAuthorizeConfig->component_access_token         = $this->componentAccessToken;
					$this->wxAuthorizeConfig->component_access_token_expires = (string) $this->componentAccessTokenExpires;
					$this->wxAuthorizeConfig->save();
				}
			}

			return $this->componentAccessToken;
		}

		/**
		 * 获取预授权码pre_auth_code
		 *
		 * @return mixed|string
		 * @throws NotAllowException
		 */
		public function getPreAuthCode ()
		{
			if ($this->preAuthCode == '' || $this->preAuthCodeExpires == '' || $this->preAuthCodeExpires < time()) {
				$accessToken = $this->getComponentAccessToken();

				$url = static::API_CREATE_PRE_AUTH_CODE . $accessToken;

				$data
					= '{
					"component_appid":"' . $this->appId . '"
				}';

				$res = $this->https_request($url, $data);

				if ($res['errcode'] > 0) {
					throw new NotAllowException('获取pre_auth_code错误:' . $res['errcode']);
				} else {
					$this->preAuthCode        = $res['pre_auth_code'];
					$this->preAuthCodeExpires = YII_BEGIN_NORMAL_TIME + $res['expires_in'];

					$this->wxAuthorizeConfig->pre_auth_code         = $this->preAuthCode;
					$this->wxAuthorizeConfig->pre_auth_code_expires = (string) $this->preAuthCodeExpires;
					$this->wxAuthorizeConfig->save();
				}
			}

			return $this->preAuthCode;
		}

		/**
		 * 使用授权码换取公众号或小程序的接口调用凭据和授权信息
		 *
		 * @param      $authorizationCode
		 * @param bool $allResponse
		 *
		 * @return array
		 * @throws NotAllowException
		 * @throws \app\components\InvalidDataException
		 */
		public function getAuthorizationInfo ($authorizationCode, $allResponse = false)
		{
			$result = [
				'status'           => false,
				'authorization_id' => '',
			];

			if (empty($authorizationCode)) {
				$result['msg'] = '授权code不能为空！';

				return $result;
			}

			$accessToken = $this->getComponentAccessToken();

			$url = static::API_QUERY_AUTH . $accessToken;

			$data
				= '{
				"component_appid":"' . $this->appId . '" ,
				"authorization_code": "' . $authorizationCode . '"
			}';

			$res = $this->https_request($url, $data);

			if ($res['errcode'] > 0) {
				$result['msg'] = '获取authorization_info错误:' . $res['errcode'];
			} else {
				$result['status'] = true;

				if ($allResponse) {
					$result['authorizer_access_token'] = $res['authorization_info']['authorizer_access_token'];
				} else {
					$authorizeData = [
						'authorizer_appid'                => $res['authorization_info']['authorizer_appid'],
						'authorizer_access_token'         => $res['authorization_info']['authorizer_access_token'],
						'authorizer_access_token_expires' => YII_BEGIN_NORMAL_TIME + $res['authorization_info']['expires_in'],
						'authorizer_refresh_token'        => $res['authorization_info']['authorizer_refresh_token'],
						'func_info'                       => $this->getFuncInfo($res['authorization_info']['func_info']),
					];

					$authorizationId = WxAuthorize::setAuthorizeData($this->uid, $this->configId, $authorizeData);

					$result['status']           = true;
					$result['authorization_id'] = $authorizationId;
				}
			}

			return $result;
		}

		/**
		 * 获取（刷新）授权公众号或小程序的接口调用凭据（令牌）
		 *
		 * @return array
		 * @throws NotAllowException
		 * @throws \app\components\InvalidDataException
		 */
		public function getAuthorizationAccessToken ()
		{
			$result = [
				'status'                  => false,
				'authorizer_access_token' => '',
			];

			if (empty($this->authorizeAppid)) {
				$authorize = WxAuthorize::find()->alias('wxAuth')->rightJoin('{{%user_author_relation}} relation', '`relation`.`author_id` = `wxAuth`.`author_id`')->where(['relation.uid' => $this->uid, 'wxAuth.config_id' => $this->configId])->andWhere(['or', ['<>', 'wxAuth.authorizer_type', 'wxAuth.unauthorized'], ['wxAuth.authorizer_type' => NULL]])->one();
				/*$relation = UserAuthorRelation::findOne(['uid' => $this->uid]);
				$authorize = WxAuthorize::find()->where(['author_id' => $relation->author_id, 'config_id' => $this->configId])->andWhere(['or', ['<>', 'authorizer_type', 'unauthorized'], ['authorizer_type' => NULL]])->one();*/
			} else {
				$authorize = WxAuthorize::find()->where(['authorizer_appid' => $this->authorizeAppid])->andWhere(['or', ['<>', 'authorizer_type', 'unauthorized'], ['authorizer_type' => NULL]])->one();
			}

			if (!empty($authorize)) {
				$authorizerAccessToken        = $authorize->authorizer_access_token;
				$authorizerAccessTokenExpires = $authorize->authorizer_access_token_expires;

				if (empty($authorizerAccessToken) || $authorizerAccessTokenExpires < time()) {
					$accessToken = $this->getComponentAccessToken();

					$authorizerAppId        = $authorize->authorizer_appid;
					$authorizerRefreshToken = $authorize->authorizer_refresh_token;

					$url = static::API_AUTHORIZER_TOKEN . $accessToken;

					$data
						= '{
						"component_appid":"' . $this->appId . '",
						"authorizer_appid":"' . $authorizerAppId . '",
						"authorizer_refresh_token":"' . $authorizerRefreshToken . '"
					}';

					$res = $this->https_request($url, $data);

					if ($res['errcode'] > 0) {
						$result['msg'] = '刷新authorizer_access_token错误：' . $res['errcode'];
					} else {
						$authorizeData = [
							'authorizer_appid'                => $authorizerAppId,
							'authorizer_access_token'         => $res['authorizer_access_token'],
							'authorizer_access_token_expires' => YII_BEGIN_NORMAL_TIME + $res['expires_in'],
							'authorizer_refresh_token'        => $res['authorizer_refresh_token'],
						];

						if (WxAuthorize::setAuthorizeData($this->uid, $this->configId, $authorizeData)) {
							$result['status']                  = true;
							$result['authorizer_access_token'] = $res['authorizer_access_token'];
						}
					}
				} else {
					$result['status']                  = true;
					$result['authorizer_access_token'] = $authorizerAccessToken;
				}
			}

			return $result;
		}

		/**
		 * 获取授权方的帐号基本信息
		 *
		 * @param $authAppid
		 *
		 * @return array|mixed
		 * @throws NotAllowException
		 */
		public function getAuthorizerInfo ($authAppid)
		{
			$accessToken = $this->getComponentAccessToken();

			$url = static::API_GET_AUTHORIZER_INFO . $accessToken;

			$data
				= '{
				"component_appid":"' . $this->appId . '",
				"authorizer_appid": "' . $authAppid . '"
			}';

			$res = $this->https_request($url, $data);

			return $res;
		}

		/**
		 * 获取授权方的选项设置信息
		 *
		 * @param $authAppid
		 * @param $optionName
		 *
		 * @return array|mixed
		 * @throws NotAllowException
		 */
		public function getAuthoirzerOption ($authAppid, $optionName)
		{
			$accessToken = $this->getComponentAccessToken();

			$url = static::API_GET_AUTHORIZER_OPTION . $accessToken;

			$data
				= '{
				"component_appid":"' . $this->appId . '",
				"authorizer_appid":"' . $authAppid . '",
				"option_name": "' . $optionName . '"
			}';

			$res = $this->https_request($url, $data);

			return $res;
		}

		/**
		 * 设置授权方的选项信息
		 *
		 * @param $authAppid
		 * @param $optionName
		 * @param $optionValue
		 *
		 * @return array|mixed
		 * @throws NotAllowException
		 */
		public function setAuthoirzerOption ($authAppid, $optionName, $optionValue)
		{
			$accessToken = $this->getComponentAccessToken();

			$url = static::API_SET_AUTHORIZER_OPTION . $accessToken;

			$data
				= '{
				"component_appid":"' . $this->appId . '",
				"authorizer_appid":"' . $authAppid . '",
				"option_name": "' . $optionName . '",
				"option_value": "' . $optionValue . '"
			}';

			$res = $this->https_request($url, $data);

			return $res;
		}

		/**
		 * 引入用户进入授权页
		 *
		 * @param     $redirectUri
		 * @param int $authType
		 *
		 * @return string
		 * @throws NotAllowException
		 */
		public function startAuthorization ($redirectUri, $authType = 1)
		{
			$preAuthCode = $this->getPreAuthCode();

			$this->wxAuthorizeConfig->pre_auth_code         = '';
			$this->wxAuthorizeConfig->pre_auth_code_expires = '';
			$this->wxAuthorizeConfig->save();

			$url = static::API_COMPONENT_LOGIN_PAGE . '?component_appid=' . $this->appId . '&pre_auth_code=' . $preAuthCode . '&redirect_uri=' . urlencode($redirectUri) . '&auth_type=' . $authType;

			return $url;
		}

		/**
		 * code 换取 session_key
		 *
		 * @param $jsCode
		 *
		 * @return string
		 * @throws NotAllowException
		 */
		public function getSessionKeyUrl ($jsCode)
		{
			$accessToken = $this->getComponentAccessToken();

			$url = static::API_MINI_GET_SESSION_KEY . '?appid=' . $this->authorizeAppid . '&js_code=' . $jsCode . '&grant_type=authorization_code&component_appid=' . $this->appId . '&component_access_token=' . $accessToken;

			return $url;
		}

		/**
		 * 从第三方平台跳转至微信公众平台授权注册页面
		 *
		 * @param     $redirectUri
		 * @param int $copyWxVerify
		 *
		 * @return string
		 */
		public function fastRegisterAuth ($redirectUri, $copyWxVerify = 1)
		{
			$url = static::API_FAST_REGISTER_AUTH . '?component_appid=' . $this->appId . '&appid=' . $this->authorizeAppid . '&copy_wx_verify=' . $copyWxVerify . '&redirect_uri=' . urlencode($redirectUri);

			return $url;
		}
	}