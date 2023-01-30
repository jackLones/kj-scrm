<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%work_msg_audit_info_chatrecord_item}}".
	 *
	 * @property int                        $id
	 * @property int                        $record_id     会话记录ID
	 * @property int                        $audit_info_id 会话内容ID
	 * @property string                     $type          每条聊天记录的具体消息类型：ChatRecordText、ChatRecordFile、ChatRecordImage、ChatRecordVideo、ChatRecordLink、ChatRecordLocation等
	 * @property int                        $from_chatroom 是否来自群聊：0、否；1、是
	 * @property int                        $sort          消息排序
	 * @property int                        $text_id       文本消息ID
	 * @property int                        $image_id      图片消息ID
	 * @property int                        $video_id      视频消息ID
	 * @property int                        $location_id   位置消息ID
	 * @property int                        $emotion_id    表情消息ID
	 * @property int                        $file_id       文件消息ID
	 * @property int                        $link_id       链接消息ID
	 * @property int                        $weapp_id      小程序消息ID
	 * @property int                        $chatrecord_id 会话记录消息ID
	 * @property int                        $meeting_id    会议消息ID
	 * @property int                        $docmsg_id     在线文档消息ID
	 * @property int                        $markdown_id   MarkDown消息ID
	 * @property int                        $news_id       图文消息ID
	 * @property string                     $mixed_id      混合消息ID
	 *
	 * @property WorkMsgAuditInfo           $auditInfo
	 * @property WorkMsgAuditInfoChatrecord $chatrecord
	 * @property WorkMsgAuditInfoDocmsg     $docmsg
	 * @property WorkMsgAuditInfoEmotion    $emotion
	 * @property WorkMsgAuditInfoFile       $file
	 * @property WorkMsgAuditInfoImage      $image
	 * @property WorkMsgAuditInfoLink       $link
	 * @property WorkMsgAuditInfoLocation   $location
	 * @property WorkMsgAuditInfoMarkdown   $markdown
	 * @property WorkMsgAuditInfoMeeting    $meeting
	 * @property WorkMsgAuditInfoNews       $news
	 * @property WorkMsgAuditInfoChatrecord $record
	 * @property WorkMsgAuditInfoText       $text
	 * @property WorkMsgAuditInfoVideo      $video
	 * @property WorkMsgAuditInfoWeapp      $weapp
	 */
	class WorkMsgAuditInfoChatrecordItem extends \yii\db\ActiveRecord
	{
		const NOT_FROM_ROOM_CHAT = 0;
		const IS_FROM_ROOM_CHAT = 1;

		const CHAT_RECORD_TEXT = "chatrecordtext";
		const CHAT_RECORD_IMAGE = "chatrecordimage";
		const CHAT_RECORD_VIDEO = "chatrecordvideo";
		const CHAT_RECORD_LOCATION = "chatrecordlocation";
		const CHAT_RECORD_EMOTION = "chatrecordemotion";
		const CHAT_RECORD_FILE = "chatrecordfile";
		const CHAT_RECORD_LINK = "chatrecordlink";
		const CHAT_RECORD_WEAPP = "chatrecordweapp";
		const CHAT_RECORD_CHAT_RECORD = "chatrecord";
		const CHAT_RECORD_MEETING = "chatrecordmeeting";
		const CHAT_RECORD_DOCMSG = "chatrecordocmsg";
		const CHAT_RECORD_MARKDOWN = "chatrecordmarkdown";
		const CHAT_RECORD_NEWS = "chatrecordnews";
		const CHAT_RECORD_MIXED = "chatrecordmixed";

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_msg_audit_info_chatrecord_item}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['record_id', 'audit_info_id', 'from_chatroom', 'sort', 'text_id', 'image_id', 'video_id', 'location_id', 'emotion_id', 'file_id', 'link_id', 'weapp_id', 'chatrecord_id', 'meeting_id', 'docmsg_id', 'markdown_id', 'news_id'], 'integer'],
				[['type'], 'string', 'max' => 64],
				[['audit_info_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAuditInfo::className(), 'targetAttribute' => ['audit_info_id' => 'id']],
				[['chatrecord_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAuditInfoChatrecord::className(), 'targetAttribute' => ['chatrecord_id' => 'id']],
				[['docmsg_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAuditInfoDocmsg::className(), 'targetAttribute' => ['docmsg_id' => 'id']],
				[['emotion_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAuditInfoEmotion::className(), 'targetAttribute' => ['emotion_id' => 'id']],
				[['file_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAuditInfoFile::className(), 'targetAttribute' => ['file_id' => 'id']],
				[['image_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAuditInfoImage::className(), 'targetAttribute' => ['image_id' => 'id']],
				[['link_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAuditInfoLink::className(), 'targetAttribute' => ['link_id' => 'id']],
				[['location_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAuditInfoLocation::className(), 'targetAttribute' => ['location_id' => 'id']],
				[['markdown_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAuditInfoMarkdown::className(), 'targetAttribute' => ['markdown_id' => 'id']],
				[['meeting_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAuditInfoMeeting::className(), 'targetAttribute' => ['meeting_id' => 'id']],
				[['mixed_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAuditInfoMixed::className(), 'targetAttribute' => ['mixed_id' => 'id']],
				[['news_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAuditInfoNews::className(), 'targetAttribute' => ['news_id' => 'id']],
				[['record_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAuditInfoChatrecord::className(), 'targetAttribute' => ['record_id' => 'id']],
				[['text_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAuditInfoText::className(), 'targetAttribute' => ['text_id' => 'id']],
				[['video_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAuditInfoVideo::className(), 'targetAttribute' => ['video_id' => 'id']],
				[['weapp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAuditInfoWeapp::className(), 'targetAttribute' => ['weapp_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'            => Yii::t('app', 'ID'),
				'record_id'     => Yii::t('app', '会话记录ID'),
				'audit_info_id' => Yii::t('app', '会话内容ID'),
				'type'          => Yii::t('app', '每条聊天记录的具体消息类型：ChatRecordText、ChatRecordFile、ChatRecordImage、ChatRecordVideo、ChatRecordLink、ChatRecordLocation等'),
				'from_chatroom' => Yii::t('app', '是否来自群聊：0、否；1、是'),
				'sort'          => Yii::t('app', '消息排序'),
				'text_id'       => Yii::t('app', '文本消息ID'),
				'image_id'      => Yii::t('app', '图片消息ID'),
				'video_id'      => Yii::t('app', '视频消息ID'),
				'location_id'   => Yii::t('app', '位置消息ID'),
				'emotion_id'    => Yii::t('app', '表情消息ID'),
				'file_id'       => Yii::t('app', '文件消息ID'),
				'link_id'       => Yii::t('app', '链接消息ID'),
				'weapp_id'      => Yii::t('app', '小程序消息ID'),
				'chatrecord_id' => Yii::t('app', '会话记录消息ID'),
				'meeting_id'    => Yii::t('app', '会议消息ID'),
				'docmsg_id'     => Yii::t('app', '在线文档消息ID'),
				'markdown_id'   => Yii::t('app', 'MarkDown消息ID'),
				'news_id'       => Yii::t('app', '图文消息ID'),
				'mixed_id'      => Yii::t('app', '混合消息ID'),
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
		public function getChatrecord ()
		{
			return $this->hasOne(WorkMsgAuditInfoChatrecord::className(), ['id' => 'chatrecord_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getDocmsg ()
		{
			return $this->hasOne(WorkMsgAuditInfoDocmsg::className(), ['id' => 'docmsg_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getEmotion ()
		{
			return $this->hasOne(WorkMsgAuditInfoEmotion::className(), ['id' => 'emotion_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getFile ()
		{
			return $this->hasOne(WorkMsgAuditInfoFile::className(), ['id' => 'file_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getImage ()
		{
			return $this->hasOne(WorkMsgAuditInfoImage::className(), ['id' => 'image_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getLink ()
		{
			return $this->hasOne(WorkMsgAuditInfoLink::className(), ['id' => 'link_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getLocation ()
		{
			return $this->hasOne(WorkMsgAuditInfoLocation::className(), ['id' => 'location_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getMarkdown ()
		{
			return $this->hasOne(WorkMsgAuditInfoMarkdown::className(), ['id' => 'markdown_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getMeeting ()
		{
			return $this->hasOne(WorkMsgAuditInfoMeeting::className(), ['id' => 'meeting_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getNews ()
		{
			return $this->hasOne(WorkMsgAuditInfoNews::className(), ['id' => 'news_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getRecord ()
		{
			return $this->hasOne(WorkMsgAuditInfoChatrecord::className(), ['id' => 'record_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getText ()
		{
			return $this->hasOne(WorkMsgAuditInfoText::className(), ['id' => 'text_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getVideo ()
		{
			return $this->hasOne(WorkMsgAuditInfoVideo::className(), ['id' => 'video_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWeapp ()
		{
			return $this->hasOne(WorkMsgAuditInfoWeapp::className(), ['id' => 'weapp_id']);
		}

		public function dumpData ()
		{
			$data = [
				'type'          => $this->type,
				'from_chatroom' => $this->from_chatroom,
				'sort'          => $this->sort,
				'info'          => '',
			];

			switch (strtolower($this->type)) {
				case self::CHAT_RECORD_TEXT:
					$data['info'] = $this->text->dumpData();
					$data['type'] = 'text';//为了适应前端
					break;
				case self::CHAT_RECORD_IMAGE:
					$data['info'] = $this->image->dumpData();
					$data['type'] = 'image';
					break;
				case self::CHAT_RECORD_VIDEO:
					$data['info'] = $this->video->dumpData();
					$data['type'] = 'video';
					break;
				case self::CHAT_RECORD_LOCATION:
					$data['info'] = $this->location->dumpData();
					$data['type'] = 'location';
					break;
				case self::CHAT_RECORD_EMOTION:
					$data['info'] = $this->emotion->dumpData();
					$data['type'] = 'emotion';
					break;
				case self::CHAT_RECORD_FILE:
					$data['info'] = $this->file->dumpData();
					$data['type'] = 'file';
					break;
				case self::CHAT_RECORD_LINK:
					$data['info'] = $this->link->dumpData();
					$data['type'] = 'link';
					break;
				case self::CHAT_RECORD_WEAPP:
					$data['info'] = $this->weapp->dumpData();
					$data['type'] = 'weapp';
					break;
				case self::CHAT_RECORD_CHAT_RECORD:
					$data['info'] = $this->chatrecord->dumpData();
					$data['type'] = 'chatrecord';
					break;
				case self::CHAT_RECORD_MEETING:
					$data['info'] = $this->meeting->dumpData();
					$data['type'] = 'meeting';
					break;
				case self::CHAT_RECORD_DOCMSG:
					$data['info'] = $this->docmsg->dumpData();
					$data['type'] = 'docmsg';
					break;
				case self::CHAT_RECORD_MARKDOWN:
					$data['info'] = $this->markdown->dumpData();
					$data['type'] = 'markdown';
					break;
				case self::CHAT_RECORD_NEWS:
					$data['info'] = $this->news->dumpData();
					$data['type'] = 'news';
					break;
				case self::CHAT_RECORD_MIXED:
					$data['type'] = 'mixed';
					if (!empty($this->mixed_id)) {
						$mixIds    = explode(',', $this->mixed_id);
						$mixedList = WorkMsgAuditInfoMixed::find()->where(['id' => $mixIds])->all();
						if (!empty($mixedList)) {
							$info = [];
							/**@var WorkMsgAuditInfoMixed $mixedInfo * */
							foreach ($mixedList as $mixedInfo) {
								$tempMixed = $mixedInfo->dumpData();
								array_push($info, $tempMixed);
							}
							$data['info'] = $info;
						}
					}

					break;
				default:
					break;
			}

			return $data;
		}

		/**
		 * @param                  $corpId
		 * @param                  $recordId
		 * @param WorkMsgAuditInfo $auditInfo
		 * @param                  $info
		 *
		 * @return array
		 *
		 * @throws \Throwable
		 */
		public static function create ($corpId, $recordId, $auditInfo, $info)
		{
			$result = [
				'count'        => count($info['item']),
				'success'      => 0,
				'failed'       => 0,
				'success_info' => [],
				'failed_info'  => [],
			];
			foreach ($info['item'] as $key => $item) {
				try {
					$chatRecordInfo = self::findOne(['record_id' => $recordId, 'audit_info_id' => $auditInfo->id, 'type' => $item['type'], 'sort' => $key]);

					if (empty($chatRecordInfo)) {
						$chatRecordInfo                = new self();
						$chatRecordInfo->record_id     = $recordId;
						$chatRecordInfo->audit_info_id = $auditInfo->id;
						$chatRecordInfo->type          = $item['type'];
						$chatRecordInfo->from_chatroom = $item['from_chatroom'] ? self::IS_FROM_ROOM_CHAT : self::NOT_FROM_ROOM_CHAT;
						$chatRecordInfo->sort          = $key;

						switch (strtolower($item['type'])) {
							case self::CHAT_RECORD_TEXT:
								$textId                  = WorkMsgAuditInfoText::create($auditInfo->id, $item['content'], true);
								$chatRecordInfo->text_id = $textId;

								break;
							case self::CHAT_RECORD_IMAGE:
								$imageId                  = WorkMsgAuditInfoImage::create($auditInfo, $item['content'], true);
								$chatRecordInfo->image_id = $imageId;

								break;
							case self::CHAT_RECORD_VIDEO:
								$videoId                  = WorkMsgAuditInfoVideo::create($auditInfo, $item['content'], true);
								$chatRecordInfo->video_id = $videoId;

								break;
							case self::CHAT_RECORD_LOCATION:
								$locationId                  = WorkMsgAuditInfoLocation::create($auditInfo->id, $item['content'], true);
								$chatRecordInfo->location_id = $locationId;

								break;
							case self::CHAT_RECORD_EMOTION:
								$emotionId                  = WorkMsgAuditInfoEmotion::create($auditInfo, $item['content'], true);
								$chatRecordInfo->emotion_id = $emotionId;

								break;
							case self::CHAT_RECORD_FILE:
								$fileId                  = WorkMsgAuditInfoFile::create($auditInfo, $item['content'], true);
								$chatRecordInfo->file_id = $fileId;

								break;
							case self::CHAT_RECORD_LINK:
								$linkId                  = WorkMsgAuditInfoLink::create($auditInfo->id, $item['content'], true);
								$chatRecordInfo->link_id = $linkId;

								break;
							case self::CHAT_RECORD_WEAPP:
								$weappId                  = WorkMsgAuditInfoWeapp::create($auditInfo->id, $item['content'], true);
								$chatRecordInfo->weapp_id = $weappId;

								break;
							case self::CHAT_RECORD_CHAT_RECORD:
								$chatRecordId                  = WorkMsgAuditInfoChatrecord::create($corpId, $auditInfo, $item['content'], true);
								$chatRecordInfo->chatrecord_id = $chatRecordId;

								break;
							case self::CHAT_RECORD_MEETING:
								$meetingId                  = WorkMsgAuditInfoMeeting::create($auditInfo->id, $item['content'], true);
								$chatRecordInfo->meeting_id = $meetingId;

								break;
							case self::CHAT_RECORD_DOCMSG:
								$docmsgId                  = WorkMsgAuditInfoDocmsg::create($corpId, $auditInfo->id, $item['content'], true);
								$chatRecordInfo->docmsg_id = $docmsgId;

								break;
							case self::CHAT_RECORD_MARKDOWN:
								$markdownId                  = WorkMsgAuditInfoMarkdown::create($auditInfo->id, $item['content'], true);
								$chatRecordInfo->markdown_id = $markdownId;

								break;
							case self::CHAT_RECORD_NEWS:
								$newsId                  = WorkMsgAuditInfoNews::create($auditInfo->id, $item['content'], true);
								$chatRecordInfo->news_id = $newsId;

								break;
							case self::CHAT_RECORD_MIXED:
								$mixedId                  = WorkMsgAuditInfoMixed::create($corpId, $auditInfo, $item['content'], true);
								$chatRecordInfo->mixed_id = $mixedId;

								break;
							default:
								break;
						}

						if (!$chatRecordInfo->validate() || !$chatRecordInfo->save()) {
							throw new InvalidDataException(SUtils::modelError($chatRecordInfo));
						}
					}

					$result['success']++;
					array_push($result['success_info'], $key);
				} catch (\Exception $e) {
					$result['failed']++;
					array_push($result['failed_info'], $key);
					Yii::error($e->getMessage(), __CLASS__ . "-" . __FUNCTION__);
				}
			}

			return $result;
		}
	}
