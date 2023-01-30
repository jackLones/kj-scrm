<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\WorkUtils;
	use dovechen\yii2\weWork\ServiceProvider;
	use Yii;

	/**
	 * This is the model class for table "{{%work_provider_config}}".
	 *
	 * @property int                    $id
	 * @property string                 $provider_corpid               每个服务商同时也是一个企业微信的企业，都有唯一的corpid。
	 * @property string                 $provider_secret               作为服务商身份的调用凭证，应妥善保管好该密钥，务必不能泄漏。
	 * @property string                 $token                         Token用于计算签名
	 * @property string                 $encode_aes_key                EncodingAESKey用于消息内容加密
	 * @property string                 $provider_access_token         服务商的access_token
	 * @property string                 $provider_access_token_expires provider_access_token有效期
	 * @property int                    $status                        0：关闭、1：开启
	 * @property string                 $create_time                   创建时间
	 *
	 * @property WorkProviderTemplate[] $workProviderTemplates
	 * @property WorkSuiteConfig[]      $workSuiteConfigs
	 */
	class WorkProviderConfig extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_provider_config}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['provider_corpid', 'provider_secret', 'token', 'encode_aes_key'], 'required'],
				[['status'], 'integer'],
				[['create_time'], 'safe'],
				[['provider_corpid', 'provider_secret'], 'string', 'max' => 64],
				[['token', 'encode_aes_key', 'provider_access_token'], 'string', 'max' => 255],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'                            => Yii::t('app', 'ID'),
				'provider_corpid'               => Yii::t('app', '每个服务商同时也是一个企业微信的企业，都有唯一的corpid。'),
				'provider_secret'               => Yii::t('app', '作为服务商身份的调用凭证，应妥善保管好该密钥，务必不能泄漏。'),
				'token'                         => Yii::t('app', 'Token用于计算签名'),
				'encode_aes_key'                => Yii::t('app', 'EncodingAESKey用于消息内容加密'),
				'provider_access_token'         => Yii::t('app', '服务商的access_token'),
				'provider_access_token_expires' => Yii::t('app', 'provider_access_token有效期'),
				'status'                        => Yii::t('app', '0：关闭、1：开启'),
				'create_time'                   => Yii::t('app', '创建时间'),
			];
		}

		public function dumpData ($withSuite = false)
		{
			$result = [
				'id'                            => $this->id,
				'provider_corpid'               => $this->provider_corpid,
				'provider_secret'               => $this->provider_secret,
				'token'                         => $this->token,
				'encode_aes_key'                => $this->encode_aes_key,
				'provider_access_token'         => $this->provider_access_token,
				'provider_access_token_expires' => $this->provider_access_token_expires,
				'register_code'                 => $this->register_code,
				'register_code_expires'         => $this->register_code_expires,
				'status'                        => $this->status,
				'create_time'                   => $this->create_time,
			];

			if ($withSuite) {
				$result['suite'] = [];
				if (!empty($this->workSuiteConfigs)) {
					foreach ($this->workSuiteConfigs as $workSuiteConfig) {
						array_push($result['suite'], $workSuiteConfig->dumpData());
					}
				}
			}

			return $result;
		}

		public function dumpMiniData ()
		{
			return [
				'id'              => $this->id,
				'provider_corpid' => $this->provider_corpid,
				'provider_secret' => $this->provider_secret,
				'token'           => $this->token,
				'encode_aes_key'  => $this->encode_aes_key,
				'status'          => $this->status,
				'create_time'     => $this->create_time,
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkProviderTemplates ()
		{
			return $this->hasMany(WorkProviderTemplate::className(), ['provider_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkSuiteConfigs ()
		{
			return $this->hasMany(WorkSuiteConfig::className(), ['provider_id' => 'id']);
		}

		/**
		 * @param int $providerId
		 *
		 * @return array
		 *
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function getProviderAccessTokenInfo ($providerId = 1)
		{
			$workProviderConfig = static::findOne($providerId);

			if (!empty($workProviderConfig) && $workProviderConfig->provider_access_token_expires < time()) {
				/** @var ServiceProvider $serviceProvider */
				$serviceProvider = Yii::createObject([
					'class'           => ServiceProvider::className(),
					'provider_corpid' => $workProviderConfig->provider_corpid,
					'provider_secret' => $workProviderConfig->provider_secret,
				]);

				$serviceProvider->GetProviderAccessToken(true);

				$workProviderConfig->provider_access_token         = $serviceProvider->provider_access_token;
				$workProviderConfig->provider_access_token_expires = $serviceProvider->provider_access_token_expire;

				$workProviderConfig->save();
			}

			return [
				'provider_access_token' => $workProviderConfig->provider_access_token,
				'expire'                => $workProviderConfig->provider_access_token_expires,
			];
		}

		/**
		 * @param     $authCode
		 * @param int $providerId
		 *
		 * @return          |null
		 *
		 * @throws InvalidDataException
		 */
		public static function getLoginInfo ($authCode, $providerId = 1)
		{
			try {
				$serviceProvider = WorkUtils::getProviderApi($providerId);
			} catch (\Exception $e) {
				throw new InvalidDataException($e->getMessage());
			}

			try {
				return $serviceProvider->getLoginInfo($authCode);
			} catch (\Exception $e) {
				throw new InvalidDataException($e->getMessage());
			}
		}
	}
