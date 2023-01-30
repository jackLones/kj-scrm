<?php

	namespace app\models;

	use app\util\apiOauth;
	use app\util\DateUtil;
	use app\util\StringUtil;
	use Yii;

	/**
	 * This is the model class for table "{{%wx_authorize_info}}".
	 *
	 * @property int         $id
	 * @property int         $author_id         授权信息ID
	 * @property string      $nick_name         授权方昵称
	 * @property string      $head_img          授权方头像
	 * @property int         $service_type_info 授权方公众号类型，0代表订阅号，1代表由历史老帐号升级后的订阅号，2代表服务号
	 * @property int         $verify_type_info  授权方认证类型，-1代表未认证，0代表微信认证，1代表新浪微博认证，2代表腾讯微博认证，3代表已资质认证通过但还未通过名称认证，4代表已资质认证通过、还未通过名称认证，但通过了新浪微博认证，5代表已资质认证通过、还未通过名称认证，但通过了腾讯微博认证
	 * @property string      $user_name         授权方公众号的原始ID
	 * @property string      $signature         帐号介绍
	 * @property string      $industry          行业
	 * @property string      $principal_name    小程序的主体名称
	 * @property string      $alias             授权方公众号所设置的微信号，可能为空
	 * @property int         $open_store        是否开通微信门店功能
	 * @property int         $open_scan         是否开通微信扫商品功能
	 * @property int         $open_pay          是否开通微信支付功能
	 * @property int         $open_card         是否开通微信卡券功能
	 * @property int         $open_shake        是否开通微信摇一摇功能
	 * @property string      $qrcode_url        二维码图片的URL
	 * @property string      $qrcode_img        二维码base64数据
	 * @property string      $authorizer_appid  授权方appid
	 * @property string      $func_info         公众号授权给开发者的权限集列表，ID为1到50
	 * @property string      $miniprograminfo   可根据这个字段判断是否为小程序类型授权
	 * @property int         $auth_type         0：公众号；1：小程序
	 * @property string      $update_time       更新时间
	 * @property string      $create_time       创建时间
	 * @property string      $last_tag_time     最后一次同步时间
	 *
	 * @property WxAuthorize $author
	 */
	class WxAuthorizeInfo extends \yii\db\ActiveRecord
	{
		const AUTH_TYPE_APP = 0;         //公众号
		const AUTH_TYPE_MINI_APP = 1;    //小程序

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%wx_authorize_info}}';
		}

		/**
		 *
		 * @return object|\yii\db\Connection|null
		 *
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function getDb ()
		{
			return Yii::$app->get('mdb');
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['author_id'], 'required'],
				[['author_id', 'service_type_info', 'verify_type_info', 'open_store', 'open_scan', 'open_pay', 'open_card', 'open_shake', 'auth_type'], 'integer'],
				[['head_img', 'qrcode_url', 'qrcode_img', 'miniprograminfo', 'industry'], 'string'],
				[['update_time', 'create_time', 'last_tag_time'], 'safe'],
				[['nick_name', 'user_name', 'principal_name', 'alias', 'authorizer_appid'], 'string', 'max' => 64],
				[['signature', 'func_info'], 'string', 'max' => 255],
				[['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => WxAuthorize::className(), 'targetAttribute' => ['author_id' => 'author_id']],
			];
		}

		/**
		 * string func_info ID为1到26分别代表：
		 * 1、消息管理权限
		 * 2、用户管理权限
		 * 3、帐号服务权限
		 * 4、网页服务权限
		 * 5、微信小店权限
		 * 6、微信多客服权限
		 * 7、群发与通知权限
		 * 8、微信卡券权限
		 * 9、微信扫一扫权限
		 * 10、微信连WIFI权限
		 * 11、素材管理权限
		 * 12、微信摇周边权限
		 * 13、微信门店权限
		 * 14、微信支付权限
		 * 15、自定义菜单权限
		 * 16、获取认证状态及信息
		 * 17、帐号管理权限（小程序）
		 * 18、开发管理与数据分析权限（小程序）
		 * 19、客服消息管理权限（小程序）
		 * 20、微信登录权限（小程序）
		 * 21、数据分析权限（小程序）
		 * 22、城市服务接口权限
		 * 23、广告管理权限
		 * 24、开放平台帐号管理权限
		 * 25、 开放平台帐号管理权限（小程序）
		 * 26、微信电子发票权限
		 * 27、微信开放平台帐号管理权限
		 * 28、帐号管理权限
		 * 29、开发管理与数据分析权限
		 * 30、客服消息管理权限
		 * 31、微信开放平台帐号管理权限
		 * 32、小程序插件管理权限集
		 * 33、小程序附近地点权限集
		 *
		 * @inheritdoc
		 */
		public function attributeLabels ()
		{
			return [
				'id'                => Yii::t('app', 'ID'),
				'author_id'         => Yii::t('app', '授权信息ID'),
				'nick_name'         => Yii::t('app', '授权方昵称'),
				'head_img'          => Yii::t('app', '授权方头像'),
				'service_type_info' => Yii::t('app', '授权方公众号类型，0代表订阅号，1代表由历史老帐号升级后的订阅号，2代表服务号'),
				'verify_type_info'  => Yii::t('app', '授权方认证类型，-1代表未认证，0代表微信认证，1代表新浪微博认证，2代表腾讯微博认证，3代表已资质认证通过但还未通过名称认证，4代表已资质认证通过、还未通过名称认证，但通过了新浪微博认证，5代表已资质认证通过、还未通过名称认证，但通过了腾讯微博认证'),
				'user_name'         => Yii::t('app', '授权方公众号的原始ID'),
				'signature'         => Yii::t('app', '帐号介绍'),
				'industry'          => Yii::t('app', '行业'),
				'principal_name'    => Yii::t('app', '小程序的主体名称'),
				'alias'             => Yii::t('app', '授权方公众号所设置的微信号，可能为空'),
				'open_store'        => Yii::t('app', '是否开通微信门店功能'),
				'open_scan'         => Yii::t('app', '是否开通微信扫商品功能'),
				'open_pay'          => Yii::t('app', '是否开通微信支付功能'),
				'open_card'         => Yii::t('app', '是否开通微信卡券功能'),
				'open_shake'        => Yii::t('app', '是否开通微信摇一摇功能'),
				'qrcode_url'        => Yii::t('app', '二维码图片的URL'),
				'qrcode_img'        => Yii::t('app', '二维码base64数据'),
				'authorizer_appid'  => Yii::t('app', '授权方appid'),
				'func_info'         => Yii::t('app', '公众号授权给开发者的权限集列表，ID为1到50'),
				'miniprograminfo'   => Yii::t('app', '可根据这个字段判断是否为小程序类型授权'),
				'auth_type'         => Yii::t('app', '0：公众号；1：小程序'),
				'update_time'       => Yii::t('app', '更新时间'),
				'create_time'       => Yii::t('app', '创建时间'),
				'last_tag_time'     => Yii::t('app', '最后一次同步标签时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getAuthor ()
		{
			return $this->hasOne(WxAuthorize::className(), ['author_id' => 'author_id']);
		}

		/**
		 * @param bool $withFunc
		 *
		 * @return array
		 */
		public function dumpData ($withFunc = false)
		{
			$result = [
				'alias'             => $this->alias,
				'user_name'         => $this->user_name,
				'authorizer_appid'  => $this->authorizer_appid,
				'nick_name'         => $this->nick_name,
				'head_img'          => $this->head_img,
				'qrcode_url'        => $this->qrcode_url,
				'open_store'        => $this->open_store,
				'open_scan'         => $this->open_scan,
				'open_pay'          => $this->open_pay,
				'open_card'         => $this->open_card,
				'open_shake'        => $this->open_shake,
				'service_type_info' => $this->service_type_info,
				'verify_type_info'  => $this->verify_type_info,
				'author_type'       => $this->author->authorizer_type == WxAuthorize::AUTH_TYPE_UNAUTH ? 0 : ($this->author->authorizer_type == WxAuthorize::AUTH_TYPE_UPDATEAUTH ? 2 : 1),
				'miniprograminfo'   => $this->miniprograminfo,
				'signature'         => $this->signature,
				'principal_name'    => $this->principal_name,
				'auth_type'         => $this->auth_type,
			];

			if ($withFunc) {
				$result['func_info'] = $this->func_info;
			}

			return $result;
		}

		/**
		 * 获取精简信息
		 *
		 * @return array
		 */
		public function dumpMinData ()
		{
			$result = [
				'alias'     => $this->alias,
				'user_name' => $this->user_name,
				'nick_name' => $this->nick_name,
				'head_img'  => $this->head_img,
			];

			return $result;
		}

		/**
		 * @param $authorizeData
		 *
		 * @return bool|int
		 */
		public static function setAuthorizerInfo ($authorizeData)
		{
			$authorizeInfoId = false;

			$transaction = Yii::$app->db->beginTransaction();

			try {
				$authAppid = $authorizeData['authorizer_appid'];
				$authorize = WxAuthorize::findOne(['authorizer_appid' => $authAppid]);

				$authorizeInfo = WxAuthorizeInfo::findOne(['authorizer_appid' => $authAppid]);

				if (empty($authorizeInfo)) {
					$authorizeInfo = new WxAuthorizeInfo();
				}

				$authorizeInfo->author_id = $authorize->author_id;
				$authorizeInfo->setAttributes($authorizeData);
				$authorizeInfo->update_time = DateUtil::getCurrentTime();
				$authorizeInfo->create_time = DateUtil::getCurrentTime();
				if ($authorizeInfo->save()) {
					$authorizeInfoId = $authorizeInfo->id;

					if ($authorizeInfo->auth_type == static::AUTH_TYPE_MINI_APP) {
						$authorize->auth_type = WxAuthorize::AUTH_TYPE_MINI_APP;
						$authorize->save();
					}
				}

				$transaction->commit();
			} catch (\Exception $e) {
				$transaction->rollBack();
			}

			return $authorizeInfoId;
		}

		/**
		 * @param      $authAppid
		 * @param      $uid
		 * @param bool $refresh
		 *
		 * @return WxAuthorizeInfo|array|null
		 * @throws \app\components\InvalidParameterException
		 * @throws \app\components\NotAllowException
		 */
		public static function getAuthorizerInfo ($authAppid, $uid, $refresh = false)
		{
			if (empty($authAppid)) {
				return [];
			}

			$authorizerInfo = WxAuthorizeInfo::findOne(['authorizer_appid' => $authAppid]);

			if (empty($authorizerInfo) || $refresh) {
				if (empty($authorizeInfo)) {
					$authorizer = WxAuthorize::findOne(['authorizer_appid' => $authAppid]);

					if (!empty($authorizer)) {
						$configId = $authorizer->config_id;
					} else {
						$configId = 1;
					}
				} else {
					$configId = $authorizerInfo->author->config_id;
				}

				$apiOauth      = new apiOauth($uid, $configId, $authAppid);
				$authorizeInfo = $apiOauth->getAuthorizerInfo($authAppid);

				if ($authorizeInfo['errcode'] != 0) {
					$authorizeInfo = $apiOauth->getAuthorizerInfo($authAppid);
				}

				if ($authorizeInfo['errcode'] == 0) {
					$authorizeData = [
						'nick_name'         => isset($authorizeInfo['authorizer_info']['nick_name']) ? $authorizeInfo['authorizer_info']['nick_name'] : '',
						'head_img'          => isset($authorizeInfo['authorizer_info']['head_img']) ? $authorizeInfo['authorizer_info']['head_img'] : '',
						'service_type_info' => $authorizeInfo['authorizer_info']['service_type_info']['id'],
						'verify_type_info'  => $authorizeInfo['authorizer_info']['verify_type_info']['id'],
						'user_name'         => $authorizeInfo['authorizer_info']['user_name'],
						'alias'             => $authorizeInfo['authorizer_info']['alias'],
						'open_store'        => $authorizeInfo['authorizer_info']['business_info']['open_store'],
						'open_scan'         => $authorizeInfo['authorizer_info']['business_info']['open_scan'],
						'open_pay'          => $authorizeInfo['authorizer_info']['business_info']['open_pay'],
						'open_card'         => $authorizeInfo['authorizer_info']['business_info']['open_card'],
						'open_shake'        => $authorizeInfo['authorizer_info']['business_info']['open_shake'],
						'qrcode_url'        => $authorizeInfo['authorizer_info']['qrcode_url'],
						'qrcode_img'        => StringUtil::getImgString($authorizeInfo['authorizer_info']['qrcode_url']),
						'authorizer_appid'  => $authorizeInfo['authorization_info']['authorizer_appid'],
						'func_info'         => $apiOauth->getFuncInfo($authorizeInfo['authorization_info']['func_info']),
					];

					if (isset($authorizeInfo['authorizer_info']['signature'])) {
						$authorizeData['signature'] = $authorizeInfo['authorizer_info']['signature'];
					}

					if (isset($authorizeInfo['authorizer_info']['principal_name'])) {
						$authorizeData['principal_name'] = $authorizeInfo['authorizer_info']['principal_name'];
					}

					if (isset($authorizeInfo['authorizer_info']['MiniProgramInfo'])) {
						$authorizeData['miniprograminfo'] = json_encode($authorizeInfo['authorizer_info']['MiniProgramInfo'], JSON_UNESCAPED_UNICODE);
						$authorizeData['auth_type']       = static::AUTH_TYPE_MINI_APP;
					}

					$authorizerInfoId = WxAuthorizeInfo::setAuthorizerInfo($authorizeData);

					if ($authorizerInfoId) {
						$authorizerInfo = WxAuthorizeInfo::findOne(['id' => $authorizerInfoId]);
					}
				}
			}

			return $authorizerInfo;
		}
	}
