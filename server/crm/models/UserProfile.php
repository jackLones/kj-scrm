<?php

	namespace app\models;

	use app\util\DateUtil;
	use app\components\InvalidDataException;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%user_profile}}".
	 *
	 * @property int      $id
	 * @property int      $uid               用户ID
	 * @property string   $nick_name         昵称
	 * @property resource $avatar            头像
	 * @property string   $sex               性别
	 * @property string   $department        部门
	 * @property string   $position          职务
	 * @property string   $company_name      企业名称
	 * @property string   $company_logo      企业logo
	 * @property string   $update_time       更新时间
	 * @property string   $create_time       创建时间
	 * @property int      $province          省份
	 * @property int      $city              城市
	 * @property string   $email             邮箱
	 * @property string   $qq                qq
	 * @property string   $weixin            微信
	 *
	 * @property User     $u
	 */
	class UserProfile extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%user_profile}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['uid'], 'required'],
				[['uid', 'sex', 'province', 'city'], 'integer'],
				[['avatar', 'department', 'position', 'company_name', 'company_logo', 'email', 'qq', 'weixin'], 'string'],
				[['update_time', 'create_time'], 'safe'],
				[['nick_name'], 'string', 'max' => 64],
				[['uid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['uid' => 'uid']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'           => Yii::t('app', 'ID'),
				'uid'          => Yii::t('app', '用户ID'),
				'nick_name'    => Yii::t('app', '昵称'),
				'avatar'       => Yii::t('app', '头像'),
				'sex'          => Yii::t('app', '性别'),
				'department'   => Yii::t('app', '部门'),
				'position'     => Yii::t('app', '职务'),
				'company_name' => Yii::t('app', '企业名称'),
				'company_logo' => Yii::t('app', '企业logo'),
				'update_time'  => Yii::t('app', '更新时间'),
				'create_time'  => Yii::t('app', '创建时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getU ()
		{
			return $this->hasOne(User::className(), ['uid' => 'uid']);
		}

		/**
		 * 创建用户信息
		 *
		 * @param $uid
		 *
		 * @throws InvalidDataException
		 */
		public static function create ($uid, $profileData = [])
		{
			$user_pro = UserProfile::findOne($uid);
			if (empty($user_pro)) {
				$user_pro              = new UserProfile();
				$user_pro->create_time = DateUtil::getCurrentTime();
				$user_pro->position    = '创建人';
			} else {
				$user_pro->update_time = DateUtil::getCurrentTime();
			}
			$user_pro->uid = $uid;

			if (!empty($profileData)) {
				foreach ($profileData as $k => $v) {
					$user_pro->$k = $v;
				}
			}

			if ($user_pro->dirtyAttributes) {
				if (!$user_pro->validate() || !$user_pro->save()) {
					throw new InvalidDataException(SUtils::modelError($user_pro));
				}
			}

		}
	}
