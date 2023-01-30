<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%message_push_detail}}".
	 *
	 * @property int         $id
	 * @property int         $uid        用户id
	 * @property int         $message_id 短信群发id
	 * @property string      $title      消息名称
	 * @property string      $phone      手机号码
	 * @property string      $sign_name  短信签名
	 * @property string      $type_name  短信类型
	 * @property string      $content    发送内容
	 * @property int         $status     状态：0未发送、1已发送、2发送失败、3发送中、4未知
	 * @property string      $push_time  发送时间
	 * @property string      $error_code 错误码
	 * @property string      $error_msg  错误信息
	 * @property string      $num        内容所占短信数
	 *
	 * @property MessagePush $message
	 * @property User        $u
	 */
	class MessagePushDetail extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%message_push_detail}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['uid', 'message_id', 'status'], 'integer'],
				[['create_time'], 'safe'],
				[['title', 'sign_name', 'type_name'], 'string', 'max' => 64],
				[['phone'], 'string', 'max' => 32],
				[['content'], 'string', 'max' => 350],
				[['message_id'], 'exist', 'skipOnError' => true, 'targetClass' => MessagePush::className(), 'targetAttribute' => ['message_id' => 'id']],
				[['uid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['uid' => 'uid']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'         => Yii::t('app', 'ID'),
				'uid'        => Yii::t('app', '用户id'),
				'message_id' => Yii::t('app', '短信群发id'),
				'title'      => Yii::t('app', '消息名称'),
				'phone'      => Yii::t('app', '手机号码'),
				'sign_name'  => Yii::t('app', '短信签名'),
				'type_name'  => Yii::t('app', '短信类型'),
				'content'    => Yii::t('app', '发送内容'),
				'status'     => Yii::t('app', '状态：0未发送、1已发送、2发送失败、3发送中、4未知'),
				'push_time'  => Yii::t('app', '发送时间'),
				'error_code' => Yii::t('app', '错误码'),
				'error_msg'  => Yii::t('app', '错误信息'),
				'num'        => Yii::t('app', '内容所占短信数'),
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
		public function getMessage ()
		{
			return $this->hasOne(MessagePush::className(), ['id' => 'message_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getU ()
		{
			return $this->hasOne(User::className(), ['uid' => 'uid']);
		}
	}
