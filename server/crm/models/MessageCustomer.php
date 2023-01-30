<?php

	namespace app\models;

	use app\util\DateUtil;
	use Yii;

	/**
	 * This is the model class for table "{{%message_customer}}".
	 *
	 * @property int    $id
	 * @property int    $uid         用户ID
	 * @property string $phone       手机号
	 * @property string $name        姓名
	 * @property string $nickname    微信昵称
	 * @property int    $sex         性别，0：未知、1：男、2：女
	 * @property string $remark      备注
	 * @property int    $status      状态，0：不可用、1：可用
	 * @property string $update_time 修改时间
	 * @property string $create_time 创建时间
	 *
	 * @property User   $u
	 */
	class MessageCustomer extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%message_customer}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['uid', 'sex', 'status'], 'integer'],
				[['remark'], 'string'],
				[['update_time', 'create_time'], 'safe'],
				[['phone', 'name', 'nickname'], 'string', 'max' => 32],
				[['uid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['uid' => 'uid']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => Yii::t('app', 'ID'),
				'uid'         => Yii::t('app', '用户ID'),
				'phone'       => Yii::t('app', '手机号'),
				'name'        => Yii::t('app', '姓名'),
				'nickname'    => Yii::t('app', '微信昵称'),
				'sex'         => Yii::t('app', '性别，0：未知、1：男、2：女'),
				'remark'      => Yii::t('app', '备注'),
				'status'      => Yii::t('app', '状态，0：不可用、1：可用'),
				'update_time' => Yii::t('app', '修改时间'),
				'create_time' => Yii::t('app', '创建时间'),
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
		 *
		 * @return object|\yii\db\Connection|null
		 *
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function getDb ()
		{
			return Yii::$app->get('mdb');
		}

		//导入客户
		public static function setCustomer ($data)
		{
			$uid      = $data['uid'];
			$phone    = trim($data['phone']);
			$name     = !empty($data['name']) ? $data['name'] : '';
			$nickname = !empty($data['nickname']) ? $data['nickname'] : '';
			$sex      = !empty($data['sex']) ? $data['sex'] : '';
			$remark   = !empty($data['remark']) ? $data['remark'] : '';
			if (empty($uid) || empty($phone)) {
				return 'skipPhone';
			}
			if (!empty($phone)) {
				if (!preg_match("/^((13[0-9])|(14[0-9])|(15([0-9]))|(16([0-9]))|(17([0-9]))|(18[0-9])|(19[0-9]))\d{8}$/", $phone)) {
					return 'skipPhone';
				}
			}
			$info = static::findOne(['uid' => $uid, 'phone' => $phone]);
			if (!empty($info)) {
				return 'skip';
			}
			$customer           = new MessageCustomer();
			$customer->uid      = $uid;
			$customer->phone    = $phone;
			$customer->name     = $name;
			$customer->nickname = $nickname;
			if ($sex == '男') {
				$sex = 1;
			} elseif ($sex == '女') {
				$sex = 2;
			} else {
				$sex = 0;
			}
			$customer->sex         = $sex;
			$customer->remark      = $remark;
			$customer->status      = 1;
			$customer->create_time = DateUtil::getCurrentTime();
			if (!$customer->validate() || !$customer->save()) {
				return 'skip';
			}

			return 'insert';
		}
	}
