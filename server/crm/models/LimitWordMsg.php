<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\queue\SyncWordMsgJob;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%limit_word_msg}}".
	 *
	 * @property int              $id
	 * @property int              $corp_id       企业微信id
	 * @property int              $word_id       敏感词id
	 * @property int              $audit_info_id 会话内容ID
	 *
	 * @property WorkCorp         $corp
	 * @property LimitWord        $word
	 * @property WorkMsgAuditInfo $auditInfo
	 */
	class LimitWordMsg extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%limit_word_msg}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_id'], 'required'],
				[['corp_id', 'word_id', 'audit_info_id'], 'integer'],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
				[['word_id'], 'exist', 'skipOnError' => true, 'targetClass' => LimitWord::className(), 'targetAttribute' => ['word_id' => 'id']],
				[['audit_info_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAuditInfo::className(), 'targetAttribute' => ['audit_info_id' => 'id']],
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
				'word_id'       => Yii::t('app', '敏感词id'),
				'audit_info_id' => Yii::t('app', '会话内容ID'),
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
		public function getCorp ()
		{
			return $this->hasOne(WorkCorp::className(), ['id' => 'corp_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWord ()
		{
			return $this->hasOne(LimitWord::className(), ['id' => 'word_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getAuditInfo ()
		{
			return $this->hasOne(WorkMsgAuditInfo::className(), ['id' => 'audit_info_id']);
		}

		//获取监控数据
		public static function getMsg ($data, $keywordArr)
		{
			$corpId       = $data['corp_id'];
			$wordIdStr    = $data['wordIdStr'];
			$fromType     = $data['from_type'];
			$toType       = $data['to_type'];
			$toUserId     = $data['to_user_id'];
			$toExternalId = $data['to_external_id'];
			$content      = rawurldecode($data['content']);

			$fromName = $toName = $avatar = '';
			$isChat   = 0;
			switch ($fromType) {
				case 1:
					//发送方
					$workUser = WorkUser::findOne($data['user_id']);
					if (!empty($workUser)) {
						$fromName = $workUser->name;
						$avatar   = $workUser->thumb_avatar;
					}
					break;
				case 2:
					//发送方
					$contact = WorkExternalContact::findOne($data['external_id']);
					if (!empty($contact)) {
						$fromName = $contact->name;
						$avatar   = $contact->avatar;
					}
					break;
				default:

					break;
			}
			//接收方
			if (!empty($data['chat_id'])) {
				$chatInfo = WorkChat::findOne($data['chat_id']);
				if (!empty($chatInfo)) {
					$toName = $chatInfo->name;
					$isChat = 1;
				}
			} elseif (!empty($toType)) {
				switch ($toType) {
					case SUtils::IS_WORK_USER:
						$userInfo = WorkUser::findOne(['corp_id' => $corpId, 'id' => $toUserId]);
						if (!empty($userInfo)) {
							$toName = $userInfo->name;
						}
						break;
					case SUtils::IS_EXTERNAL_USER:
						$contactInfo = WorkExternalContact::findOne(['corp_id' => $corpId, 'id' => $toExternalId]);
						if (!empty($contactInfo)) {
							$toName = $contactInfo->name;
						}
						break;
					default:

						break;
				}
			}

			if (empty($fromName) || empty($toName)) {
				return [];
			}
			if (empty($avatar)) {
				$site_url = \Yii::$app->params['site_url'];
				$avatar   = $site_url . '/static/image/default-avatar.png';
			}

			$msgTime = intval($data['msgtime'] / 1000);
			//内容
			$wordIdArr = explode(',', $wordIdStr);
			if (!empty($wordIdArr)) {
				foreach ($wordIdArr as $word_id) {
					$keyword = !empty($keywordArr[$word_id]) ? $keywordArr[$word_id] : '';
					if (!empty($keyword)) {
						$content = str_replace($keyword, "<span style='color:#1890FF;cursor: pointer;'>" . $keyword . "</span>", $content);
					}
				}
			}

			return ['key' => $data['id'], 'from_name' => $fromName, 'from_type' => $fromType, 'to_name' => $toName, 'to_type' => $toType, 'avatar' => $avatar, 'is_chat' => $isChat, 'content' => $content, 'msg_time' => date('Y-m-d H:i', $msgTime)];
		}

		//添加数据
		public static function setMsg ($data)
		{
			if (empty($data['uid']) || empty($data['corp_id']) || empty($data['word_id']) || empty($data['from_type']) || empty($data['audit_info_id'])) {
				return false;
			}
			$corp_id       = $data['corp_id'];
			$word_id       = $data['word_id'];
			$uid           = $data['uid'];
			$from_type     = $data['from_type'];
			$audit_info_id = $data['audit_info_id'];
			$wordMsg       = static::findOne(['corp_id' => $corp_id, 'word_id' => $word_id, 'audit_info_id' => $audit_info_id]);
			try {
				if (empty($wordMsg)) {
					$wordMsg                = new LimitWordMsg();
					$wordMsg->corp_id       = $corp_id;
					$wordMsg->word_id       = $word_id;
					$wordMsg->audit_info_id = $audit_info_id;
					if (!$wordMsg->validate() || !$wordMsg->save()) {
						throw new InvalidDataException(SUtils::modelError($wordMsg));
					}

					//敏感词提醒统计
					$wordTimes = LimitWordTimes::findOne(['corp_id' => $corp_id, 'word_id' => $word_id]);
					if (empty($wordTimes)) {
						$wordTimes          = new LimitWordTimes();
						$wordTimes->uid     = $uid;
						$wordTimes->corp_id = $corp_id;
						$wordTimes->word_id = $word_id;
					}

					if ($from_type == 1) {
						$wordTimes->staff_times++;
					} elseif ($from_type == 2) {
						$wordTimes->custom_times++;
					}
					if (!$wordTimes->validate() || !$wordTimes->save()) {
						throw new InvalidDataException(SUtils::modelError($wordTimes));
					}
				}
			} catch (\Exception $e) {
				\Yii::error($e->getMessage(), 'getMessage');
			}
		}

		//进队列
		public static function pushJob ($wordIds, $data)
		{
			\Yii::error($data, 'pushJob');
			if (empty($wordIds) || empty($data['corp_id']) || empty($data['uid']) || empty($data['audit_id'])) {
				return false;
			}
			$limitWord = LimitWord::getList('', $wordIds);
			if (!empty($limitWord)) {
				$jobData = ['corp_id' => $data['corp_id'], 'audit_id' => $data['audit_id'], 'uid' => $data['uid']];
				if (!empty($data['chat_id'])) {
					$jobData['chat_id'] = $data['chat_id'];
				} elseif (!empty($data['user_id'])) {
					$jobData['user_id'] = $data['user_id'];
				} else {
					return false;
				}
				/**@var LimitWord $word * */
				foreach ($limitWord as $word) {
					$jobData['word_id']    = $word->id;
					$jobData['word_title'] = $word->title;
					\Yii::$app->queue->push(new SyncWordMsgJob([
						'jobData' => $jobData
					]));
				}
			}
		}
	}
