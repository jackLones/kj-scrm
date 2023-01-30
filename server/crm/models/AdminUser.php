<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\DateUtil;
	use app\util\StringUtil;
	use app\util\SUtils;
	use Yii;
	use yii\db\ActiveRecord;
	use yii\db\Expression;
	use yii\web\IdentityInterface;

	/**
	 * This is the model class for table "{{%admin_user}}".
	 *
	 * @property int    $id
	 * @property string $account      账户名
	 * @property string $phone        手机号（代理商）
	 * @property string $password     加密后的密码
	 * @property string $salt         加密校验码
	 * @property int    $type         帐号类型：0总账户 1独家代理 2普通代理
	 * @property string $access_token 对接验证字符串
	 * @property int    $status       是否启用，1：启用、0：不启用
	 * @property string $update_time  修改时间
	 * @property string $create_time  创建时间
	 *
	 */
	class AdminUser extends ActiveRecord implements IdentityInterface
	{
		const AGENT_PASSWORD = 'm123456';//默认代理商密码
		const AGENT_DEFAULT_STATUS = 0;//默认代理商状态

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%admin_user}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['status', 'type'], 'integer'],
				[['update_time', 'create_time'], 'safe'],
				[['account', 'phone'], 'string', 'max' => 64],
				[['password'], 'string', 'max' => 250],
				[['salt'], 'string', 'max' => 6],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'           => Yii::t('app', 'ID'),
				'account'      => Yii::t('app', '账户名'),
				'phone'        => Yii::t('app', '手机号（代理商）'),
				'password'     => Yii::t('app', '加密后的密码'),
				'salt'         => Yii::t('app', '加密校验码'),
				'type'         => Yii::t('app', '帐号类型：0总账户 1独家代理 2普通代理'),
				'access_token' => Yii::t('app', '对接验证字符串'),
				'status'       => Yii::t('app', '是否启用，1：启用、0：不启用'),
				'update_time'  => Yii::t('app', '修改时间'),
				'create_time'  => Yii::t('app', '创建时间'),
			];
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
		 *
		 * @return User|array|ActiveRecord|null
		 * @throws InvalidDataException
		 */
		public static function findIdentityByIdentifier ($identifier)
		{
			$eid     = 0;
			$account = $identifier;
			//员工登录
			if (strpos($identifier, "@") !== false) {
				$name = explode('@', $identifier);
				if (empty($name[0]) || empty($name[1])) {
					throw new InvalidDataException('账号或密码错误！');
				}
				$employeeName = $name[0];
				$account      = $name[1];

				$adminUser = static::findOne(['account' => $account]);
				if (empty($adminUser)) {
					throw new InvalidDataException('账号或密码错误！');
				}

				$employee = AdminUserEmployee::findOne(['account' => $employeeName, 'uid' => $adminUser->id]);
				if (empty($employee)) {
					throw new InvalidDataException('账号或密码错误！');
				}
				if ($employee->status == 0) {
					throw new InvalidDataException('账号未启用！');
				}
				if ($adminUser->type == 0) {
					$role = SystemRole::findOne($employee->role_id);
					if ($role->status == 0) {
						throw new InvalidDataException('账号已禁用！');
					}
				}

				$eid = $employee->id;

				Yii::$app->adminUserEmployee->login(AdminUserEmployee::findIdentityByIdentifier($employeeName, $adminUser->id), 3600 * 24 * 30);
			} else {
				$adminUser = static::findOne(['account' => $account]);
				if (empty($adminUser)) {
					throw new InvalidDataException('账号或密码错误！');
				}
			}

			//代理商信息
			if ($adminUser && $adminUser->type != 0) {
				if ($adminUser['status'] == 0) {
					throw new InvalidDataException('帐号未启用！');
				} else {
					$agent = Agent::findOne(['uid' => $adminUser->id]);
					if ($agent->is_contract == 0 || $agent->endtime < time()) {
						throw new InvalidDataException('帐号签约时间已到期！');
					}
				}
			}

			if ($eid && $employee->uid != $adminUser->id) {
				throw new InvalidDataException('帐号错误！');
			}

			if ($eid) {
				$adminUser->password = $employee->pwd;
				$adminUser->salt     = $employee->salt;
			}

			return $adminUser;
		}

		/**
		 * {@inheritdoc}
		 */
		public function getId ()
		{
			return $this->id;
		}

		/**
		 * {@inheritdoc}
		 */
		public function getAuthKey ()
		{
			return $this->access_token;
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
		 * @param string $account
		 * @param string $phone
		 *
		 * @return User|null
		 * @throws InvalidDataException
		 */
		public static function create ($account, $phone, $agentData = [])
		{
			$adminUser = static::findOne(['account' => $account]);

			if (!empty($adminUser)) {
				throw new InvalidDataException('用户已存在！');
			}

			$adminUser              = new AdminUser();
			$adminUser->account     = $account;
			$adminUser->phone       = $phone;
			$adminUser->salt        = StringUtil::randomStr(6, true);
			$adminUser->status      = self::AGENT_DEFAULT_STATUS;
			$adminUser->password    = StringUtil::encodePassword($adminUser->salt, self::AGENT_PASSWORD);
			$adminUser->create_time = DateUtil::getCurrentTime();
			$adminUser->type        = $agentData['type'];

			if ($adminUser->validate() && $adminUser->save()) {
				Agent::create($adminUser->id, $agentData);

				return true;
			} else {
				throw new InvalidDataException(SUtils::modelError($adminUser));
			}
		}

		/**
		 * @param integer $uid
		 *
		 * @return User|null
		 * @throws InvalidDataException
		 */
		public static function uptUser ($uid, $phone, $agentData = [])
		{
			$adminUser = static::findOne($uid);
			if (empty($adminUser)) {
				throw new InvalidDataException('代理商信息错误！');
			}

			$adminUser->phone       = $phone;
			$adminUser->type        = $agentData['type'];
			$adminUser->update_time = DateUtil::getCurrentTime();

			if ($adminUser->validate() && $adminUser->save()) {
				Agent::create($adminUser->id, $agentData);

				return true;
			} else {
				throw new InvalidDataException(SUtils::modelError($adminUser));
			}
		}
	}
