<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\DateUtil;
	use app\util\SUtils;
	use dovechen\yii2\weWork\ServiceWork;
	use Yii;
	use yii\db\Exception;
	use yii\helpers\Json;

	/**
	 * This is the model class for table "{{%work_corp_auth}}".
	 *
	 * @property int             $id
	 * @property int             $suite_id                      应用ID
	 * @property int             $corp_id                       企业ID
	 * @property string          $access_token                  授权方（企业）access_token
	 * @property string          $access_token_expires          授权方（企业）access_token超时时间
	 * @property string          $permanent_code                企业微信永久授权码
	 * @property string          $auth_user_info                授权管理员的信息
	 * @property string          $dealer_corp_info              代理服务商企业信息
	 * @property string          $auth_type                     授权状态 cancel_auth是取消授权，change_auth是更新授权，create_auth是授权成功通知
	 * @property string          $create_time                   添加时间
	 *
	 * @property WorkCorp        $corp
	 * @property WorkSuiteConfig $suite
	 */
	class WorkCorpAuth extends \yii\db\ActiveRecord
	{
		const CREATE_AUTH = 'create_auth';
		const CHANGE_AUTH = 'change_auth';
		const CANCEL_AUTH = 'cancel_auth';

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_corp_auth}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['suite_id', 'corp_id'], 'integer'],
				[['create_time'], 'safe'],
				[['access_token', 'permanent_code', 'auth_user_info', 'dealer_corp_info'], 'string', 'max' => 255],
				[['auth_type'], 'string', 'max' => 16],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
				[['suite_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkSuiteConfig::className(), 'targetAttribute' => ['suite_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'                   => Yii::t('app', 'ID'),
				'suite_id'             => Yii::t('app', '应用ID'),
				'corp_id'              => Yii::t('app', '企业ID'),
				'access_token'         => Yii::t('app', '授权方（企业）access_token'),
				'access_token_expires' => Yii::t('app', '授权方（企业）access_token超时时间'),
				'permanent_code'       => Yii::t('app', '企业微信永久授权码'),
				'auth_user_info'       => Yii::t('app', '授权管理员的信息'),
				'dealer_corp_info'     => Yii::t('app', '代理服务商企业信息'),
				'auth_type'            => Yii::t('app', '授权状态 cancel_auth是取消授权，change_auth是更新授权，create_auth是授权成功通知'),
				'create_time'          => Yii::t('app', '添加时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getCorp ()
		{
			return $this->hasOne(WorkCorp::className(), ['id' => 'corp_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getSuite ()
		{
			return $this->hasOne(WorkSuiteConfig::className(), ['id' => 'suite_id']);
		}

		/**
		 * @param bool $withConfig
		 *
		 * @return array
		 */
		public function dumpData ($withConfig = false)
		{
			$result = [
				'access_token'         => $this->access_token,
				'access_token_expires' => $this->access_token_expires,
				'permanent_code'       => $this->permanent_code,
				'corpid'               => $this->corp->corpid,
				'corp_name'            => $this->corp->corp_name,
				'corp_type'            => $this->corp->corp_type,
				'corp_square_logo_url' => $this->corp->corp_square_logo_url,
				'corp_user_max'        => $this->corp->corp_user_max,
				'corp_agent_max'       => $this->corp->corp_agent_max,
				'corp_full_name'       => $this->corp->corp_full_name,
				'verified_end_time'    => $this->corp->verified_end_time,
				'subject_type'         => $this->corp->subject_type,
				'corp_wxqrcode'        => $this->corp->corp_wxqrcode,
				'corp_scale'           => $this->corp->corp_scale,
				'corp_industry'        => $this->corp->corp_industry,
				'corp_sub_industry'    => $this->corp->corp_sub_industry,
				'location'             => $this->corp->location,
				'auth_user_info'       => Json::decode($this->auth_user_info, true),
				'dealer_corp_info'     => Json::decode($this->dealer_corp_info, true),
			];

			if ($withConfig) {
				$result['suite'] = $this->suite;
			}

			return $result;
		}

		/**
		 * @param bool $withConfig
		 *
		 * @return array
		 */
		public function dumpMiniData ($withConfig = false)
		{
			$result = [
				'access_token'         => $this->access_token,
				'access_token_expires' => $this->access_token_expires,
				'permanent_code'       => $this->permanent_code,
				'corpid'               => $this->corp->corpid,
				'corp_name'            => $this->corp->corp_name,
			];

			if ($withConfig) {
				$result['suite'] = $this->suite;
			}

			return $result;
		}

		/**
		 * @return int
		 *
		 * @throws InvalidDataException
		 * @throws \yii\base\InvalidConfigException
		 */
		public function refreshCorp ()
		{
			/** @var ServiceWork $serviceWork */
			$serviceWork = Yii::createObject([
				'class'        => ServiceWork::className(),
				'suite_id'     => $this->suite->suite_id,
				'suite_secret' => $this->suite->suite_secret,
				'suite_ticket' => $this->suite->suite_ticket,
			]);

			$suiteAccessTokenInfo = WorkSuiteConfig::getSuiteAccessTokenInfo($this->suite_id);

			$serviceWork->SetSuiteAccessToken($suiteAccessTokenInfo);

			$corpInfo = $serviceWork->getAuthInfo($this->corp->corpid, $this->permanent_code);

			$corpId = WorkCorp::setCorp($corpInfo, $this->suite_id);

			return $corpId;
		}

		/**
		 * @param $suiteId
		 * @param $corpId
		 * @param $corpInfo
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 */
		public static function setCorp ($suiteId, $corpId, $corpInfo)
		{
			// 事务处理
			$transaction = Yii::$app->db->beginTransaction();
			try {
				$corpAuth = static::findOne(['suite_id' => $suiteId, 'corp_id' => $corpId]);

				if (empty($corpAuth)) {
					$corpAuth              = new WorkCorpAuth();
					$corpAuth->create_time = DateUtil::getCurrentTime();
				}

				if (empty($corpAuth->auth_type) || $corpAuth->auth_type == static::CANCEL_AUTH) {
					$corpAuth->auth_type = static::CREATE_AUTH;
				}

				$corpAuth->suite_id = $suiteId;
				$corpAuth->corp_id  = $corpId;

				if (!empty($corpInfo['access_token'])) {
					$corpAuth->access_token = $corpInfo['access_token'];
				}

				if (!empty($corpInfo['expires_in'])) {
					$corpAuth->access_token_expires = $corpInfo['expires_in'] + time();
				}

				if (!empty($corpInfo['permanent_code'])) {
					$corpAuth->permanent_code = $corpInfo['permanent_code'];
				}

				if (!empty($corpInfo['auth_user_info'])) {
					$corpAuth->auth_user_info = Json::encode($corpInfo['auth_user_info'], JSON_UNESCAPED_UNICODE);
				}

				if (!empty($corpInfo['dealer_corp_info'])) {
					$corpAuth->dealer_corp_info = Json::encode($corpInfo['dealer_corp_info'], JSON_UNESCAPED_UNICODE);
				}

				if ($corpAuth->dirtyAttributes) {
					if (!$corpAuth->validate() || !$corpAuth->save()) {
						throw new Exception(SUtils::modelError($corpAuth));
					}
				}

				$transaction->commit();
			} catch (Exception $e) {
				$transaction->rollBack();
				throw new InvalidDataException($e->getMessage());
			}

			return $corpAuth->id;
		}

		/**
		 * @param      $corpId
		 * @param bool $withConfig
		 * @param int  $suiteId
		 *
		 * @return array
		 *
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function getTokenInfo ($corpId, $withConfig = false, $suiteId = 1)
		{
			$result       = [];
			$workCorpAuth = static::findOne(['suite_id' => $suiteId, 'corp_id' => $corpId]);

			if (!empty($workCorpAuth) && $workCorpAuth->auth_type != static::CANCEL_AUTH) {
				if (empty($workCorpAuth->access_token) || $workCorpAuth->access_token_expires < (time() - 60)) {
					/** @var ServiceWork $serviceWork */
					$serviceWork = Yii::createObject([
						'class'          => ServiceWork::className(),
						'suite_id'       => $workCorpAuth->suite->suite_id,
						'suite_secret'   => $workCorpAuth->suite->suite_secret,
						'suite_ticket'   => $workCorpAuth->suite->suite_ticket,
						'auth_corpid'    => $workCorpAuth->corp->corpid,
						'permanent_code' => $workCorpAuth->permanent_code,
					]);

					$serviceWork->GetAccessToken(true);

					$workCorpAuth->access_token         = $serviceWork->access_token;
					$workCorpAuth->access_token_expires = $serviceWork->access_token_expire;
					$workCorpAuth->save();

					$result = $workCorpAuth->dumpMiniData($withConfig);
				} else {
					$result = $workCorpAuth->dumpMiniData($withConfig);
				}
			}

			return $result;
		}
	}
