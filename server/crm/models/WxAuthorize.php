<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\apiOauth;
	use app\util\DateUtil;
	use Yii;
	use yii\db\Exception;

	/**
	 * This is the model class for table "{{%wx_authorize}}".
	 *
	 * @property int                      $author_id
	 * @property int                      $config_id                       授权配置ID
	 * @property string                   $authorizer_appid                授权方APPID
	 * @property string                   $authorizer_access_token         授权方接口调用凭据
	 * @property string                   $authorizer_access_token_expires 授权方接口调用凭据有效期
	 * @property string                   $authorizer_refresh_token        接口调用凭据刷新令牌
	 * @property string                   $func_info                       公众号授权给开发者的权限集列表，ID为1到50
	 * @property int                      $auth_type                       0：公众号；1：小程序
	 * @property int                      $auth_mini_type                  0：普通；1：快速创建
	 * @property string                   $authorizer_type                 授权状态 unauthorized是取消授权，updateauthorized是更新授权，authorized是授权成功通知
	 * @property string                   $authorizer_code                 授权码，可用于换取公众号的接口调用凭据
	 * @property string                   $authorizer_code_expires         授权码有效期
	 * @property string                   $pre_auth_code                   预授权码
	 * @property string                   $update_time                     更新日期
	 * @property string                   $create_time                     创建日期
	 *
	 * @property AutoReply[]              $autoReplies
	 * @property Blacklist[]              $blacklists
	 * @property Fans[]                   $fans
	 * @property FansBehavior[]           $fansBehaviors
	 * @property FansMsgMaterial[]        $fansMsgMaterials
	 * @property FansStatistic[]          $fansStatistics
	 * @property HighLevelPushMsg[]       $highLevelPushMsgs
	 * @property InteractReply[]          $interactReplies
	 * @property InteractReplyDetail[]    $interactReplyDetails
	 * @property Keyword[]                $keywords
	 * @property KfPushMsg[]              $kfPushMsgs
	 * @property KfUser[]                 $kfUsers
	 * @property Material[]               $materials
	 * @property MiniMsgMaterial[]        $miniMsgMaterials
	 * @property MiniUser[]               $miniUsers
	 * @property QuickMsg[]               $quickMsgs
	 * @property Scene[]                  $scenes
	 * @property Tags[]                   $tags
	 * @property Template[]               $templates
	 * @property TemplatePushMsg[]        $templatePushMsgs
	 * @property UserAuthorRelation[]     $userAuthorRelations
	 * @property WorkUserAuthorRelation[] $workUserAuthorRelations
	 * @property MaterialPullTime[]       $materialPullTimes
	 * @property WxAuthorizeConfig        $config
	 * @property WxAuthorizeInfo          $wxAuthorizeInfo
	 */
	class WxAuthorize extends \yii\db\ActiveRecord
	{
		const AUTH_TYPE_APP = 0;            //公众号
		const AUTH_TYPE_MINI_APP = 1;       //小程序

		const AUTH_MINI_TYPE_NORMAL = 0;    //普通小程序
		const AUTH_MINI_TYPE_FAST = 1;      //快速创建小程序

		const AUTH_TYPE_AUTH = 'authorized';
		const AUTH_TYPE_UPDATEAUTH = 'updateauthorized';
		const AUTH_TYPE_UNAUTH = 'unauthorized';

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%wx_authorize}}';
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
				[['config_id'], 'required'],
				[['config_id', 'auth_type', 'auth_mini_type'], 'integer'],
				[['update_time', 'create_time'], 'safe'],
				[['authorizer_appid'], 'string', 'max' => 64],
				[['authorizer_access_token', 'authorizer_refresh_token', 'func_info', 'authorizer_code', 'pre_auth_code'], 'string', 'max' => 255],
				[['authorizer_access_token_expires', 'authorizer_type', 'authorizer_code_expires'], 'string', 'max' => 16],
				[['config_id'], 'exist', 'skipOnError' => true, 'targetClass' => WxAuthorizeConfig::className(), 'targetAttribute' => ['config_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'author_id'                       => Yii::t('app', 'Author ID'),
				'config_id'                       => Yii::t('app', '授权配置ID'),
				'authorizer_appid'                => Yii::t('app', '授权方APPID'),
				'authorizer_access_token'         => Yii::t('app', '授权方接口调用凭据'),
				'authorizer_access_token_expires' => Yii::t('app', '授权方接口调用凭据有效期'),
				'authorizer_refresh_token'        => Yii::t('app', '接口调用凭据刷新令牌'),
				'func_info'                       => Yii::t('app', '公众号授权给开发者的权限集列表，ID为1到50'),
				'auth_type'                       => Yii::t('app', '0：公众号；1：小程序'),
				'auth_mini_type'                  => Yii::t('app', '0：普通；1：快速创建'),
				'authorizer_type'                 => Yii::t('app', '授权状态 unauthorized是取消授权，updateauthorized是更新授权，authorized是授权成功通知'),
				'authorizer_code'                 => Yii::t('app', '授权码，可用于换取公众号的接口调用凭据'),
				'authorizer_code_expires'         => Yii::t('app', '授权码有效期'),
				'pre_auth_code'                   => Yii::t('app', '预授权码'),
				'update_time'                     => Yii::t('app', '更新日期'),
				'create_time'                     => Yii::t('app', '创建日期'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getAutoReplies ()
		{
			return $this->hasMany(AutoReply::className(), ['author_id' => 'author_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getBlacklists ()
		{
			return $this->hasMany(Blacklist::className(), ['author_id' => 'author_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getFans ()
		{
			return $this->hasMany(Fans::className(), ['author_id' => 'author_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getFansBehaviors ()
		{
			return $this->hasMany(FansBehavior::className(), ['author_id' => 'author_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getFansMsgMaterials ()
		{
			return $this->hasMany(FansMsgMaterial::className(), ['author_id' => 'author_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getFansStatistics ()
		{
			return $this->hasMany(FansStatistic::className(), ['author_id' => 'author_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getHighLevelPushMsgs ()
		{
			return $this->hasMany(HighLevelPushMsg::className(), ['author_id' => 'author_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getInteractReplies ()
		{
			return $this->hasMany(InteractReply::className(), ['author_id' => 'author_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getInteractReplyDetails ()
		{
			return $this->hasMany(InteractReplyDetail::className(), ['author_id' => 'author_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getKeywords ()
		{
			return $this->hasMany(Keyword::className(), ['author_id' => 'author_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getKfPushMsgs ()
		{
			return $this->hasMany(KfPushMsg::className(), ['author_id' => 'author_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getKfUsers ()
		{
			return $this->hasMany(KfUser::className(), ['author_id' => 'author_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getMaterials ()
		{
			return $this->hasMany(Material::className(), ['author_id' => 'author_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getMiniMsgMaterials ()
		{
			return $this->hasMany(MiniMsgMaterial::className(), ['author_id' => 'author_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getMiniUsers ()
		{
			return $this->hasMany(MiniUser::className(), ['author_id' => 'author_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getQuickMsgs ()
		{
			return $this->hasMany(QuickMsg::className(), ['author_id' => 'author_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getScenes ()
		{
			return $this->hasMany(Scene::className(), ['author_id' => 'author_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getTags ()
		{
			return $this->hasMany(Tags::className(), ['author_id' => 'author_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getTemplates ()
		{
			return $this->hasMany(Template::className(), ['author_id' => 'author_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getTemplatePushMsgs ()
		{
			return $this->hasMany(TemplatePushMsg::className(), ['author_id' => 'author_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getUserAuthorRelations ()
		{
			return $this->hasMany(UserAuthorRelation::className(), ['author_id' => 'author_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkUserAuthorRelations ()
		{
			return $this->hasMany(WorkUserAuthorRelation::className(), ['author_id' => 'author_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getMaterialPullTimes ()
		{
			return $this->hasMany(MaterialPullTime::className(), ['author_id' => 'author_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getConfig ()
		{
			return $this->hasOne(WxAuthorizeConfig::className(), ['id' => 'config_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWxAuthorizeInfo ()
		{
			return $this->hasOne(WxAuthorizeInfo::className(), ['author_id' => 'author_id']);
		}

		/**
		 * @param bool $withFunc
		 * @param bool $withConfig
		 *
		 * @return array
		 */
		public function dumpData ($withFunc = false, $withConfig = false)
		{
			$result = [
				'appid'        => $this->authorizer_appid,
				'access_token' => $this->authorizer_access_token,
				'expires'      => $this->authorizer_access_token_expires,
				'status'       => $this->authorizer_type,
			];

			if ($withFunc) {
				$result['func_info'] = $this->func_info;
			}

			if ($withConfig) {
				$result['config'] = $this->config;
			}

			return $result;
		}

		/**
		 * 获取授权状态的文案
		 *
		 * @return mixed
		 */
		public function getAuthorType ()
		{
			$data = [
				self::AUTH_TYPE_AUTH       => '已授权',
				self::AUTH_TYPE_UPDATEAUTH => '更新授权',
				self::AUTH_TYPE_UNAUTH     => '取消授权',
			];

			return $data[$this->authorizer_type];
		}

		/**
		 * 设置绑定信息
		 *
		 * @param $uid
		 * @param $configId
		 * @param $authorizeData
		 *
		 * @return int
		 * @throws \app\components\InvalidDataException
		 */
		public static function setAuthorizeData ($uid, $configId, $authorizeData)
		{
			$authorizeId = 0;
			$isNew       = false;

			// 事务处理
			$transaction = Yii::$app->db->beginTransaction();
			try {
				$authorize = static::findOne(['authorizer_appid' => $authorizeData['authorizer_appid']]);

				if (empty($authorize)) {
					$isNew                  = true;
					$authorize              = new WxAuthorize();
					$authorize->create_time = DateUtil::getCurrentTime();
				} else {
					$authorize->update_time = DateUtil::getCurrentTime();
				}

				if (empty($authorize->authorizer_type) || $authorize->authorizer_type == static::AUTH_TYPE_UNAUTH) {
					$authorize->authorizer_type = static::AUTH_TYPE_AUTH;
				}

				$authorize->config_id = $configId;
				$authorize->setAttributes($authorizeData);

				$authorize->authorizer_access_token_expires = (string) $authorize->authorizer_access_token_expires;
				$authorize->authorizer_code_expires         = (string) $authorize->authorizer_code_expires;

				if ($authorize->validate() && $authorize->save()) {
					$authorizeId = $authorize->author_id;
				}

				$transaction->commit();
			} catch (Exception $e) {
				$transaction->rollBack();
				throw new InvalidDataException($e->getMessage());
			}

			$user = User::findOne($uid);
			if ($isNew) {
				$limitAuthorNum = $user->limit_author_num > 0 ? $user->limit_author_num : Yii::$app->params['default_author_num'];
				if (count($user->userAuthorRelations) >= $limitAuthorNum) {
					throw new InvalidDataException("添加数量已达上限，请联系渠道经理。");
				}
			}

			if (!empty($authorizeId)) {
				UserAuthorRelation::setRelation($uid, $authorizeId);
			}

			return $authorizeId;
		}

		/**
		 * @param      $appid
		 * @param bool $withFunc
		 * @param bool $withConfig
		 *
		 * @return array
		 * @throws \app\components\InvalidDataException
		 * @throws \app\components\InvalidParameterException
		 * @throws \app\components\NotAllowException
		 */
		public static function getTokenInfo ($appid, $withFunc = false, $withConfig = false)
		{
			$result      = [];
			$wxAuthorize = WxAuthorize::findOne(['authorizer_appid' => $appid]);

			if (!empty($wxAuthorize) && $wxAuthorize->authorizer_type != static::AUTH_TYPE_UNAUTH) {
				if (empty($wxAuthorize->authorizer_access_token) || $wxAuthorize->authorizer_access_token_expires < (time() - 60)) {
					$relations = $wxAuthorize->userAuthorRelations;
					$apiOauth  = new apiOauth($relations[0]->uid, $wxAuthorize->config_id, $appid);
					$tokenData = $apiOauth->getAuthorizationAccessToken();

					if ($tokenData['status']) {
						$wxAuthorize = WxAuthorize::findOne(['authorizer_appid' => $appid]);
						$result      = $wxAuthorize->dumpData($withFunc, $withConfig);
					}
				} else {
					$result = $wxAuthorize->dumpData($withFunc, $withConfig);
				}
			}

			$cacheKey = implode('_', ['wechat_cache', $appid, 'access_token_' . $appid]);

			if (!empty($result)) {
				Yii::$app->getCache()->set($cacheKey, ['access_token' => $result['access_token'], 'expire' => $result['expires']], $result['expires'] - time());
			}

			return $result;
		}
	}
