<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2019/9/19
	 * Time: 14:56
	 */

	namespace app\components;

	use app\models\SubUser;
	use app\models\User;
	use yii\filters\auth\HttpBearerAuth;
	use yii\web\Request;
	use yii\web\Response;
	use yii\web\UnauthorizedHttpException;
	use yii\web\User as WebUser;

	class ApiCrmHttpBearerAuth extends HttpBearerAuth
	{
		/**
		 * {@inheritdoc}
		 *
		 * @param WebUser  $user
		 * @param Request  $request
		 * @param Response $response
		 *
		 * @return \yii\web\IdentityInterface|null
		 * @throws UnauthorizedHttpException
		 */
		public function authenticate ($user, $request, $response)
		{
			$authHeader = $request->getHeaders()->get('Authorization');
			if ($authHeader !== NULL && preg_match("/^Bearer\\s+(.*?)$/", $authHeader, $matches)) {
				$userData    = [];
				$userType    = User::USER_TYPE;
				$accessToken = '';

				$accessTokenData = explode('-', base64_decode($matches[1]));

				if (count($accessTokenData) > 1) {
					$userType    = $accessTokenData[0];
					$accessToken = $accessTokenData[1];
				}

				if ($userType == User::USER_TYPE) {
					$userData = User::findIdentityByAccessToken($accessToken);
				} elseif ($userType == SubUser::USER_TYPE) {
					$userData = SubUser::findIdentityByAccessToken($accessToken);
				}

				if (!empty($userData)) {
					$loginUser   = $user;
					$needRefresh = false;

					if ($userType == User::USER_TYPE) {
						$needRefresh = $userData->uid != $user->getId();
						\Yii::$app->subUser->logout();
					} elseif ($userType == SubUser::USER_TYPE) {
						\Yii::$app->user->logout();
						$needRefresh = $userData->sub_id != $user->getId();
					}

					if ($needRefresh) {
						$loginUser->logout();

						if ($userType == SubUser::USER_TYPE) {
							$loginUser = \Yii::$app->subUser;
						}

						if ($userData->access_token_expire > time()) {
							$identity = $loginUser->loginByAccessToken($accessToken, get_class($this));

							if (empty($identity)) {
								$this->handleFailure($response);
							}

							$identity->refreshTokenExpire();

							return $identity;
						} else {
							throw new UnauthorizedHttpException('access_token has time out.');
						}
					}

					if ($loginUser->identity->getAuthExpire() <= time()) {
						throw new UnauthorizedHttpException('access_token has time out.');
					}

					$loginUser->identity->refreshTokenExpire();

					return $loginUser->identity;
				} else {
					throw new UnauthorizedHttpException('access_token has time out.');
				}
			}

			return NULL;
		}
	}