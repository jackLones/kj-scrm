<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2020/1/21
	 * Time: 11:04
	 */

	namespace app\util;

	use app\components\InvalidDataException;
	use app\components\InvalidParameterException;
	use app\models\SubUser;
	use app\models\User;
	use app\models\UserCorpRelation;
	use app\models\WorkCorp;
	use app\models\WorkCorpAgent;
	use app\models\WorkCorpAuth;
	use app\models\WorkCorpBind;
	use app\models\WorkExternalContact;
	use app\models\WorkExternalContactFollowUser;
	use app\models\WorkMsgAudit;
	use app\models\WorkProviderConfig;
	use app\models\WorkSuiteConfig;
	use app\models\WorkUser;
	use dovechen\yii2\weWork\ServiceProvider;
	use dovechen\yii2\weWork\ServiceWork;
	use dovechen\yii2\weWork\src\dataStructure\UserInfoByCode;
	use dovechen\yii2\weWork\Work;
	use yii\caching\Cache;
	use yii\helpers\Json;

	class WorkUtils
	{
		const BOOK_API = 0;
		const EXTERNAL_API = 1;
		const AGENT_API = 2;

		const FROM_SERVICE = 0;
		const FROM_BIND = 1;
		const FROM_AGENT = 2;

		const BATCH_GET_BY_USER = 'batch_get_by_user';

		/**
		 * @param     $authCorpId
		 * @param int $suiteId
		 *
		 * @return ServiceWork
		 *
		 * @throws InvalidDataException
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function getServiceWorkApi ($authCorpId, $suiteId = 1)
		{
			$authConfig = WorkSuiteConfig::findOne($suiteId);

			if (empty($authConfig)) {
				throw new InvalidDataException('参数不正确。');
			}

			$authCorp = WorkCorp::findOne($authCorpId);

			if (empty($authCorp)) {
				throw new InvalidDataException('参数不正确。');
			}

			$corpAuth = WorkCorpAuth::findOne(['suite_id' => $authConfig->id, 'corp_id' => $authCorp->id]);

			if (empty($corpAuth) || $corpAuth->auth_type == WorkCorpAuth::CANCEL_AUTH) {
				throw new InvalidDataException('参数不正确。');
			}

			/** @var ServiceWork $serviceWork */
			$serviceWork = \Yii::createObject([
				'class'          => ServiceWork::className(),
				'suite_id'       => $authConfig->suite_id,
				'suite_secret'   => $authConfig->suite_secret,
				'suite_ticket'   => $authConfig->suite_ticket,
				'auth_corpid'    => $authCorp->corpid,
				'permanent_code' => $corpAuth->permanent_code,
			]);

			$suiteAccessTokenInfo = WorkSuiteConfig::getSuiteAccessTokenInfo($authConfig->id);
			if (!empty($suiteAccessTokenInfo)) {
				$serviceWork->SetSuiteAccessToken($suiteAccessTokenInfo);
			}

			$accessTokenInfo = WorkCorpAuth::getTokenInfo($authCorp->id, false, $suiteId);
			if (!empty($accessTokenInfo)) {
				$serviceWork->SetAccessToken(['access_token' => $accessTokenInfo['access_token'], 'expire' => $accessTokenInfo['access_token_expires']]);
			}

			return $serviceWork;
		}

		/**
		 * @param     $authCorpId
		 * @param int $type
		 * @param int $from
		 * @param int $suiteId
		 *
		 * @return ServiceWork|Work|string
		 *
		 * @throws InvalidDataException
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function getWorkApi ($authCorpId, $type = self::BOOK_API, $from = self::FROM_BIND, $suiteId = 1)
		{
			$authCorp = WorkCorp::findOne($authCorpId);

			if (empty($authCorp)) {
				throw new InvalidDataException('参数不正确。');
			}

			$workApi = '';
			$hasBind = false;

			if ($from == self::FROM_BIND) {
				// 绑定模式
				switch ($type) {
					case static::BOOK_API:
						if (!empty($authCorp->workCorpBind) && !empty($authCorp->workCorpBind->book_secret)) {
							/** @var Work $workApi */
							$workApi = \Yii::createObject([
								'class'  => Work::className(),
								'corpid' => $authCorp->corpid,
								'secret' => $authCorp->workCorpBind->book_secret,
							]);

							$accessTokenInfo = WorkCorpBind::getBookTokenInfo($authCorp->id);
							if (!empty($accessTokenInfo)) {
								$workApi->SetAccessToken(['access_token' => $accessTokenInfo['book_access_token'], 'expire' => $accessTokenInfo['book_access_token_expires']]);
							}

							$hasBind = true;
						}

						break;
					case static::EXTERNAL_API:
						if (!empty($authCorp->workCorpBind) && !empty($authCorp->workCorpBind->external_secret)) {
							/** @var Work $workApi */
							$workApi = \Yii::createObject([
								'class'  => Work::className(),
								'corpid' => $authCorp->corpid,
								'secret' => $authCorp->workCorpBind->external_secret,
							]);

							$accessTokenInfo = WorkCorpBind::getExternalTokenInfo($authCorp->id);
							if (!empty($accessTokenInfo)) {
								$workApi->SetAccessToken(['access_token' => $accessTokenInfo['external_access_token'], 'expire' => $accessTokenInfo['external_access_token_expires']]);
							}

							$hasBind = true;
						}

						break;
					default:
						break;

				}
			}

			if (!$hasBind) {
				$hasAgent = false;
				if (!empty($authCorp->workCorpAgents)) {
					/** @var WorkCorpAgent $agentInfo */
					$agentInfo = WorkCorpAgent::find()
						->where(['corp_id' => $authCorp->id, 'agent_type' => WorkCorpAgent::CUSTOM_AGENT, 'close' => WorkCorpAgent::AGENT_NOT_CLOSE, 'is_del' => WorkCorpAgent::AGENT_NO_DEL])
						->orderBy(['id' => SORT_DESC])
						->select(['id', 'corp_id'])
						->one();

					if (!empty($agentInfo)) {
						$hasAgent = true;
						$workApi  = static::getAgentApi($agentInfo->corp_id, $agentInfo->id);
					}
				}

				if (!$hasAgent) {
					// 判断是否授权了通讯录应用
					if (!empty($authCorp->workCorpAuths)) {
						foreach ($authCorp->workCorpAuths as $workCorpAuth) {
							if ($workCorpAuth->suite_id == 2 && $workCorpAuth->auth_type != WorkCorpAuth::CANCEL_AUTH) {
								$suiteId = 2;
							}
						}
					}

					$workApi = static::getServiceWorkApi($authCorpId, $suiteId);
				}
			}

			return $workApi;
		}

		/**
		 * @param $corpId
		 * @param $agentId
		 *
		 * @return ServiceWork|Work|string
		 *
		 * @throws InvalidDataException
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function getAgentApi ($corpId, $agentId)
		{
			$workCorpAgent = WorkCorpAgent::findOne($agentId);

			if (empty($workCorpAgent) || $workCorpAgent->corp_id != $corpId) {
				throw new InvalidDataException('参数不正确。');
			}

			$agentApi = '';

			if ($workCorpAgent->agent_type == WorkCorpAgent::AUTH_AGENT) {
				$agentApi = static::getServiceWorkApi($corpId, $workCorpAgent->suite_id);
			} else {
				/** @var Work $agentApi */
				$agentApi = \Yii::createObject([
					'class'  => Work::className(),
					'corpid' => $workCorpAgent->corp->corpid,
					'secret' => $workCorpAgent->secret,
				]);

				$accessTokenInfo = WorkCorpAgent::getTokenInfo($corpId, $agentId);
				if (!empty($accessTokenInfo)) {
					$agentApi->SetAccessToken(['access_token' => $accessTokenInfo['access_token'], 'expire' => $accessTokenInfo['access_token_expires']]);
				}
			}

			return $agentApi;
		}

		/**
		 * @param int $providerId
		 *
		 * @return ServiceProvider
		 *
		 * @throws InvalidDataException
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function getProviderApi ($providerId = 1)
		{
			$workProviderConfig = WorkProviderConfig::findOne($providerId);

			if (empty($workProviderConfig)) {
				throw new InvalidDataException('参数不正确。');
			}

			/** @var ServiceProvider $serviceProvider */
			$serviceProvider = \Yii::createObject([
				'class'           => ServiceProvider::className(),
				'provider_corpid' => $workProviderConfig->provider_corpid,
				'provider_secret' => $workProviderConfig->provider_secret,
			]);

			$providerAccessTokenInfo = WorkProviderConfig::getProviderAccessTokenInfo($providerId);
			if (!empty($providerAccessTokenInfo)) {
				$serviceProvider->SetProviderAccessToken($providerAccessTokenInfo);
			}

			return $serviceProvider;
		}

		/**
		 * 获取会话存档的WorkApi
		 *
		 * @param $corpId
		 *
		 * @return Work
		 *
		 * @throws InvalidDataException
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function getMsgAuditApi ($corpId)
		{
			$workCorp = WorkCorp::findOne($corpId);

			if (empty($workCorp)) {
				throw new InvalidDataException('参数不正确。');
			}

			if (empty($workCorp->workMsgAudit)) {
				throw new InvalidDataException('参数不正确。');
			}

			/** @var Work $msgAuditApi */
			$msgAuditApi = \Yii::createObject([
				'class'  => Work::className(),
				'corpid' => $workCorp->corpid,
				'secret' => $workCorp->workMsgAudit->secret,
			]);

			$accessTokenInfo = WorkMsgAudit::getTokenInfo($corpId);

			if (!empty($accessTokenInfo)) {
				$msgAuditApi->SetAccessToken(['access_token' => $accessTokenInfo['access_token'], 'expire' => $accessTokenInfo['access_token_expires']]);
			}

			return $msgAuditApi;
		}

		/**
		 * @param int $length
		 * @param int $i
		 *
		 * @return mixed|string
		 */
		public static function getCorpState ($length = 10, $i = 0)
		{
			$state = StringUtil::randomNoNumberStr($length);

			$hasCount = WorkCorp::find()->where(['state' => $state])->count();
			if ($hasCount > 0) {
				$state = self::getCorpState($length);
			}

			return $state;
		}

		/**
		 * @param      $uid
		 * @param int  $currentCorpId
		 *
		 * @throws InvalidParameterException
		 */
		public static function checkCorpLimit ($uid, $currentCorpId = 0)
		{
			/** @var User $userData */
			$userData = User::findOne($uid);
			if (empty($userData)) {
				throw new InvalidParameterException('参数不合法！');
			}

			$userCorps = UserCorpRelation::find()->where(['uid' => $uid])->select('corp_id')->asArray()->all();
			if (!empty($userCorps)) {
				$corpId = array_column($userCorps, 'corp_id');
				if ($currentCorpId != 0) {
					if (array_search($currentCorpId, $corpId) !== false) {
						$keys = array_search($currentCorpId, $corpId);
						unset($corpId[$keys]);

						$corpId = array_values($corpId);
					}
				}

				if (!empty($corpId)) {
					$hasAuthCount = WorkCorpAuth::find()
						->where(['corp_id' => $corpId])
						->andWhere((['<>', 'auth_type', WorkCorpAuth::CANCEL_AUTH]))
						->count();
					$limitCorpNum = $userData->limit_corp_num != 0 ? $userData->limit_corp_num : \Yii::$app->params['default_corp_num'];
					if ($hasAuthCount >= $limitCorpNum) {
						throw new InvalidParameterException('绑定数量已达上限');
					}
				}
			}
		}

		/**
		 * @param                      $deviceCode
		 * @param                      $corpId
		 * @param array|UserInfoByCode $returnData
		 * @param array|UserInfoByCode $result
		 * @param bool                 $needFormat
		 *
		 * @throws InvalidDataException
		 * @throws InvalidParameterException
		 * @throws \Throwable
		 * @throws \yii\db\StaleObjectException
		 */
		public static function getUserData ($deviceCode, $corpId, &$returnData, $result = [], $needFormat = false)
		{
			if (empty($deviceCode) || empty($corpId)) {
				throw new InvalidParameterException('参数不合法！');
			}

			if ($needFormat) {
				$returnData = UserInfoByCode::parseFromArray([]);
			} else {
				$returnData = [
					'is_use'           => 0,
					'info'             => [],
					'wap_auth'         => 1,
					'user'             => [],
					'external_contact' => [],
					'openid'           => '',
					"deviceCode"       => $deviceCode
				];
			}

			$workCorp = WorkCorp::findOne(['corpid' => $corpId]);
			if (empty($workCorp)) {
				$workCorp = WorkCorp::findOne($corpId);
			}

			if (empty($workCorp)) {
				throw new InvalidParameterException('参数不合法！');
			} else {
				/** @var Cache $authCache * */
				$authCache            = \Yii::$app->authCache;
				$authCache->keyPrefix = $deviceCode . '-';
				$authInfo             = $authCache->get($workCorp->corpid);
				if ($needFormat) {
					$result = [];
					if (!empty($authInfo)) {
						$result             = Json::decode($authInfo);
						$result["CorpId"]   = $workCorp->corpid;
						$result["OpenId"]   = $result["openid"];
						$result["DeviceId"] = $result["deviceCode"];
					}
					$returnData = UserInfoByCode::parseFromArray($result);

				} else {
					\Yii::error($authInfo, '$authInfo');

					if (!empty($authInfo)) {
						if (empty($result)) {
							$result                  = Json::decode($authInfo);
							$returnData['firstTime'] = $result['firstTime'];
						} else {
							if ($result instanceof UserInfoByCode) {
								self::setAuthInfoCache($authCache, $deviceCode, $workCorp, $result, $authInfo);
								$result = $authInfo;
							}
						}
						self::GetAuthUserInfo($corpId, $authCache, $workCorp, $result, $returnData);

					} else {
						if (!empty($result)) {
							$returnData['openid'] = $result->OpenId;
							$Tresult              = [
								"openid"          => $result->OpenId,
								"UserId"          => $result->UserId,
								"external_userid" => $result->external_userid,
								"user_ticket"     => isset($result->user_ticket) ? $result->user_ticket : 0,
								"expires_in"      => isset($result->expires_in) ? $result->expires_in : 0,
								"open_userid"     => isset($result->open_userid) ? $result->open_userid : 0,
							];
							self::GetAuthUserInfo($corpId, $authCache, $workCorp, $Tresult, $returnData);
							self::setAuthInfoCache($authCache, $deviceCode, $workCorp, $result, $authInfo);
						} else {
							$returnData['firstTime'] = 0;
						}
					}
				}
			}
		}

		/**
		 * @param          $corpId
		 * @param Cache    $authCache
		 * @param WorkCorp $workCorp
		 * @param          $result
		 * @param          $returnData
		 *
		 * @throws InvalidDataException
		 * @throws \Throwable
		 * @throws \yii\db\StaleObjectException
		 */
		public static function GetAuthUserInfo ($corpId, $authCache, $workCorp, $result, &$returnData)
		{
			if (!empty($workCorp)) {
				$user = $workCorp->userCorpRelations[0]->u;
				if (!empty($result['UserId'])) {
					$workUserId = WorkUser::getUserId($workCorp->id, $result["UserId"]);
					if (!empty($workUserId)) {
						$workUser = WorkUser::findOne($workUserId);
					}

					if (!empty($workUser)) {
						$returnData['user']   = $workUser->dumpData();
						$returnData['openid'] = $workUser->openid;

						$userInfo      = [];
						$userType      = '';
						$masterAccount = '';
						$type          = 1;
						$subId         = 0;
						$returnData["wap_auth"] = 0;
						// init info data by work user
						if (!empty($workUser->mobile)) {
							$subUser = SubUser::findOne(['uid' => $user->uid, 'account' => $workUser->mobile]);
							if (!empty($subUser)) {
								$masterUser            = $subUser->u;
								$returnData["wap_auth"] = ($subUser->status == 1) ? 1 : 0;
								if (empty($masterUser->sub_num)) {
									$returnData["wap_auth"] = 1;
								}
								if ($subUser->type == SubUser::SUB_USER_MAIN) {
									$userInfo = $subUser->u;
									if (empty($userInfo->access_token)) {
										$userInfo = User::refreshToken($userInfo->account);
									}

									$masterAccount = $userInfo->account;
									$type          = 1;
									$subId         = 0;
									$userType      = User::USER_TYPE;
								} else {
//									if (!in_array($subUser->status, [SubUser::SUB_USER_CLOSE, SubUser::SUB_USER_FORBIDDEN])) {
									$userInfo = $subUser;
									if (empty($userInfo->access_token)) {
										$userInfo = SubUser::refreshToken($userInfo->account, $userInfo->uid, 2, '', true);
									}

									$masterAccount = $userInfo->u->account;
									$type          = 2;
									$subId         = $userInfo->sub_id;
									$userType      = SubUser::USER_TYPE;
//									}
								}
							}
						}

						if (empty($userInfo)) {
							$userInfo = $user;
							if (empty($userInfo->access_token)) {
								$userInfo = User::refreshToken($userInfo->account);
							}

							$masterAccount = $userInfo->account;
							$type          = 1;
							$subId         = 0;
							$userType      = User::USER_TYPE;
						}

						if (!empty($userInfo)) {
							$userInfo->refreshTokenExpire();

							$returnData['info'] = [
								'master_account' => $masterAccount,
								'phone'          => $workUser->mobile,
								'type'           => $type,
								'uid'            => $userInfo->uid,
								'sub_id'         => $subId,
								'access_token'   => base64_encode($userType . '-' . $userInfo->access_token),
							];
						}
					}
				} elseif (!empty($result['external_userid'])) {
					$externalContactId = WorkExternalContact::getExternalId($workCorp->id, $result['external_userid']);
					if ($externalContactId != 0) {
						$externalContact = WorkExternalContact::findOne($externalContactId);
						if (!empty($externalContact)) {
							$externalFollowCount = WorkExternalContactFollowUser::find()->where(['external_userid' => $externalContact->id, 'del_type' => WorkExternalContactFollowUser::WORK_CON_EX])->count();
							if ($externalFollowCount > 0) {
								$returnData['external_contact'] = $externalContact->dumpData();
							}
						}
					}
				} elseif (!empty($result['openid'])) {
					$returnData['openid'] = $result['openid'];
				}

				if (!empty($returnData['openid']) && empty($returnData['external_contact'])) {
					$result['openid'] = $returnData['openid'];
					$externalContact  = WorkExternalContact::findOne(['corp_id' => $workCorp->id, 'openid' => $returnData['openid']]);
					if (!empty($externalContact)) {
						$result['external_userid'] = $externalContact->external_userid;
						$authInfo                  = Json::encode($result, JSON_UNESCAPED_UNICODE);
						$authCache->set($corpId, $authInfo, 15 * 24 * 60 * 60);

						$externalFollowCount = WorkExternalContactFollowUser::find()->where(['external_userid' => $externalContact->id, 'del_type' => WorkExternalContactFollowUser::WORK_CON_EX])->count();
						if ($externalFollowCount > 0) {
							$returnData['external_contact'] = $externalContact->dumpData();
						}
					}
				}
			}

		}

		/**
		 * @param                $authCache
		 * @param                $deviceCode
		 * @param WorkCorp       $workCorp
		 * @param UserInfoByCode $result
		 * @param array          $authInfo
		 */
		public static function setAuthInfoCache ($authCache, $deviceCode, $workCorp, $result, &$authInfo)
		{
			$authInfo = [
				"corp_id"         => $workCorp->id,
				"openid"          => $result->OpenId,
				"UserId"          => $result->UserId,
				"external_userid" => $result->external_userid,
				"user_ticket"     => $result->user_ticket,
				"expires_in"      => $result->expires_in,
				"open_userid"     => $result->open_userid,
				"deviceCode"      => $deviceCode,
				"firstTime"       => time(),
			];
			$authCache->set($workCorp->corpid, Json::encode($authInfo, JSON_UNESCAPED_UNICODE), 15 * 24 * 60 * 60);
		}

		/**
		 * 发送销售机会
		 *
		 * @param User   $userInfo
		 * @param int    $type
		 * @param string $name
		 * @param int    $from
		 * @param string $sourceName
		 *
		 * @return bool
		 *
		 * @throws InvalidParameterException
		 * @throws \Throwable
		 * @throws \yii\db\StaleObjectException
		 */
		public static function sendOpportunities (User $userInfo, $type = 0, $name = "", $from = 0, $sourceName = '')
		{
			if (empty($userInfo) || !$userInfo instanceof User) {
				return false;
			}

			if (!empty(\Yii::$app->params['webhook']) && !empty(\Yii::$app->params['webhook']['register']) && !empty(\Yii::$app->params['webhook']['register'][$type])) {
				$webHookUrl = '';
				$agentUid   = 0;

				if (is_string(\Yii::$app->params['webhook']['register'][$type])) {
					$webHookUrl = \Yii::$app->params['webhook']['register'][$type];
				} elseif (is_array(\Yii::$app->params['webhook']['register'][$type])) {
					$webHookUrl = !empty(\Yii::$app->params['webhook']['register'][$type]['url']) ? \Yii::$app->params['webhook']['register'][$type]['url'] : '';
					$agentUid   = !empty(\Yii::$app->params['webhook']['register'][$type]['agent_uid']) ? \Yii::$app->params['webhook']['register'][$type]['agent_uid'] : 0;
				}

				if (!empty($webHookUrl)) {
					$messageContent = [
						"content" => "手机号：" . $userInfo->account . "
用户昵称：" . (!empty($name) ? $name : "暂未填写") . "
注册时间：" . $userInfo->create_time . "
注册来源：" . (!empty($sourceName) ? $sourceName : ($from == 0 ? "站点注册" : "官网注册")) . "
体验产品：日思夜想智能营销系统-日思夜想 SCRM",
					];

					WebhookUtil::send($webHookUrl, $messageContent, WebhookUtil::TEXT_MESSAGE);
				}

				if (!empty($agentUid)) {
					$userInfo->agent_uid = $agentUid;
					$userInfo->update();
				}
			}

			return true;
		}
	}