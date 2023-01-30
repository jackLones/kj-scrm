<?php

	namespace app\models;

	use app\util\DateUtil;
	use Yii;

	/**
	 * This is the model class for table "{{%user_package}}".
	 *
	 * @property int     $id
	 * @property int     $user_id     用户id
	 * @property int     $package_id  套餐id
	 * @property int     $start_time  开始时间
	 * @property int     $end_time    到期时间
	 * @property string  $update_time 修改时间
	 * @property string  $create_time 创建时间
	 *
	 * @property Package $package
	 * @property User    $user
	 */
	class UserPackage extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%user_package}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['user_id', 'package_id', 'start_time', 'end_time'], 'integer'],
				[['update_time', 'create_time'], 'safe'],
				[['package_id'], 'exist', 'skipOnError' => true, 'targetClass' => Package::className(), 'targetAttribute' => ['package_id' => 'id']],
				[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'uid']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => Yii::t('app', 'ID'),
				'user_id'     => Yii::t('app', '用户id'),
				'package_id'  => Yii::t('app', '套餐id'),
				'start_time'  => Yii::t('app', '开始时间'),
				'end_time'    => Yii::t('app', '到期时间'),
				'update_time' => Yii::t('app', '修改时间'),
				'create_time' => Yii::t('app', '创建时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getPackage ()
		{
			return $this->hasOne(Package::className(), ['id' => 'package_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getUser ()
		{
			return $this->hasOne(User::className(), ['uid' => 'user_id']);
		}

		//设置用户套餐关联表

		/**
		 * 设置用户套餐关联表
		 * $uid 用户id
		 * $packageId 套餐id
		 * $end_time 到期时间
		 */
		public static function setUserPackage ($uid, $packageId = 0, $end_time = 0)
		{
			if (empty($uid)) {
				return '';
			}
			$userPackage = static::findOne(['user_id' => $uid]);
			$time        = time();
			$is_save     = true;
			if (!empty($userPackage)) {
				if (!empty($packageId)) {
					$userPackage->package_id = $packageId;
					$userPackage->start_time = $time;
					$userPackage->end_time   = $end_time;
				} elseif (!empty($userPackage->end_time) && ($userPackage->end_time < $time)) {
					$package                 = Package::findOne(['is_trial' => 1]);
					$userPackage->package_id = $package->id;
					$userPackage->start_time = 0;
					$userPackage->end_time   = 0;
				} else {
					$is_save = false;
				}
				$userPackage->update_time = DateUtil::getCurrentTime();
			} else {
				$userPackage          = new UserPackage();
				$userPackage->user_id = $uid;
				if (empty($packageId)) {
					$package                 = Package::findOne(['is_trial' => 1]);
					$userPackage->package_id = $package->id;
				} else {
					$userPackage->package_id = $packageId;
				}
				$userPackage->update_time = DateUtil::getCurrentTime();
				$userPackage->create_time = DateUtil::getCurrentTime();
			}
			if ($is_save) {
				$userPackage->save();
			}
		}
	}
