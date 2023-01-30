<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\DateUtil;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%template_push_info}}".
	 *
	 * @property int             $id
	 * @property int             $template_id  模板消息群发推送表ID
	 * @property int             $fans_id      粉丝ID
	 * @property string          $message_id   微信消息ID
	 * @property int             $status       发送状态：0：未发送，1：发送成功；2：发送失败；3：发送中
	 * @property int             $queue_id     发送的队列ID
	 * @property string          $errcode      错误code
	 * @property string          $errmsg       错误信息
	 * @property string          $success_time 成功时间
	 * @property string          $send_time    发送时间
	 * @property string          $create_time  创建时间
	 *
	 * @property Fans            $fans
	 * @property TemplatePushMsg $template
	 */
	class TemplatePushInfo extends \yii\db\ActiveRecord
	{
		const ADD_INFO = 1;
		const EDIT_INFO = 2;

		const UN_SEND = 0;
		const SEND_SUCCESS = 1;
		const SEND_FAILED = 2;
		const SENDING = 3;

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
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%template_push_info}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['template_id', 'fans_id', 'status', 'queue_id'], 'integer'],
				[['success_time', 'send_time', 'create_time'], 'safe'],
				[['fans_id'], 'exist', 'skipOnError' => true, 'targetClass' => Fans::className(), 'targetAttribute' => ['fans_id' => 'id']],
				[['template_id'], 'exist', 'skipOnError' => true, 'targetClass' => TemplatePushMsg::className(), 'targetAttribute' => ['template_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'           => Yii::t('app', 'ID'),
				'template_id'  => Yii::t('app', '模板消息群发推送表ID'),
				'fans_id'      => Yii::t('app', '粉丝ID'),
				'message_id'   => Yii::t('app', '微信消息ID'),
				'status'       => Yii::t('app', '发送状态：0：未发送，1：发送成功；2：发送失败；3：发送中'),
				'queue_id'     => Yii::t('app', '发送的队列ID'),
				'errcode'      => Yii::t('app', '错误code'),
				'errmsg'       => Yii::t('app', '错误信息'),
				'success_time' => Yii::t('app', '成功时间'),
				'send_time'    => Yii::t('app', '发送时间'),
				'create_time'  => Yii::t('app', '创建时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getFans ()
		{
			return $this->hasOne(Fans::className(), ['id' => 'fans_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getTemplate ()
		{
			return $this->hasOne(TemplatePushMsg::className(), ['id' => 'template_id']);
		}

		/**
		 * @param int   $templateId
		 * @param array $fansInfo
		 * @param int   $type
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 */
		public static function create ($templateId, $fansInfo, $type = self::ADD_INFO)
		{
			if (empty($templateId) || empty($fansInfo)) {
				throw new InvalidDataException('参数不正确！');
			}

			$templatePushInfo = static::findOne(['template_id' => $templateId, 'fans_id' => $fansInfo['id']]);

			if (empty($templatePushInfo)) {
				$templatePushInfo              = new TemplatePushInfo();
				$templatePushInfo->template_id = $templateId;
				$templatePushInfo->fans_id     = $fansInfo['id'];
				$templatePushInfo->create_time = DateUtil::getCurrentTime();
				$templatePushInfo->status      = self::UN_SEND;
			}

			if ($templatePushInfo->dirtyAttributes) {
				if (!$templatePushInfo->validate() || !$templatePushInfo->save()) {
					throw new InvalidDataException(SUtils::modelError($templatePushInfo));
				}
			}

			return $templatePushInfo->id;
		}
	}
