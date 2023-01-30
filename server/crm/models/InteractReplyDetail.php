<?php

	namespace app\models;

	use Yii;
	use app\util\DateUtil;
	use app\util\SUtils;

	/**
	 * This is the model class for table "{{%interact_reply_detail}}".
	 *
	 * @property int           $id
	 * @property int           $author_id   公众号ID
	 * @property int           $inter_id    智能互动id
	 * @property int           $type        1 关注回复 2 消息回复
	 * @property string        $openid      用户的标识，对当前公众号唯一
	 * @property int           $status      0成功1失败2未发送
	 * @property int           $error_code  错误码
	 * @property string        $error_msg   错误信息
	 * @property string        $create_time 创建时间
	 * @property string        $msg_id      消息发送任务的ID，多个已逗号隔开
	 * @property int           $queue_id    队列id
	 * @property string        $push_time   发送时间
	 * @property int           $inter_time  记录最后一次关注时间和第一次收到消息时间
	 * @property int           $auto_id     关联pig_auto_reply表的id
	 *
	 * @property WxAuthorize   $author
	 * @property InteractReply $inter
	 */
	class InteractReplyDetail extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%interact_reply_detail}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['author_id', 'inter_id', 'type', 'status', 'error_code', 'queue_id', 'auto_id', 'inter_time'], 'integer'],
				[['create_time', 'push_time'], 'safe'],
				[['msg_id'], 'string'],
				[['openid', 'error_msg'], 'string', 'max' => 64],
				[['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => WxAuthorize::className(), 'targetAttribute' => ['author_id' => 'author_id']],
				[['inter_id'], 'exist', 'skipOnError' => true, 'targetClass' => InteractReply::className(), 'targetAttribute' => ['inter_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => Yii::t('app', 'ID'),
				'author_id'   => Yii::t('app', '公众号ID'),
				'inter_id'    => Yii::t('app', '智能互动id'),
				'type'        => Yii::t('app', '1 关注回复 2 消息回复'),
				'openid'      => Yii::t('app', '用户的标识，对当前公众号唯一'),
				'status'      => Yii::t('app', '0成功1失败2未发送'),
				'error_code'  => Yii::t('app', '错误码'),
				'error_msg'   => Yii::t('app', '错误信息'),
				'create_time' => Yii::t('app', '创建时间'),
				'msg_id'      => Yii::t('app', '消息发送任务的ID，多个已逗号隔开 '),
				'queue_id'    => Yii::t('app', '队列id'),
				'push_time'   => Yii::t('app', '发送时间'),
				'inter_time'  => Yii::t('app', '记录最后一次关注时间和第一次收到消息时间'),
				'auto_id'     => Yii::t('app', '关联pig_auto_reply表的id'),
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
		public function getAuthor ()
		{
			return $this->hasOne(WxAuthorize::className(), ['author_id' => 'author_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getInter ()
		{
			return $this->hasOne(InteractReply::className(), ['id' => 'inter_id']);
		}

		//创建
		public static function create ($data, $status = 0)
		{
			try {
				$inter_detail             = new InteractReplyDetail();
				$inter_detail->author_id  = $data['author_id'];
				$inter_detail->inter_id   = $data['inter_id'];
				$inter_detail->type       = $data['type'];
				$inter_detail->auto_id    = $data['auto_id'];
				$inter_detail->openid     = $data['openid'];
				$inter_detail->inter_time = $data['time'];
				$inter_detail->queue_id   = !empty($data['queue_id']) ? $data['queue_id'] : 0;
				$inter_detail->error_code = !empty($data['error_code']) ? $data['error_code'] : 0;
				$inter_detail->error_msg  = !empty($data['error_msg']) ? $data['error_msg'] : '';
				if ($status == 1) {
					$inter_detail->status    = 0;  //成功
					$inter_detail->push_time = DateUtil::getCurrentTime();
				} elseif ($status == 2) {
					$inter_detail->status    = 1; //失败
					$inter_detail->push_time = DateUtil::getCurrentTime();
				} elseif ($status == 3) {
					$inter_detail->status = 2; //未发送
				}
				$inter_detail->create_time = DateUtil::getCurrentTime();
				if (!$inter_detail->validate() || !$inter_detail->save()) {
					throw new InvalidDataException(SUtils::modelError($inter_detail));
				}

				return $inter_detail->id;
			} catch (InvalidDataException $e) {
				\Yii::error($e->getMessage(), 'msg');
			}


		}
	}
