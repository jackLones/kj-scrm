<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\DateUtil;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%message_template}}".
	 *
	 * @property int         $id
	 * @property int         $uid         用户id
	 * @property int         $sign_id     签名id
	 * @property int         $type_id     短信类型表id
	 * @property string      $content     模版内容
	 * @property int         $status      状态，-1：删除、0：待审核、1：已审核、2：审核失败
	 * @property string      $update_time 修改时间
	 * @property string      $create_time 创建时间
	 * @property string      $error_msg   失败原因
	 * @property string      $apply_time  申请时间
	 *
	 * @property MessageSign $sign
	 * @property User        $u
	 */
	class MessageTemplate extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%message_template}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['type_id', 'status'], 'integer'],
				[['content'], 'string'],
				[['update_time', 'create_time'], 'safe'],
				[['sign_id'], 'exist', 'skipOnError' => true, 'targetClass' => MessageSign::className(), 'targetAttribute' => ['sign_id' => 'id']],
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
				'uid'         => Yii::t('app', '用户id'),
				'sign_id'     => Yii::t('app', '签名id'),
				'type_id'     => Yii::t('app', '短信类型表id'),
				'content'     => Yii::t('app', '模版内容'),
				'status'      => Yii::t('app', '状态，-1：删除、0：待审核、1：已审核、2：审核失败'),
				'update_time' => Yii::t('app', '修改时间'),
				'create_time' => Yii::t('app', '创建时间'),
				'error_msg'   => Yii::t('app', '失败原因'),
				'apply_time'  => Yii::t('app', '申请时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getSign ()
		{
			return $this->hasOne(MessageSign::className(), ['id' => 'sign_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getU ()
		{
			return $this->hasOne(User::className(), ['uid' => 'uid']);
		}

		//设置模版
		public static function setTemplate ($data)
		{
			$id      = !empty($data['id']) ? $data['id'] : 0;
			$content = trim($data['content']);
			$type_id = intval($data['type_id']);
			if (empty($type_id)) {
				throw new InvalidDataException('请选择短信类型');
			}
			if (empty($content)) {
				throw new InvalidDataException('请填写内容');
			} elseif (mb_strlen($content, 'utf-8') > 250) {
				throw new InvalidDataException('模版内容最多不能超过250个字符');
			}
			if (!empty($id)) {
				$template              = static::findOne($id);
				$template->update_time = DateUtil::getCurrentTime();
			} else {
				$template              = new MessageTemplate();
				$template->create_time = DateUtil::getCurrentTime();
			}
			$template->type_id = $type_id;
			$template->content = $content;
			$template->status  = 1;
			if (!$template->save()) {
				throw new InvalidDataException(SUtils::modelError($template));
			}

			return ['error' => 0, 'msg' => ''];
		}
	}
