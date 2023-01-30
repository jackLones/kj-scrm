<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\DateUtil;
	use app\util\StringUtil;
	use app\util\SUtils;
	use yii\db\ActiveRecord;
	use yii\db\Expression;
	use yii\web\IdentityInterface;
	use Yii;

	/**
	 * This is the model class for table "{{%user}}".
	 *
	 * @property int                  $uid
	 * @property string               $account             账户名
	 * @property string               $password            加密后的密码
	 * @property string               $salt                加密校验码
	 * @property int                  $status              账号状态：0、禁用；1、正常
	 * @property string               $access_token        对接验证字符串
	 * @property int                  $access_token_expire 对接验证字符串失效时间戳
	 * @property string               $update_time         更新时间
	 * @property string               $create_time         创建时间
	 * @property string               $message_num         短信数量
	 * @property string               $company_name        企业名称
	 * @property string               $company_logo        企业logo
	 * @property int                  $limit_corp_num      可授权企业微信数量
	 * @property int                  $limit_author_num    可授权公众号数量
	 * @property int                  $package_id          套餐id
	 * @property int                  $package_time        套餐时长
	 * @property int                  $time_type           套餐时长类型:1日2月3年
	 * @property int                  $end_time            套餐失效时间
	 * @property int                  $login_time          最后登录时间
	 * @property int                  $is_merchant         是否入驻1是0否
	 * @property int                  $merchant_time       入驻时间
	 * @property int                  $source              用户来源：1自助注册2手动录入
	 * @property int                  $agent_uid           代理商id 0总后台
	 * @property int                  $eid                 后台员工id
	 * @property int                  $application_status  客户资料状态：1已提交未审核，2审核通过，3审核失败
	 * @property int                  $is_hide_phone       手机号是否隐藏1是0否
	 * @property int                  $sub_num             允许子账户数量
	 * @property int                  $is_sync_image       是否同步图片
	 * @property int                  $is_sync_voice       是否同步音频
	 * @property int                  $is_sync_video       是否同步视频
	 * @property int                  $is_sync_news        是否同步图文
	 *
	 * @property Attachment[]         $attachments
	 * @property QuickMsg[]           $quickMsgs
	 * @property SubUser[]            $subUsers
	 * @property UserAuthorRelation[] $userAuthorRelations
	 * @property UserCorpRelation[]   $userCorpRelations
	 * @property UserProfile          $userProfile
	 * @property UserYouzanRelation[] $userYouzanRelations
	 */
	class User extends ActiveRecord implements IdentityInterface
	{
		const USER_TYPE = 'MainUser';
		const USER_HIDE_PHONE = 13;//测试站uid=2,正式站uid=13

		const FORBIDDEN_USER = 0;
		const NORMAL_USER = 1;

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%user}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['account'], 'required'],
				[['sub_num', 'status', 'access_token_expire', 'limit_corp_num', 'limit_author_num', 'package_id', 'package_time', 'time_type', 'end_time', 'login_time', 'is_merchant', 'merchant_time', 'source', 'agent_uid', 'eid', 'application_status', 'is_hide_phone'], 'integer'],
				[['update_time', 'create_time'], 'safe'],
				[['account'], 'string', 'max' => 64],
				[['company_name'], 'string', 'max' => 50],
				[['company_logo'], 'string', 'max' => 100],
				[['password', 'access_token'], 'string', 'max' => 255],
				[['salt'], 'string', 'max' => 6],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'uid'                 => Yii::t('app', 'Uid'),
				'account'             => Yii::t('app', '账户名'),
				'password'            => Yii::t('app', '加密后的密码'),
				'salt'                => Yii::t('app', '加密校验码'),
				'status'              => Yii::t('app', '账号状态：0、禁用；1、正常'),
				'access_token'        => Yii::t('app', '对接验证字符串'),
				'access_token_expire' => Yii::t('app', '对接验证字符串失效时间戳'),
				'update_time'         => Yii::t('app', '更新时间'),
				'create_time'         => Yii::t('app', '创建时间'),
				'message_num'         => Yii::t('app', '短信数量'),
				'company_name'        => Yii::t('app', '企业名称'),
				'company_logo'        => Yii::t('app', '企业logo'),
				'limit_corp_num'      => Yii::t('app', '可授权企业微信数量'),
				'limit_author_num'    => Yii::t('app', '可授权公众号数量'),
				'package_id'          => Yii::t('app', '套餐id'),
				'package_time'        => Yii::t('app', '套餐时长'),
				'time_type'           => Yii::t('app', '套餐时长类型:1日2月3年'),
				'end_time'            => Yii::t('app', '套餐失效时间'),
				'login_time'          => Yii::t('app', '最后登录时间'),
				'is_merchant'         => Yii::t('app', '是否入驻1是0否'),
				'merchant_time'       => Yii::t('app', '入驻时间'),
				'source'              => Yii::t('app', '用户来源：1自助注册2手动录入'),
				'agent_uid'           => Yii::t('app', '代理商id 0总后台'),
				'eid'                 => Yii::t('app', '后台员工id'),
				'application_status'  => Yii::t('app', '客户资料状态：1已提交未审核，2审核通过，3审核失败'),
				'is_hide_phone     '  => Yii::t('app', '手机号是否隐藏1是0否'),
				'sub_num'             => Yii::t('app', '允许子账户数量'),
				'is_sync_image'       => Yii::t('app', '是否同步图片'),
				'is_sync_voice'       => Yii::t('app', '是否同步音频'),
				'is_sync_video'       => Yii::t('app', '是否同步视频'),
				'is_sync_news'        => Yii::t('app', '是否同步图文'),
			];
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
		 * @return \yii\db\ActiveQuery
		 */
		public function getAttachments ()
		{
			return $this->hasMany(Attachment::className(), ['uid' => 'uid']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getQuickMsgs ()
		{
			return $this->hasMany(QuickMsg::className(), ['uid' => 'uid']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getSubUsers ()
		{
			return $this->hasMany(SubUser::className(), ['uid' => 'uid']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getUserAuthorRelations ()
		{
			return $this->hasMany(UserAuthorRelation::className(), ['uid' => 'uid']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getUserCorpRelations ()
		{
			return $this->hasMany(UserCorpRelation::className(), ['uid' => 'uid']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getUserProfile ()
		{
			return $this->hasOne(UserProfile::className(), ['uid' => 'uid']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getUserYouzanRelations ()
		{
			return $this->hasMany(UserYouzanRelation::className(), ['uid' => 'uid']);
		}

		/**
		 * {@inheritdoc}
		 */
		public static function findIdentity ($id)
		{
			return static::findOne($id);
		}

		/**
		 * {@inheritdoc}
		 */
		public static function findIdentityByAccessToken ($token, $type = NULL)
		{
			return static::findOne(['access_token' => $token]);
		}

		/**
		 * Finds user by username
		 *
		 * @param string $identifier
		 * @param bool   $withProfile
		 *
		 * @return User|array|ActiveRecord|null
		 */
		public static function findIdentityByIdentifier ($identifier, $withProfile = false)
		{
			if ($withProfile) {
				return static::find()->where(['account' => $identifier])->with('userProfile')->one();
			} else {
				return static::findOne(['account' => $identifier]);
			}
		}

		/**
		 * {@inheritdoc}
		 */
		public function getId ()
		{
			return $this->uid;
		}

		/**
		 * {@inheritdoc}
		 */
		public function getAuthKey ()
		{
			return $this->access_token;
		}

		/**
		 * @return int
		 */
		public function getAuthExpire ()
		{
			return $this->access_token_expire;
		}

		/**
		 * {@inheritdoc}
		 */
		public function validateAuthKey ($authKey)
		{
			return $this->getAuthKey() === $authKey;
		}

		/**
		 * Validates password
		 *
		 * @param string $password password to validate
		 *
		 * @return bool if password provided is valid for current user
		 */
		public function validatePassword ($password)
		{
			return $this->password === StringUtil::encodePassword($this->salt, $password);
		}

		/**
		 * @param      $identifier
		 * @param bool $force
		 * @param bool $autoLogin
		 *
		 * @return User|array|ActiveRecord|null
		 *
		 * @throws \Throwable
		 * @throws \yii\db\StaleObjectException
		 */
		public static function refreshToken ($identifier, $force = false, $autoLogin = false)
		{

			$user = static::findIdentityByIdentifier($identifier);

			$ignoreIp  = [
				'60.173.195.178',   // 公司
				'36.7.87.202',  // 渠道售后
				'114.102.149.42'  // Zhang Ting
			];
			$ignoreUid = [];
			if ($force || (!in_array(SUtils::getClientIP(), $ignoreIp) && !in_array($user->uid, $ignoreUid)) || empty($user->access_token)) {
				$accessToken = StringUtil::uuid();

				if (static::findIdentityByAccessToken($accessToken)) {
					return static::refreshToken($identifier);
				}

				$user->access_token = $accessToken;
			}

			$expireTime                = $autoLogin ? strtotime("+3day") : strtotime('+2hour');
			$user->access_token_expire = $expireTime;
			$user->update_time         = DateUtil::getCurrentTime();
			$user->update();
			$user->refresh();

			return $user;
		}

		/**
		 * 更新token失效时间
		 *
		 * @return bool
		 * @throws \Throwable
		 * @throws \yii\db\StaleObjectException
		 */
		public function refreshTokenExpire ()
		{
			$newExpireTime = strtotime('+2hour');
			if ($this->access_token_expire < $newExpireTime) {
				$this->access_token_expire = $newExpireTime;
			}

			$this->update_time = DateUtil::getCurrentTime();
			$this->update();
			$this->refresh();

			return true;
		}

		/**
		 * @param integer $account
		 * @param string  $password
		 *
		 * @return User|null
		 * @throws InvalidDataException
		 */
		public static function create ($account, $password, $source = 1, $profileData = [], $agent_uid = 0, $eid = 0)
		{
			$user = static::findIdentityByIdentifier($account);

			if (!empty($user)) {
				throw new InvalidDataException('用户已存在！');
			}

			$user              = new User();
			$user->account     = $account;
			$user->status      = self::NORMAL_USER;
			if (!empty($password)) {
				$user->salt     = StringUtil::randomStr(6, true);
				$user->password = StringUtil::encodePassword($user->salt, $password);
			}
			$user->create_time = DateUtil::getCurrentTime();
			$user->source      = $source;
			$user->agent_uid   = $agent_uid;
			$user->eid         = $eid;

			//默认套餐
			$package            = Package::getDefaultPackage();
			$user->package_id   = $package->package_id;
			$user->package_time = $package->duration;
			$user->time_type    = $package->duration_type;
			if ($user->time_type == 2) {
				$time_type = 'month';
			} elseif ($user->time_type == 3) {
				$time_type = 'year';
			} else {
				$time_type = 'day';
			}
			$user->end_time = strtotime("+$user->package_time $time_type");

			if ($user->end_time % 86400 != 0) {
				$user->end_time = strtotime(date('Y-m-d', $user->end_time)) + 86399;
			}

			if ($user->validate() && $user->save()) {
				//UserPackage::setUserPackage($user->uid);
				UserProfile::create($user->uid, $profileData);

				$data['uid']      = $user->uid;
				$data['account']  = $account;
				$data['status']   = 1;
				$data['password'] = $password;
				$data['type']     = 1;
				SubUser::add($data);

				return static::findOne($user->uid);
			} else {
				throw new InvalidDataException(SUtils::modelError($user));
			}
		}

		/**
		 * 检测账号
		 *
		 * @param $account
		 *
		 * @return bool
		 *
		 * @throws InvalidDataException
		 */
		public static function checkAccount ($account)
		{
			$has_user = false;
			$user     = static::findOne(['account' => $account]);
			if (!empty($user) && $user->status == self::NORMAL_USER) {
				$has_user = true;
			}
			$sub_user = SubUser::findAll(['account' => $account, 'type' => 0]);
			if (!empty($sub_user) && !$has_user) {
				if (count($sub_user) == 1) {
					if ($sub_user[0]->status == 0) {
						throw new InvalidDataException('无法登录，请检查手机号或密码是否正确');
					}
					if ($sub_user[0]->status == 2) {
						throw new InvalidDataException('账户已被禁用，无法登录');
					}
				}

				foreach ($sub_user as $subUser) {
					if ($subUser->u->status == self::NORMAL_USER) {
						$has_user = true;

						break;
					}
				}
			}

			if (!$has_user && !empty($user) && $user->status != self::NORMAL_USER) {
				throw new InvalidDataException('账户异常，无法登录');
			}

			return $has_user;
		}

		/**
		 * 检查账号和密码
		 *
		 * @param $account
		 * @param $password
		 *
		 * @return mixed
		 *
		 * @throws InvalidDataException
		 */
		public static function checkAccountPassword ($account, $password)
		{
			$check        = false;
			$num          = 0;
			$new_user     = '';
			$new_sub_user = '';
			$user         = static::findOne(['account' => $account]);
			if (!empty($user) && $user->status == self::NORMAL_USER) {
				$password_new = StringUtil::encodePassword($user->salt, $password);
				if ($password_new === $user->password) {
					$check = true;
					$num++;
					$new_user = $user;
				}
			}
			$sub_user = SubUser::find()->andWhere(['account' => $account, 'status' => 1, 'type' => 0])->all();
			if (!empty($sub_user)) {
				/** @var SubUser $info */
				foreach ($sub_user as $info) {
					$password_new = StringUtil::encodePassword($info->salt, $password);
					if ($password_new === $info->password && $info->u->status == self::NORMAL_USER) {
						$check = true;
						$num++;
						$new_sub_user = $info;
					}
				}
			}
			if (!$check) {
				if (!empty($user)) {
					$password_new = StringUtil::encodePassword($user->salt, $password);
					if ($password_new !== $user->password) {
						throw new InvalidDataException('无法登录，请检查手机号或密码是否正确');
					}

					if ($user->status != self::NORMAL_USER) {
						throw new InvalidDataException('账户异常，无法登录');
					}
				}

				if (!empty($sub_user)) {
					$hasForbidden     = false;
					$hasWrongPassword = false;

					/** @var SubUser $subUser */
					foreach ($sub_user as $subUser) {
						if ($subUser->u->status != self::NORMAL_USER) {
							$hasForbidden = true;
						}

						$password_new = StringUtil::encodePassword($subUser->salt, $password);
						if ($password_new !== $subUser->password) {
							$hasWrongPassword = true;
						}
					}

					if ($hasWrongPassword) {
						throw new InvalidDataException('无法登录，请检查手机号或密码是否正确');
					}

					if ($hasForbidden) {
						throw new InvalidDataException('商家账户异常，无法登录');
					}
				}

				$subUser = SubUser::findOne(['account' => $account, 'status' => 2, 'type' => 0]);
				if (!empty($subUser)) {
					throw new InvalidDataException('账户已被禁用，无法登录');
				}
			}
			$result['check']        = $check;
			$result['num']          = $num;
			$result['new_user']     = $new_user;
			$result['new_sub_user'] = $new_sub_user;

			return $result;
		}

		/**
		 * 检查主账号和密码
		 *
		 * @param $account
		 * @param $password
		 *
		 * @return mixed
		 *
		 * @throws InvalidDataException
		 */
		public static function checkUserAccountPassword ($account, $password)
		{
			$check    = false;
			$num      = 0;
			$new_user = '';
			$user     = static::findOne(['account' => $account]);
			if (!empty($user)) {
				if ($user->status != self::NORMAL_USER) {
					throw new InvalidDataException('账户异常，无法登录');
				}

				$password_new = StringUtil::encodePassword($user->salt, $password);
				if ($password_new === $user->password) {
					$check = true;
					$num++;
					$new_user = $user;
				}
			}

			$result['check']    = $check;
			$result['num']      = $num;
			$result['new_user'] = $new_user;

			return $result;
		}

		/**
		 * 获取公众号数据
		 *
		 * @param $uid
		 *
		 * @return mixed
		 *
		 */
		public static function getAuthData ($uid)
		{
			$authData = UserAuthorRelation::find()->alias('uar');
			$authData = $authData->leftJoin('{{%wx_authorize}} wx', '`wx`.`author_id` = `uar`.`author_id`');
			$authData = $authData->leftJoin('{{%wx_authorize_info}} wai', '`wai`.`author_id` = `wx`.`author_id`');
			$authData = $authData->select('wx.*,wai.nick_name,wai.head_img,wai.user_name,wai.qrcode_url');
			$authData = $authData->where(['uar.uid' => $uid]);
			$authData = $authData->andWhere(['in', 'wx.authorizer_type', ["authorized", "updateauthorized"]]);
			$authData = $authData->asArray()->all();

			return $authData;
		}

		/**
		 * 设置客户入驻
		 *
		 * @param $uid
		 * @param $agentOrder
		 *
		 * @return array
		 *
		 * @throws InvalidDataException
		 */
		public static function setUserMerchant ($uid, $agentOrder)
		{
			$user = static::findOne($uid);
			if (empty($user)) {
				throw new InvalidDataException('客户数据错误');
			}

			$time                = time();
			$user->package_id    = $agentOrder->package_id;
			$user->package_time  = $agentOrder->package_time;
			$user->time_type     = $agentOrder->time_type;
			$user->is_merchant   = 1;
			$user->merchant_time = empty($user->merchant_time) ? $time : $user->merchant_time;
			//套餐结束时间
			if ($agentOrder->type == 3){
				$end_time = $user->end_time;
			}else{
				if ($user->time_type == 2) {
					$time_type = 'month';
				} elseif ($user->time_type == 3) {
					$time_type = 'year';
				} else {
					$time_type = 'day';
				}
				if ($time > $user->end_time) {
					$end_time = strtotime("+$user->package_time $time_type");
				} else {
					$now_end_time = date('Y-m-d H:i:s', $user->end_time);
					$end_time     = strtotime("$now_end_time +$user->package_time $time_type");
				}
			}
			//赠送时间
			$extrainfo = !empty($agentOrder->extrainfo) ? json_decode($agentOrder->extrainfo, true) : [];
			if (!empty($extrainfo) && $extrainfo['sendTimeNum'] > 0) {
				$sendTimeNum = $extrainfo['sendTimeNum'];
				if ($extrainfo['sendTimeType'] == 2) {
					$sendTimeType = 'month';
				} elseif ($extrainfo['sendTimeType'] == 3) {
					$sendTimeType = 'year';
				} else {
					$sendTimeType = 'day';
				}
				$now_end_time = date('Y-m-d H:i:s', $end_time);
				$end_time     = strtotime("$now_end_time +$sendTimeNum $sendTimeType");
			}
			if ($end_time % 86400 == 0) {
				$user->end_time = $end_time;
			} else {
				$user->end_time = strtotime(date('Y-m-d', $end_time)) + 86399;
			}

			if ($user->validate() && $user->save()) {
				return ['error' => 0, 'end_time' => $user->end_time];
			} else {
				throw new InvalidDataException(SUtils::modelError($user));
			}
		}

        /*
         * 根据uid获取对应套餐的引流客户数量
         * */
        public static function getPackageAboutNum($uid = 0, $key = '')
        {
            if (!$uid || !$key) {
                return 0;
            }
            $data = static::find()->alias('a')
                ->select('b.fission_num,lottery_draw_num,red_envelopes_num')
                ->leftJoin(Package::tableName() . ' b', 'a.package_id=b.id')
                ->where(['a.uid'=>$uid])
                ->asArray()
                ->one();
            if (!$data) {
                return 0;
            }

            return $data[$key] ?? 0;

        }

	}
