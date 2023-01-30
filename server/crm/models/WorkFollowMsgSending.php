<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%work_follow_msg_sending}}".
	 *
	 * @property int           $id
	 * @property int           $corp_id     企业ID
	 * @property int           $agentid     授权方应用id
	 * @property int           $msg_id      跟进提醒表id
	 * @property int           $date_time   当日时间（Ymd）
	 * @property string        $send_time   发送时间（时:分）
	 * @property int           $push_type   0立即发送1指定时间发送
	 * @property int           $queue_id    队列id
	 * @property int           $status      发送状态 0未发送 1已发送 2发送失败
	 * @property string        $push_time   成功发送时间
	 * @property string        $error_msg   错误信息
	 * @property int           $error_code  错误码
	 * @property int           $is_del      删除状态 0 未删除 1 已删除
	 * @property string        $create_time 创建时间
	 * @property string        $update_time 更新时间
	 *
	 * @property WorkCorp      $corp
	 * @property WorkFollowMsg $msg
	 */
	class WorkFollowMsgSending extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_follow_msg_sending}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_id', 'agentid', 'msg_id', 'date_time', 'push_type', 'queue_id', 'status', 'error_code', 'is_del'], 'integer'],
				[['push_time', 'create_time', 'update_time'], 'safe'],
				[['send_time'], 'string', 'max' => 50],
				[['error_msg'], 'string', 'max' => 255],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
				[['msg_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkFollowMsg::className(), 'targetAttribute' => ['msg_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => Yii::t('app', 'ID'),
				'corp_id'     => Yii::t('app', '企业ID'),
				'agentid'     => Yii::t('app', '授权方应用id'),
				'msg_id'      => Yii::t('app', '跟进提醒表id'),
				'date_time'   => Yii::t('app', '当日时间（Ymd）'),
				'send_time'   => Yii::t('app', '发送时间（时:分）'),
				'push_type'   => Yii::t('app', '0立即发送1指定时间发送'),
				'queue_id'    => Yii::t('app', '队列id'),
				'status'      => Yii::t('app', '发送状态 0未发送 1已发送 2发送失败'),
				'push_time'   => Yii::t('app', '成功发送时间'),
				'error_msg'   => Yii::t('app', '错误信息'),
				'error_code'  => Yii::t('app', '错误码'),
				'is_del'      => Yii::t('app', '删除状态 0 未删除 1 已删除'),
				'create_time' => Yii::t('app', '创建时间'),
				'update_time' => Yii::t('app', '更新时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getCorp ()
		{
			return $this->hasOne(WorkCorp::className(), ['id' => 'corp_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getMsg ()
		{
			return $this->hasOne(WorkFollowMsg::className(), ['id' => 'msg_id']);
		}
	}
