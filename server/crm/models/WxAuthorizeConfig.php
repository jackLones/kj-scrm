<?php

	namespace app\models;

	use app\util\apiOauth;
	use Yii;

	/**
	 * This is the model class for table "{{%wx_authorize_config}}".
	 *
	 * @property int           $id
	 * @property string        $appid                          第三方开放平台应用APPID
	 * @property string        $appSecret                      第三方开放平台应用APPSECRET
	 * @property string        $token                          第三方开放平台应用对接TOKEN
	 * @property string        $encode_aes_key                 第三方开放平台应用对接ENCODE_AES_KEY
	 * @property string        $component_verify_ticket        第三方平台安全TICKET（每十分钟更新一次）
	 * @property string        $component_access_token         第三方平台接口调用凭据
	 * @property string        $component_access_token_expires 第三方平台接口调用凭据失效时间
	 * @property string        $pre_auth_code                  第三方平台授权流程准备的预授权码
	 * @property string        $pre_auth_code_expires          第三方平台授权流程准备的预授权码失效时间
	 * @property int           $status                         0：关闭、1：开启
	 * @property string        $update_time                    第三方平台安全TICKET更新时间
	 * @property string        $create_time                    创建日期
	 *
	 * @property WxAuthorize[] $wxAuthorizes
	 */
	class WxAuthorizeConfig extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%wx_authorize_config}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['status'], 'integer'],
				[['update_time', 'create_time'], 'safe'],
				[['create_time'], 'required'],
				[['appid', 'appSecret'], 'string', 'max' => 64],
				[['token', 'encode_aes_key', 'component_verify_ticket', 'component_access_token', 'pre_auth_code'], 'string', 'max' => 255],
				[['component_access_token_expires', 'pre_auth_code_expires'], 'string', 'max' => 16],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'                             => Yii::t('app', 'ID'),
				'appid'                          => Yii::t('app', '第三方开放平台应用APPID'),
				'appSecret'                      => Yii::t('app', '第三方开放平台应用APPSECRET'),
				'token'                          => Yii::t('app', '第三方开放平台应用对接TOKEN'),
				'encode_aes_key'                 => Yii::t('app', '第三方开放平台应用对接ENCODE_AES_KEY'),
				'component_verify_ticket'        => Yii::t('app', '第三方平台安全TICKET（每十分钟更新一次）'),
				'component_access_token'         => Yii::t('app', '第三方平台接口调用凭据'),
				'component_access_token_expires' => Yii::t('app', '第三方平台接口调用凭据失效时间'),
				'pre_auth_code'                  => Yii::t('app', '第三方平台授权流程准备的预授权码'),
				'pre_auth_code_expires'          => Yii::t('app', '第三方平台授权流程准备的预授权码失效时间'),
				'status'                         => Yii::t('app', '0：关闭、1：开启'),
				'update_time'                    => Yii::t('app', '第三方平台安全TICKET更新时间'),
				'create_time'                    => Yii::t('app', '创建日期'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWxAuthorizes ()
		{
			return $this->hasMany(WxAuthorize::className(), ['config_id' => 'id']);
		}

		/**
		 * @param $configId
		 * @param $cashierToken
		 *
		 * @return mixed|string
		 * @throws \app\components\InvalidParameterException
		 * @throws \app\components\NotAllowException
		 */
		public static function getAccessTokenInfo ($configId, $cashierToken)
		{
			$wxAuthorizeConfig = WxAuthorizeConfig::findOne(['id' => $configId]);

			if ($wxAuthorizeConfig->component_access_token_expires < time()) {
				$apiOauth    = new apiOauth($cashierToken, $configId);
				$accessToken = $apiOauth->getComponentAccessToken();
			} else {
				$accessToken = $wxAuthorizeConfig->component_access_token;
			}

			return $accessToken;
		}
	}
