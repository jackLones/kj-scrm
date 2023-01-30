<?php

	namespace app\models;

	use app\queue\WorkChatRemindSendJob;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%work_chat_remind_send}}".
	 *
	 * @property int              $id
	 * @property int              $corp_id       企业微信id
	 * @property int              $audit_info_id 会话内容ID
	 * @property int              $remind_id     群提醒ID
	 * @property int              $chat_id       群ID
	 * @property int              $from_type     发送者身份：1、企业成员；2、外部联系人；3、群机器人
	 * @property int              $user_id       成员ID
	 * @property int              $external_id   外部联系人ID
	 * @property string           $tolist        消息接收方列表
	 * @property string           $send_user_id  提醒人成员ID集合
	 * @property string           $msgtype       消息类型：文本：text； 图片：image；语音：voice；视频：video；名片：card；链接：link；小程序：weapp；红包：redpacket
	 * @property string           $content       提醒内容
	 * @property int              $time          发送时间
	 * @property int              $status        发送状态 0未发送 1已发送 2发送失败
	 * @property int              $error_code    错误码
	 * @property string           $error_msg     错误信息
	 *
	 * @property WorkMsgAuditInfo $auditInfo
	 * @property WorkCorp         $corp
	 */
	class WorkChatRemindSend extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_chat_remind_send}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_id', 'audit_info_id', 'remind_id', 'chat_id', 'from_type', 'user_id', 'external_id', 'time', 'status', 'error_code'], 'integer'],
				[['send_user_id', 'msgtype'], 'required'],
				[['msgtype'], 'string', 'max' => 32],
				[['error_msg'], 'string', 'max' => 255],
				[['content', 'send_user_id'], 'string', 'max' => 1000],
				[['tolist'], 'string'],
				[['audit_info_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAuditInfo::className(), 'targetAttribute' => ['audit_info_id' => 'id']],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'            => Yii::t('app', 'ID'),
				'corp_id'       => Yii::t('app', '企业微信id'),
				'audit_info_id' => Yii::t('app', '会话内容ID'),
				'remind_id'     => Yii::t('app', '群提醒ID'),
				'chat_id'       => Yii::t('app', '群ID'),
				'from_type'     => Yii::t('app', '发送者身份：1、企业成员；2、外部联系人；3、群机器人'),
				'user_id'       => Yii::t('app', '成员ID'),
				'external_id'   => Yii::t('app', '外部联系人ID'),
				'tolist'        => Yii::t('app', '消息接收方列表'),
				'send_user_id'  => Yii::t('app', '提醒人成员ID集合'),
				'msgtype'       => Yii::t('app', '消息类型：文本：text； 图片：image；语音：voice；视频：video；名片：card；链接：link；小程序：weapp；红包：redpacket'),
				'content'       => Yii::t('app', '提醒内容'),
				'time'          => Yii::t('app', '发送时间'),
				'status'        => Yii::t('app', '发送状态 0未发送 1已发送 2发送失败'),
				'error_code'    => Yii::t('app', '错误码'),
				'error_msg'     => Yii::t('app', '错误信息'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getAuditInfo ()
		{
			return $this->hasOne(WorkMsgAuditInfo::className(), ['id' => 'audit_info_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getCorp ()
		{
			return $this->hasOne(WorkCorp::className(), ['id' => 'corp_id']);
		}

		/**
		 * @param $auditInfo
		 * @param $msgtype
		 * @param $content
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 * @throws \app\components\InvalidParameterException]
		 */
		public static function creat ($auditInfo, $msgtype, $content = '')
		{
			$remindMsgType = [
				WorkMsgAuditInfoText::MSG_TYPE,
				WorkMsgAuditInfoImage::MSG_TYPE,
				WorkMsgAuditInfoVoice::MSG_TYPE,
				WorkMsgAuditInfoVideo::MSG_TYPE,
				WorkMsgAuditInfoCard::MSG_TYPE,
				WorkMsgAuditInfoLink::MSG_TYPE,
				WorkMsgAuditInfoWeapp::MSG_TYPE,
				WorkMsgAuditInfoRedpacket::MSG_TYPE,
			];

			if (isset($auditInfo->chat_id) && !empty($auditInfo->chat_id)) {
				$workChat = WorkChat::findOne($auditInfo->chat_id);
				if (!empty($workChat) && in_array($msgtype, $remindMsgType)) {
					$msgTypeStr     = 'is_' . $msgtype;
					$workChatRemind = WorkChatRemind::find()->andWhere(['corp_id' => $workChat->corp_id, 'status' => 1, $msgTypeStr => 1])->andWhere(['like', 'chat_ids', '"' . $workChat->id . '"'])->one();
					if (!empty($workChatRemind)) {
						$keywordStr = '';
						if ($msgtype == WorkMsgAuditInfoText::MSG_TYPE && !empty($content)) {
							$keywordD              = [];
							$keywordD['content']   = rawurldecode($content);
							$keywordD['ids']       = json_decode($workChatRemind->keyword, true);
							$keywordD['is_system'] = 1;
							$keywordRes            = LimitWord::checkWord($keywordD);

							$keywordArr = $keywordRes['titleData'];

							if (empty($keywordArr)) {
								return true;
							} else {
								$keywordStr = implode('/', $keywordArr);
							}
							//违规监控
							$idData   = !empty($keywordRes['idData']) ? $keywordRes['idData'] : [];
							$fromType = $auditInfo->from_type;
							if (in_array($fromType, [SUtils::IS_WORK_USER, SUtils::IS_EXTERNAL_USER])) {
								$userCorp = UserCorpRelation::findOne(['corp_id' => $workChat->corp_id]);
								$uid      = $userCorp->uid;
								foreach ($idData as $word_id) {
									LimitWordMsg::setMsg(['corp_id' => $workChat->corp_id, 'word_id' => $word_id, 'audit_info_id' => $auditInfo->id, 'from_type' => $fromType, 'uid' => $uid]);
								}
							}
						}

						//提醒人
						$sendUser = [];
						if (!empty($workChat->owner_id)) {
							array_push($sendUser, $workChat->owner_id);
						}
						if (!empty($workChatRemind->remind_user)) {
							$remind_user = json_decode($workChatRemind->remind_user, true);
							foreach ($remind_user as $v) {
								array_push($sendUser, $v['id']);
							}
						}
						$sendUser = array_unique($sendUser);

						$remindSend                = new WorkChatRemindSend();
						$remindSend->corp_id       = $workChat->corp_id;
						$remindSend->audit_info_id = $auditInfo->id;
						$remindSend->remind_id     = $workChatRemind->id;
						$remindSend->chat_id       = $workChat->id;
						$remindSend->from_type     = $auditInfo->from_type;
						$remindSend->user_id       = $auditInfo->user_id;
						$remindSend->external_id   = $auditInfo->external_id;
						$remindSend->tolist        = '';
						$remindSend->send_user_id  = !empty($sendUser) ? json_encode($sendUser) : '';
						$remindSend->msgtype       = $msgtype;
						$remindSend->content       = $keywordStr;
						$remindSend->time          = time();

						if ($remindSend->save()) {
							\Yii::$app->work->push(new WorkChatRemindSendJob([
								'work_chat_remind_send_id' => $remindSend->id
							]));
						}
					}
				}
			}

			return true;
		}
	}
