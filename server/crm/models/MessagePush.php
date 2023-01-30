<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\DateUtil;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%message_push}}".
	 *
	 * @property int                 $id
	 * @property int                 $uid         用户id
	 * @property string              $title       消息名称
	 * @property int                 $sign_id     短信签名id
	 * @property int                 $type_id     短信类型id
	 * @property int                 $template_id 短信模版id
	 * @property string              $content     发送内容
	 * @property int                 $send_type   发送对象类型：1、选择已有，2、excel导入，3、手动填写
	 * @property string              $send_data   发送对象数据
	 * @property int                 $push_type   群发时间设置：1立即发送、2指定时间
	 * @property string              $update_time 修改时间
	 * @property string              $push_time   发送时间
	 * @property int                 $status      状态：0未发送、1已发送、2发送失败、3发送中
	 * @property int                 $target_num  预计发送人数
	 * @property int                 $arrival_num 实际发送人数
	 * @property int                 $queue_id    队列id
	 * @property int                 $is_del      状态：0未删除、1已删除
	 * @property string              $create_time 发送时间
	 * @property int                 $error_code  错误码
	 * @property string              $error_msg   错误信息
	 * @property string              $smsid       短信流水号
	 *
	 * @property MessageSign         $sign
	 * @property MessageType         $type
	 * @property User                $u
	 * @property MessagePushDetail[] $messagePushDetails
	 */
	class MessagePush extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%message_push}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['uid', 'sign_id', 'type_id', 'send_type', 'status', 'target_num', 'arrival_num', 'queue_id', 'is_del'], 'integer'],
				[['send_data'], 'string'],
				[['title'], 'string', 'max' => 64],
				[['content'], 'string', 'max' => 300],
				[['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => MessageType::className(), 'targetAttribute' => ['type_id' => 'id']],
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
				'title'       => Yii::t('app', '消息名称'),
				'sign_id'     => Yii::t('app', '短信签名id'),
				'type_id'     => Yii::t('app', '短信类型id'),
				'template_id' => Yii::t('app', '短信模版id'),
				'content'     => Yii::t('app', '发送内容'),
				'send_type'   => Yii::t('app', '发送对象类型：1、选择已有，2、excel导入，3、手动填写'),
				'send_data'   => Yii::t('app', '发送对象数据'),
				'push_type'   => Yii::t('app', '群发时间设置：1立即发送、2指定时间'),
				'update_time' => Yii::t('app', '修改时间'),
				'push_time'   => Yii::t('app', '发送时间'),
				'status'      => Yii::t('app', '状态：0未发送、1已发送、2发送失败、3发送中'),
				'target_num'  => Yii::t('app', '预计发送人数'),
				'arrival_num' => Yii::t('app', '实际发送人数'),
				'queue_id'    => Yii::t('app', '队列id'),
				'is_del'      => Yii::t('app', '状态：0未删除、1已删除'),
				'create_time' => Yii::t('app', '发送时间'),
				'error_code'  => Yii::t('app', '错误码'),
				'error_msg'   => Yii::t('app', '错误信息'),
				'smsid'       => Yii::t('app', '短信流水号'),
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
		public function getType ()
		{
			return $this->hasOne(MessageType::className(), ['id' => 'type_id']);
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

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getMessagePushDetails ()
		{
			return $this->hasMany(MessagePushDetail::className(), ['message_id' => 'id']);
		}

		//设置群发短信数据
		public static function setData ($data)
		{
			$uid = intval($data['uid']);
			if (empty($uid)) {
				throw new InvalidDataException('参数不正确');
			}
			$title = trim($data['title']);
			if (empty($title)) {
				throw new InvalidDataException('请输入消息名称');
			} else {
				$pushData = static::findOne(['uid' => $uid, 'title' => $title, 'is_del' => 0]);
				if (!empty($pushData)) {
					throw new InvalidDataException('消息名称已存在，请更换');
				}
			}
			$sign_id = !empty($data['sign_id']) ? intval($data['sign_id']) : 0;
			if (empty($sign_id)) {
				throw new InvalidDataException('请选择短信签名');
			}
			if (empty($data['type_id'])) {
				throw new InvalidDataException('请选择短信类型');
			}
			$content = !empty($data['content']) ? trim($data['content']) : '';
			if (empty($content)) {
				throw new InvalidDataException('请填写要发送的内容');
			}
			$send_type = !empty($data['send_type']) ? $data['send_type'] : 0;
			if (empty($send_type)) {
				throw new InvalidDataException('请选择发送对象类型');
			}

			if ($send_type == 1) {//选择已有客户
				$customerIds = !empty($data['customerIds']) ? $data['customerIds'] : [];
				$customer    = MessageCustomer::find()->where(['id' => $customerIds])->select('phone')->all();
				$phoneArr    = array_column($customer, 'phone');
				$msg         = '请选择要发送的手机号';
			} elseif ($send_type == 2) {//excel导入
				$phoneArr = !empty($data['exportPhone']) ? $data['exportPhone'] : [];
				$msg      = '请导入要发送的手机号';
			} elseif ($send_type == 3) {//输入手机号
				$phoneTxt = !empty($data['phoneTxt']) ? $data['phoneTxt'] : '';
				$phoneTxt = trim($phoneTxt);
				if (!empty($phoneTxt)) {
					$phoneTxt = str_replace('，', ',', $phoneTxt);
					$phoneArr = explode(',', $phoneTxt);
				} else {
					$phoneArr = [];
				}
				$msg = '请输入要发送的手机号';
			}
			if (empty($phoneArr)) {
				throw new InvalidDataException($msg);
			}
			$phoneData = [];
			$is_wrong  = 0;
			foreach ($phoneArr as $phone) {
				if (preg_match("/^((13[0-9])|(14[0-9])|(15([0-9]))|(16([0-9]))|(17([0-9]))|(18[0-9])|(19[0-9]))\d{8}$/", $phone)) {
					array_push($phoneData, $phone);
				} else {
					$is_wrong = 1;
					break;
				}
			}
			//有不符合规则的手机号
			if (!empty($is_wrong)) {
				throw new InvalidDataException('要发送的手机号格式不正确，请检查');
			}

			$phoneData  = array_unique($phoneData);
			$send_data  = implode(',', $phoneData);
			$target_num = count($phoneData);

			if ($target_num < 2) {
				throw new InvalidDataException('要发送的对象必须要大于1个');
			}
			$push_type = !empty($data['push_type']) ? $data['push_type'] : 0;
			if (empty($data['push_type'])) {
				throw new InvalidDataException('请选择群发设置');
			}
			$send_time = !empty($data['push_time']) ? $data['push_time'] : '';
			$time      = time();
			if ($push_type == 2) {
				//指定时间
				if ($send_time <= $time) {
					throw new InvalidDataException("当前时间已超过发送时间，无法提交，请重新设置群发时间");
				}
				$push_time = date('Y-m-d H:i:s', $send_time);
			} else {
				$push_time = DateUtil::getCurrentTime();
			}

			//计算扣除短信数
			$signInfo    = MessageSign::findOne($sign_id);
			$contentStr  = $content . '回T退订【' . $signInfo->title . '】';
			$length      = mb_strlen($contentStr, 'utf-8');
			$user        = User::findOne($uid);
			$num         = ceil($length / 66);//营销短信按照66字/每条
			$message_num = $num * $target_num;
			if ($user->message_num < $message_num) {
				throw new InvalidDataException("当前剩余的短信数已不足，请先充值！");
			}
			//创建短信群发
			$messagePush              = new MessagePush();
			$messagePush->uid         = $uid;
			$messagePush->title       = $title;
			$messagePush->sign_id     = $sign_id;
			$messagePush->type_id     = intval($data['type_id']);
			$messagePush->template_id = intval($data['template_id']);
			$messagePush->content     = $content;
			$messagePush->send_type   = intval($data['send_type']);
			$messagePush->send_data   = $send_data;
			$messagePush->push_type   = intval($data['push_type']);
			$messagePush->push_time   = $push_time;
			$messagePush->status      = 0;
			$messagePush->target_num  = $target_num;
			$messagePush->arrival_num = 0;
			$messagePush->create_time = DateUtil::getCurrentTime();
			if (!$messagePush->validate() || !$messagePush->save()) {
				throw new InvalidDataException('创建失败.' . SUtils::modelError($messagePush));
			}
			//扣除短信数
			$user->updateCounters(['message_num' => -$message_num]);
			//导入的要进客户表，去重
			if ($send_type == 2) {
				foreach ($phoneData as $phone) {
					MessageCustomer::setCustomer(['uid' => $uid, 'phone' => $phone]);
				}
			}

			return $messagePush->id;
		}

		/**
		 * 短信发送回执
		 *
		 * @param code 状态值（2成功 其他为失败）
		 * @param task_id 流水号（对应提交时返回的smsid）
		 * @param mobilephone 手机号码
		 * @param report_time 回执时间
		 * @param msg 回执状态说明
		 */
		public static function sendReturn ($postData)
		{
			$task_id = !empty($postData['task_id']) ? $postData['task_id'] : '';
			if (empty($task_id)) {
				return '流水号不能为空';
			}
			$messagePush = static::findOne(['smsid' => $task_id]);
			if (empty($messagePush)) {
				return '无此群发任务';
			}
			$phone = !empty($postData['mobilephone']) ? $postData['mobilephone'] : '';
			if (empty($phone)) {
				return '无手机号';
			}
			$detail = MessagePushDetail::findOne(['message_id' => $messagePush->id, 'phone' => $phone]);
			if (empty($detail)) {
				return '无此群发明细';
			}
			if ($detail->status != 3 && $detail->status != 4) {
				return '只处理状态为发送中或未知的';
			}
			$code = !empty($postData['code']) ? $postData['code'] : '';
			$msg  = !empty($postData['msg']) ? $postData['msg'] : '';
			if ($code == 2) {
				$detail->status = 1;
			} else {
				$detail->status = 2;
			}

			if (!empty($detail->dirtyAttributes)) {
				//明细修改状态
				if ($detail->save()) {
					if ($code == 2) {
						//群发任务修改成功个数
						$messagePush->updateCounters(['arrival_num' => +1]);
					} else {
						//发送失败补回短信数
						$user = User::findOne($messagePush->uid);
						$user->updateCounters(['message_num' => +$detail->num]);
					}
				}
			}
		}
	}
