<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%public_sea_contact_follow_record}}".
	 *
	 * @property int               $id
	 * @property int               $uid            账户ID
	 * @property int               $sub_id         子账户ID
	 * @property int               $sea_id         公海客户ID
	 * @property int               $user_id        成员ID
	 * @property string            $record         跟进记录
	 * @property string            $file           图片附件
	 * @property int               $add_time       添加时间
	 * @property int               $update_time    更新时间
	 * @property int               $status         是否有效：1是0否
	 * @property int               $follow_id      跟进状态id
	 * @property int               $is_master      状态：0主账户添加、1子账户添加
	 * @property int               $is_sync        是否已同步过：0否、1是
	 * @property int               $lose_id        输单原因id
     * @property int               $record_type 0：手动添加；1：电话记录
	 *
	 * @property PublicSeaCustomer $sea
	 * @property User              $u
	 */
	class PublicSeaContactFollowRecord extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%public_sea_contact_follow_record}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['uid', 'sub_id', 'sea_id', 'user_id', 'add_time', 'update_time', 'status', 'follow_id', 'is_master', 'is_sync', 'lose_id', 'record_type'], 'integer'],
				[['record'], 'string'],
				[['file'], 'string', 'max' => 1000],
				[['lose_id'], 'exist', 'skipOnError' => true, 'targetClass' => FollowLoseMsg::className(), 'targetAttribute' => ['lose_id' => 'id']],
				[['sea_id'], 'exist', 'skipOnError' => true, 'targetClass' => PublicSeaCustomer::className(), 'targetAttribute' => ['sea_id' => 'id']],
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
				'uid'         => Yii::t('app', '账户ID'),
				'sub_id'      => Yii::t('app', '子账户ID'),
				'sea_id'      => Yii::t('app', '公海客户ID'),
				'user_id'     => Yii::t('app', '成员ID'),
				'record'      => Yii::t('app', '跟进记录'),
				'file'        => Yii::t('app', '图片附件'),
				'add_time'    => Yii::t('app', '添加时间'),
				'update_time' => Yii::t('app', '更新时间'),
				'status'      => Yii::t('app', '是否有效：1是0否'),
				'follow_id'   => Yii::t('app', '跟进状态id'),
				'is_master'   => Yii::t('app', '状态：0主账户添加、1子账户添加 '),
				'is_sync'     => Yii::t('app', '是否已同步过：0否、1是'),
				'lose_id'     => Yii::t('app', '输单原因id'),
                'record_type' => Yii::t('app', '0：手动添加；1：电话记录'),
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
		public function getSea ()
		{
			return $this->hasOne(PublicSeaCustomer::className(), ['id' => 'sea_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getU ()
		{
			return $this->hasOne(User::className(), ['uid' => 'uid']);
		}
	}
