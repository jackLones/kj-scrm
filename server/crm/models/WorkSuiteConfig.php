<?php

	namespace app\models;

	use app\components\InvalidParameterException;
	use dovechen\yii2\weWork\ServiceWork;
	use Yii;

	/**
	 * This is the model class for table "{{%work_suite_config}}".
	 *
	 * @property int                $id
	 * @property int                $provider_id                服务商ID
	 * @property string             $suite_id                   suiteid为应用的唯一身份标识
	 * @property string             $name                       应用名字
	 * @property string             $logo_url                   应用方形头像
	 * @property string             $description                应用详情
	 * @property string             $redirect_domain            应用可信域名
	 * @property string             $home_url                   应用主页url
	 * @property string             $suite_secret               suite_secret为对应的调用身份密钥
	 * @property string             $token                      Token用于计算签名
	 * @property string             $encode_aes_key             EncodingAESKey用于消息内容加密
	 * @property string             $suite_ticket               suite_ticket与suite_secret配套使用，用于获取suite_access_token。企业微信后台向登记的应用指令回调地址定期推送（10分钟）
	 * @property string             $suite_access_token         授权方（企业）access_token
	 * @property string             $suite_access_token_expires 授权方（企业）access_token超时时间
	 * @property string             $pre_auth_code              预授权码
	 * @property string             $pre_auth_code_expires      预授权码有效期
	 * @property int                $status                     0：关闭、1：开启
	 * @property string             $update_time                更新时间
	 * @property string             $create_time                创建时间
	 *
	 * @property WorkCorpAgent[]    $workCorpAgents
	 * @property WorkCorpAuth[]     $workCorpAuths
	 * @property WorkProviderConfig $provider
	 */
	class WorkSuiteConfig extends \yii\db\ActiveRecord
	{
		const SUITE_CLOSE = 0;
		const SUITE_NORMAL = 1;

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_suite_config}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['provider_id', 'suite_id', 'suite_secret', 'token', 'encode_aes_key'], 'required'],
				[['provider_id', 'status'], 'integer'],
				[['description'], 'string'],
				[['update_time', 'create_time'], 'safe'],
				[['suite_id', 'suite_secret'], 'string', 'max' => 64],
				[['logo_url', 'redirect_domain', 'home_url', 'token', 'encode_aes_key', 'suite_ticket', 'suite_access_token', 'pre_auth_code'], 'string', 'max' => 255],
				[['provider_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkProviderConfig::className(), 'targetAttribute' => ['provider_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'                         => Yii::t('app', 'ID'),
				'provider_id'                => Yii::t('app', '服务商ID'),
				'suite_id'                   => Yii::t('app', 'suiteid为应用的唯一身份标识'),
				'name'                       => Yii::t('app', '应用名字'),
				'logo_url'                   => Yii::t('app', '应用方形头像'),
				'description'                => Yii::t('app', '应用详情'),
				'redirect_domain'            => Yii::t('app', '应用可信域名'),
				'home_url'                   => Yii::t('app', '应用主页url'),
				'suite_secret'               => Yii::t('app', 'suite_secret为对应的调用身份密钥'),
				'token'                      => Yii::t('app', 'Token用于计算签名'),
				'encode_aes_key'             => Yii::t('app', 'EncodingAESKey用于消息内容加密'),
				'suite_ticket'               => Yii::t('app', 'suite_ticket与suite_secret配套使用，用于获取suite_access_token。企业微信后台向登记的应用指令回调地址定期推送（10分钟）'),
				'suite_access_token'         => Yii::t('app', '授权方（企业）access_token'),
				'suite_access_token_expires' => Yii::t('app', '授权方（企业）access_token超时时间'),
				'pre_auth_code'              => Yii::t('app', '预授权码'),
				'pre_auth_code_expires'      => Yii::t('app', '预授权码有效期'),
				'status'                     => Yii::t('app', '0：关闭、1：开启'),
				'update_time'                => Yii::t('app', '更新时间'),
				'create_time'                => Yii::t('app', '创建时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkCorpAgents ()
		{
			return $this->hasMany(WorkCorpAgent::className(), ['suite_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkCorpAuths ()
		{
			return $this->hasMany(WorkCorpAuth::className(), ['suite_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getProvider ()
		{
			return $this->hasOne(WorkProviderConfig::className(), ['id' => 'provider_id']);
		}

		public function dumpData ()
		{
			return [
				'id'              => $this->id,
				'suite_id'        => $this->suite_id,
				'name'            => $this->name,
				'logo_url'        => $this->logo_url,
				'description'     => $this->description,
				'redirect_domain' => $this->redirect_domain,
				'home_url'        => $this->home_url,
			];
		}

		/**
		 * @param $configId
		 *
		 * @return array
		 *
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function getSuiteAccessTokenInfo ($configId)
		{
			$workSuiteConfig = WorkSuiteConfig::findOne($configId);

			if ($workSuiteConfig->suite_access_token_expires < time()) {
				/** @var ServiceWork $serviceWork */
				$serviceWork = Yii::createObject([
					'class'        => ServiceWork::className(),
					'suite_id'     => $workSuiteConfig->suite_id,
					'suite_secret' => $workSuiteConfig->suite_secret,
					'suite_ticket' => $workSuiteConfig->suite_ticket,
				]);

				$serviceWork->GetSuiteAccessToken(true);

				$workSuiteConfig->suite_access_token         = $serviceWork->suite_access_token;
				$workSuiteConfig->suite_access_token_expires = (string) $serviceWork->suite_access_token_expire;
				$workSuiteConfig->save();
			}

			return [
				'suite_access_token' => $workSuiteConfig->suite_access_token,
				'expire'             => $workSuiteConfig->suite_access_token_expires,
			];
		}

		/**
		 * @param     $authCode
		 * @param     $configId
		 * @param int $uid
		 *
		 * @return array
		 *
		 * @throws InvalidParameterException
		 * @throws \app\components\InvalidDataException
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function getPermanentCode ($authCode, $configId, $uid = 0)
		{
			$result = [
				'status'         => false,
				'permanent_code' => '',
				'corp_id'        => '',
			];

			$workSuiteConfig = static::findOne($configId);
			if (!empty($workSuiteConfig)) {
				/** @var ServiceWork $serviceWork */
				$serviceWork = Yii::createObject([
					'class'        => ServiceWork::className(),
					'suite_id'     => $workSuiteConfig->suite_id,
					'suite_secret' => $workSuiteConfig->suite_secret,
					'suite_ticket' => $workSuiteConfig->suite_ticket,
				]);

				$suiteAccessTokenInfo = WorkSuiteConfig::getSuiteAccessTokenInfo($configId);

				$serviceWork->SetSuiteAccessToken($suiteAccessTokenInfo);

				$permanentResult = $serviceWork->getPermanentCode($authCode);

				$result = [
					'status'         => true,
					'permanent_code' => $permanentResult['permanent_code'],
					'corp_id'        => WorkCorp::setCorp($permanentResult, $configId, $uid),
				];
			} else {
				throw new InvalidParameterException('不正确的参数');
			}

			return $result;
		}
	}
