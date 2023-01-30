<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%wx_msg}}".
	 *
	 * @property int    $id
	 * @property string $key            消息去重标识
	 * @property string $msg_type       消息类别
	 * @property string $msg_type_value 消息类别
	 * @property string $data           事件解密后数据
	 * @property int    $status         消息类别名称
	 * @property string $update_time    更新时间
	 * @property string $create_time    创建时间
	 */
	class WxMsg extends \yii\db\ActiveRecord
	{
		const EVENT_MSG = 'event';
		const TEXT_MSG = 'text';
		const IMAGE_MSG = 'image';
		const VOICE_MSG = 'voice';
		const VIDEO_MSG = 'video';
		const SHORTVIDEO_MSG = 'shortVideo';
		const LOCATION_MSG = 'location';
		const LINK_MSG = 'link';

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%wx_msg}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['key'], 'required'],
				[['msg_type_value', 'data'], 'string'],
				[['status'], 'integer'],
				[['update_time', 'create_time'], 'safe'],
				[['key'], 'string', 'max' => 256],
				[['msg_type'], 'string', 'max' => 64],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'             => Yii::t('app', 'ID'),
				'key'            => Yii::t('app', '消息去重标识'),
				'msg_type'       => Yii::t('app', '消息类别'),
				'msg_type_value' => Yii::t('app', '消息类别'),
				'data'           => Yii::t('app', '事件解密后数据'),
				'status'         => Yii::t('app', '消息类别名称'),
				'update_time'    => Yii::t('app', '更新时间'),
				'create_time'    => Yii::t('app', '创建时间'),
			];
		}
	}
