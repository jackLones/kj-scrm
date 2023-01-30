<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\components\InvalidParameterException;
	use app\util\DateUtil;
	use app\util\MsgUtil;
	use app\util\SUtils;
	use Yii;
	use yii\helpers\Json;

	/**
	 * This is the model class for table "{{%fans_msg}}".
	 *
	 * @property int             $id
	 * @property int             $fans_id        粉丝ID
	 * @property int             $kf_id          客服ID
	 * @property int             $from           发送方，1：粉丝、2：用户、3：客服
	 * @property int             $to             接收方，1：粉丝、2：用户、3：客服
	 * @property string          $content        消息内容
	 * @property int             $isread         是否已读，0：未读、1：已读
	 * @property int             $content_type   消息类型，1：文本（text）、2：图片（img）、3：语音（voice）、4：视频（video）、5：图文（news）
	 * @property int             $material_id    素材ID
	 * @property int             ￥attachment_id  附件ID
	 * @property string          $create_time    创建时间
	 *
	 * @property Material        $material
	 * @property Attachment      $attachment
	 * @property Fans            $fans
	 * @property KfUser          $kf
	 * @property FansMsgMaterial $fansMsgMaterial
	 */
	class FansMsg extends \yii\db\ActiveRecord
	{
		const FROM_FANS = 1;
		const FROM_USER = 2;
		const FROM_KF = 3;

		const TO_FANS = 1;
		const TO_USER = 2;
		const TO_KF = 3;

		const TEXT_MSG = 1;
		const IMG_MSG = 2;
		const VOICE_MSG = 3;
		const VIDEO_MSG = 4;
		const NEWS_MSG = 5;
		const MUSIC_MSG = 6;
		const SHORT_VIDEO_MSG = 7;
		const LOCATION_MSG = 8;
		const LINK_MSG = 9;
		const MINI_MSG = 10;//小程序

		const MSG_UN_READ = 0;
		const MSG_IS_READ = 1;

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%fans_msg}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['fans_id', 'kf_id', 'content_type'], 'integer'],
				[['content'], 'string'],
				[['create_time'], 'safe'],
				[['fans_id'], 'exist', 'skipOnError' => true, 'targetClass' => Fans::className(), 'targetAttribute' => ['fans_id' => 'id']],
				[['kf_id'], 'exist', 'skipOnError' => true, 'targetClass' => KfUser::className(), 'targetAttribute' => ['kf_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'            => Yii::t('app', 'ID'),
				'fans_id'       => Yii::t('app', '粉丝ID'),
				'kf_id'         => Yii::t('app', '客服ID'),
				'from'          => Yii::t('app', '发送方，1：粉丝、2：用户、3：客服'),
				'to'            => Yii::t('app', '接收方，1：粉丝、2：用户、3：客服'),
				'content'       => Yii::t('app', '消息内容'),
				'isread'        => Yii::t('app', '是否已读，0：未读、1：已读'),
				'content_type'  => Yii::t('app', '消息类型，1：文本（text）、2：图片（img）、3：语音（voice）、4：视频（video）、5：图文（news）、6：音乐（music）、7：小视频（short video）、8：位置信息（location）、9：链接（link）'),
				'material_id'   => Yii::t('app', '素材ID'),
				'attachment_id' => Yii::t('app', '附件ID'),
				'create_time'   => Yii::t('app', '创建时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getMaterial ()
		{
			return $this->hasOne(Material::className(), ['id' => 'material_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getAttachment ()
		{
			return $this->hasOne(Attachment::className(), ['id' => 'attachment_id']);
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
		public function getKf ()
		{
			return $this->hasOne(KfUser::className(), ['id' => 'kf_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getFansMsgMaterial ()
		{
			return $this->hasOne(FansMsgMaterial::className(), ['msg_id' => 'id']);
		}

		/**
		 * {@inheritDoc}
		 * @return bool
		 */
		public function beforeSave ($insert)
		{
			$this->content = rawurlencode($this->content);

			return parent::beforeSave($insert); // TODO: Change the autogenerated stub
		}

		/**
		 * {@inheritDoc}
		 */
		public function afterFind ()
		{
			if (!empty($this->content)) {
				$this->content = rawurldecode($this->content);
			}

			parent::afterFind();
		}

		public function dumpData ()
		{
			$from = [
				'type' => $this->from,
				'data' => [],
			];
			$to   = [
				'type' => $this->to,
				'data' => [],
			];

			switch ($this->from) {
				case self::FROM_FANS:
					$from['data'] = $this->fans->dumpMinData();

					break;
				case self::FROM_USER:
					$from['data'] = $this->fans->author->wxAuthorizeInfo->dumpMinData();

					break;
				case self::FROM_KF:
					$from['data'] = $this->kf->dumpData();

					break;
				default:
					break;
			}

			switch ($this->to) {
				case self::FROM_FANS:
					$to['data'] = $this->fans->dumpMinData();

					break;
				case self::FROM_USER:
					$to['data'] = $this->fans->author->wxAuthorizeInfo->dumpMinData();

					break;
				case self::FROM_KF:
					$to['data'] = $this->kf->dumpData();

					break;
				default:
					break;
			}

			switch ($this->content_type) {
				case self::IMG_MSG:
				case self::VOICE_MSG:
				case self::VIDEO_MSG:
				case self::MUSIC_MSG:
				case self::SHORT_VIDEO_MSG:
					if ($this->from == self::FROM_FANS && !empty($this->fansMsgMaterial)) {
						$this->content = $this->fansMsgMaterial->dumpMiniData();
					}

					if ($this->from != self::FROM_FANS && !empty($this->material)) {
						$this->content = $this->material->dumpMiniData();
					}

					break;
				case self::LOCATION_MSG:
				case self::LINK_MSG:
					$this->content = Json::decode($this->content);

					break;
				case self::NEWS_MSG:
					if (!empty($this->material)) {
						$this->content = $this->material->dumpArticleData();
					} elseif (!empty($this->attachment)) {
						$this->content = $this->attachment->dumpArticleData();
					} else {
						$this->content = '';
					}

					break;
				default:
					break;
			}

			$result = [
				'id'          => $this->id,
				'is_read'     => $this->isread,
				'from'        => $from,
				'to'          => $to,
				'content'     => $this->content,
				'type'        => $this->content_type,
				'create_time' => $this->create_time,
			];

			return $result;
		}

		/**
		 * 创建消息
		 *
		 * @param      $fansId
		 * @param      $msgType
		 * @param      $content
		 * @param int  $from
		 * @param int  $to
		 * @param null $kfId
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 * @throws InvalidParameterException
		 * @throws \app\components\NotAllowException
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function create ($fansId, $msgType, $content, $from = self::FROM_FANS, $to = self::TO_USER, $kfId = NULL)
		{
			$fansMsgMaterialType = 0;

			$fansMsg          = new FansMsg();
			$fansMsg->fans_id = $fansId;

			if (!empty($kfId)) {
				$fansMsg->kf_id = $kfId;
			}

			$fansMsg->from = $from;
			$fansMsg->to   = $to;

			if (!empty($content['attachment_id'])) {
				$fansMsg->attachment_id = $content['attachment_id'];
			}

			switch ($msgType) {
				case static::TEXT_MSG:
					$fansMsg->content = $content;

					break;
				case static::IMG_MSG:
					if ($fansMsg->from != static::FROM_FANS) {
						$fansMsg->material_id = $content['media_id'];
					}

					$fansMsg->content = Json::encode($content, JSON_UNESCAPED_UNICODE);

					$fansMsgMaterialType = FansMsgMaterial::IMAGE_TYPE;

					break;
				case static::VOICE_MSG:
					if ($fansMsg->from != static::FROM_FANS) {
						$fansMsg->material_id = $content['media_id'];
					}

					$fansMsg->content = Json::encode($content, JSON_UNESCAPED_UNICODE);

					$fansMsgMaterialType = FansMsgMaterial::VOICE_TYPE;

					break;
				case static::VIDEO_MSG:
					if ($fansMsg->from != static::FROM_FANS) {
						$fansMsg->material_id = $content['media_id'];
					}

					$fansMsg->content = Json::encode($content, JSON_UNESCAPED_UNICODE);

					$fansMsgMaterialType = FansMsgMaterial::VIDEO_TYPE;

					break;
				case static::SHORT_VIDEO_MSG:
					if ($fansMsg->from != static::FROM_FANS) {
						$fansMsg->material_id = $content['media_id'];
					}

					$fansMsg->content = Json::encode($content, JSON_UNESCAPED_UNICODE);

					$fansMsgMaterialType = FansMsgMaterial::SHORT_VIDEO_TYPE;

					break;
				case static::LOCATION_MSG:
				case static::LINK_MSG:
					$fansMsg->content = Json::encode($content, JSON_UNESCAPED_UNICODE);

					break;
				case static::NEWS_MSG:
					$fansMsg->material_id = $content['media_id'];
					$fansMsg->content     = Json::encode($content, JSON_UNESCAPED_UNICODE);

					break;
				case static::MINI_MSG:
					$fansMsg->material_id = $content['media_id'];
					//$fansMsg->content = Json::encode($content, JSON_UNESCAPED_UNICODE);
					$fansMsg->content = Json::encode($content['title'], JSON_UNESCAPED_UNICODE);
					break;
				default:
					break;
			}

			if ($fansMsg->from != static::FROM_FANS) {
				$fansMsg->isread = static::MSG_IS_READ;
			} else {
				$fansMsg->isread = static::MSG_UN_READ;
			}

			$fansMsg->content_type = $msgType;
			$fansMsg->create_time  = DateUtil::getCurrentTime();

			if ($fansMsg->validate() && $fansMsg->save()) {
				$ignoreData = [static::TEXT_MSG, static::LOCATION_MSG, static::LINK_MSG, static::NEWS_MSG];

				if ($fansMsg->from == static::FROM_FANS && !in_array($msgType, $ignoreData)) {
					FansMsgMaterial::create($fansMsg->fans->author_id, $fansMsg->fans_id, $fansMsg->id, $content, $fansMsgMaterialType);
				}

				return $fansMsg->id;
			} else {
				throw new InvalidDataException(SUtils::modelError($fansMsg));
			}
		}

		/**
		 * 获取粉丝消息列表
		 *
		 * @param      $fansId
		 * @param int  $lastId
		 * @param int  $contentSize
		 * @param bool $onlyContent
		 *
		 * @return array
		 *
		 */
		public static function getMsgList ($fansId, $lastId = 0, $contentSize = 10, $onlyContent = false)
		{
			$msgList = [];

			$msgData = static::find()->where(['fans_id' => $fansId]);

			if ($lastId != 0) {
				$msgData = $msgData->andWhere(['<', 'id', $lastId]);
			}

			$unreadMsgData = $msgData;

			$msgCount    = $msgData->count();
			$msgData     = $msgData->limit($contentSize)->orderBy(['create_time' => SORT_DESC, 'id' => SORT_DESC])->all();
			$unReadCount = $unreadMsgData->andWhere(['isread' => static::MSG_UN_READ])->count();
			if (!empty($msgData)) {
				/** @var FansMsg $msgInfo */
				foreach ($msgData as $msgInfo) {
					$msgData = $msgInfo->dumpData();
					if ($onlyContent) {
						$msgData['unread'] = $unReadCount;
					}
					array_push($msgList, $msgData);
				}
			}

			if ($onlyContent) {
				$result = $msgList;
			} else {
				$result = [
					'unread'   => $unReadCount,
					'fans_id'  => $fansId,
					'total'    => $msgCount,
					'count'    => count($msgList),
					'msg_list' => $msgList,
				];
			}

			return $result;
		}

		/**
		 * 发送客服消息
		 *
		 * @param string $appid      公众号appid
		 * @param int    $fansId     粉丝ID
		 * @param int    $msgType    消息类型
		 * @param array  $msgContent 消息内容
		 * @param int    $from       来源
		 *
		 * @return array|bool|int
		 *
		 * @throws InvalidDataException
		 * @throws InvalidParameterException
		 * @throws \Throwable
		 * @throws \app\components\ForbiddenException
		 * @throws \app\components\NotAllowException
		 * @throws \yii\base\InvalidConfigException
		 * @throws \yii\db\StaleObjectException
		 */
		public static function send ($appid, $fansId, $msgType, $msgContent, $from = self::FROM_USER)
		{
			$fansInfo = Fans::findOne(['id' => $fansId]);
			if (!empty($fansInfo)) {
				if ($fansInfo->subscribe == Fans::USER_SUBSCRIBE) {
					$result     = MsgUtil::send($appid, $fansInfo->openid, $msgType, $msgContent);
					$sendStatus = isset($result['errmsg']) && $result['errmsg'] == 'ok';
					if ($sendStatus) {
						if ($msgType == static::TEXT_MSG) {
							$content = $msgContent['text'];
						} else {
							$content = $msgContent;
						}
						$fansMsgId = static::create($fansId, $msgType, $content, $from, static::TO_FANS);

						return $fansMsgId;
					}

					return $result;
				} else {
					return [
						'error_code' => 4003,
						'errmsg'     => '该粉丝已经取消关注'
					];
				}
			}

			return false;
		}

		/**
		 * 根据消息ID，将消息设置为已读状态
		 *
		 * @param int|string|array $msgId
		 *
		 * @return int
		 *
		 * @throws InvalidParameterException
		 */
		public static function readMsgByMsgId ($msgId)
		{
			if (empty($msgId)) {
				throw new InvalidParameterException('缺少必要参数！');
			}

			if (!is_array($msgId) && strpos($msgId, ',') !== false) {
				$msgId = explode(',', $msgId);
			}

			return static::updateAll(['isread' => static::MSG_IS_READ], ['id' => $msgId, 'isread' => static::MSG_UN_READ]);
		}

		/**
		 * 根据粉丝ID，将消息设置为已读状态
		 *
		 * @param int|string $fansId
		 *
		 * @return int
		 *
		 * @throws InvalidParameterException
		 */
		public static function readMsgByFansId ($fansId)
		{
			if (empty($fansId)) {
				throw new InvalidParameterException('缺少必要参数！');
			}

			$unReadCount = static::find()->where(['fans_id' => $fansId, 'isread' => static::MSG_UN_READ])->count();
			if ($unReadCount > 0) {
				return static::updateAll(['isread' => static::MSG_IS_READ], ['fans_id' => $fansId, 'isread' => static::MSG_UN_READ]);
			}

			return true;
		}
	}
