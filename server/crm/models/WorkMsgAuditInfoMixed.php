<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%work_msg_audit_info_mixed}}".
	 *
	 * @property int                              $id
	 * @property int                              $audit_info_id 会话内容ID
	 * @property string                           $type          消息类型：文本：text； 图片：image；语音：voice；视频：video；位置：location；表情：emotion；文件：file；链接：link；小程序：weapp；会话记录：chatrecord；待办：todo；投票：vote；填表：collect；红包：redpacket；会议邀请：meeting；在线文档：docmsg；MarkDown：markdown；图文：news；日程：calendar
	 * @property int                              $sort          消息排序
	 * @property int                              $text_id       文本消息ID
	 * @property int                              $image_id      图片消息ID
	 * @property int                              $voice_id      语音消息ID
	 * @property int                              $video_id      视频消息ID
	 * @property int                              $location_id   位置消息ID
	 * @property int                              $emotion_id    表情消息ID
	 * @property int                              $file_id       文件消息ID
	 * @property int                              $link_id       链接消息ID
	 * @property int                              $weapp_id      小程序消息ID
	 * @property int                              $chatrecord_id 会话记录消息ID
	 * @property int                              $todo_id       待办消息ID
	 * @property int                              $vote_id       投票消息ID
	 * @property int                              $collect_id    填表消息ID
	 * @property int                              $meeting_id    会议消息ID
	 * @property int                              $docmsg_id     在线文档消息ID
	 * @property int                              $markdown_id   MarkDown消息ID
	 * @property int                              $news_id       图文消息ID
	 * @property int                              $calendar_id   日程消息ID
	 *
	 * @property WorkMsgAuditInfoChatrecordItem[] $workMsgAuditInfoChatrecordItems
	 * @property WorkMsgAuditInfoCalendar         $calendar
	 * @property WorkMsgAuditInfo                 $auditInfo
	 * @property WorkMsgAuditInfoChatrecord       $chatrecord
	 * @property WorkMsgAuditInfoCollect          $collect
	 * @property WorkMsgAuditInfoDocmsg           $docmsg
	 * @property WorkMsgAuditInfoEmotion          $emotion
	 * @property WorkMsgAuditInfoFile             $file
	 * @property WorkMsgAuditInfoImage            $image
	 * @property WorkMsgAuditInfoLink             $link
	 * @property WorkMsgAuditInfoLocation         $location
	 * @property WorkMsgAuditInfoMarkdown         $markdown
	 * @property WorkMsgAuditInfoMeeting          $meeting
	 * @property WorkMsgAuditInfoNews             $news
	 * @property WorkMsgAuditInfoText             $text
	 * @property WorkMsgAuditInfoTodo             $todo
	 * @property WorkMsgAuditInfoVideo            $video
	 * @property WorkMsgAuditInfoVoice            $voice
	 * @property WorkMsgAuditInfoVote             $vote
	 * @property WorkMsgAuditInfoWeapp            $weapp
	 */
	class WorkMsgAuditInfoMixed extends \yii\db\ActiveRecord
	{
		const MSG_TYPE = 'mixed';

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_msg_audit_info_mixed}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['audit_info_id', 'sort', 'text_id', 'image_id', 'voice_id', 'video_id', 'location_id', 'emotion_id', 'file_id', 'link_id', 'weapp_id', 'chatrecord_id', 'todo_id', 'vote_id', 'collect_id', 'meeting_id', 'docmsg_id', 'markdown_id', 'news_id', 'calendar_id'], 'integer'],
				[['type'], 'string', 'max' => 16],
				[['calendar_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAuditInfoCalendar::className(), 'targetAttribute' => ['calendar_id' => 'id']],
				[['audit_info_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAuditInfo::className(), 'targetAttribute' => ['audit_info_id' => 'id']],
				[['chatrecord_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAuditInfoChatrecord::className(), 'targetAttribute' => ['chatrecord_id' => 'id']],
				[['collect_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAuditInfoCollect::className(), 'targetAttribute' => ['collect_id' => 'id']],
				[['docmsg_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAuditInfoDocmsg::className(), 'targetAttribute' => ['docmsg_id' => 'id']],
				[['emotion_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAuditInfoEmotion::className(), 'targetAttribute' => ['emotion_id' => 'id']],
				[['file_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAuditInfoFile::className(), 'targetAttribute' => ['file_id' => 'id']],
				[['image_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAuditInfoImage::className(), 'targetAttribute' => ['image_id' => 'id']],
				[['link_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAuditInfoLink::className(), 'targetAttribute' => ['link_id' => 'id']],
				[['location_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAuditInfoLocation::className(), 'targetAttribute' => ['location_id' => 'id']],
				[['markdown_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAuditInfoMarkdown::className(), 'targetAttribute' => ['markdown_id' => 'id']],
				[['meeting_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAuditInfoMeeting::className(), 'targetAttribute' => ['meeting_id' => 'id']],
				[['news_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAuditInfoNews::className(), 'targetAttribute' => ['news_id' => 'id']],
				[['text_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAuditInfoText::className(), 'targetAttribute' => ['text_id' => 'id']],
				[['todo_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAuditInfoTodo::className(), 'targetAttribute' => ['todo_id' => 'id']],
				[['video_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAuditInfoVideo::className(), 'targetAttribute' => ['video_id' => 'id']],
				[['voice_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAuditInfoVoice::className(), 'targetAttribute' => ['voice_id' => 'id']],
				[['vote_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAuditInfoVote::className(), 'targetAttribute' => ['vote_id' => 'id']],
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
				'audit_info_id' => Yii::t('app', '会话内容ID'),
				'type'          => Yii::t('app', '消息类型：文本：text； 图片：image；语音：voice；视频：video；位置：location；表情：emotion；文件：file；链接：link；小程序：weapp；会话记录：chatrecord；待办：todo；投票：vote；填表：collect；红包：redpacket；会议邀请：meeting；在线文档：docmsg；MarkDown：markdown；图文：news；日程：calendar'),
				'sort'          => Yii::t('app', '消息排序'),
				'text_id'       => Yii::t('app', '文本消息ID'),
				'image_id'      => Yii::t('app', '图片消息ID'),
				'voice_id'      => Yii::t('app', '语音消息ID'),
				'video_id'      => Yii::t('app', '视频消息ID'),
				'location_id'   => Yii::t('app', '位置消息ID'),
				'emotion_id'    => Yii::t('app', '表情消息ID'),
				'file_id'       => Yii::t('app', '文件消息ID'),
				'link_id'       => Yii::t('app', '链接消息ID'),
				'weapp_id'      => Yii::t('app', '小程序消息ID'),
				'chatrecord_id' => Yii::t('app', '会话记录消息ID'),
				'todo_id'       => Yii::t('app', '待办消息ID'),
				'vote_id'       => Yii::t('app', '投票消息ID'),
				'collect_id'    => Yii::t('app', '填表消息ID'),
				'meeting_id'    => Yii::t('app', '会议消息ID'),
				'docmsg_id'     => Yii::t('app', '在线文档消息ID'),
				'markdown_id'   => Yii::t('app', 'MarkDown消息ID'),
				'news_id'       => Yii::t('app', '图文消息ID'),
				'calendar_id'   => Yii::t('app', '日程消息ID'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoChatrecordItems ()
		{
			return $this->hasMany(WorkMsgAuditInfoChatrecordItem::className(), ['mixed_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getCalendar ()
		{
			return $this->hasOne(WorkMsgAuditInfoCalendar::className(), ['id' => 'calendar_id']);
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
		public function getCollect ()
		{
			return $this->hasOne(WorkMsgAuditInfoCollect::className(), ['id' => 'collect_id']);
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
		public function getText ()
		{
			return $this->hasOne(WorkMsgAuditInfoText::className(), ['id' => 'text_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getTodo ()
		{
			return $this->hasOne(WorkMsgAuditInfoTodo::className(), ['id' => 'todo_id']);
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
		public function getVoice ()
		{
			return $this->hasOne(WorkMsgAuditInfoVoice::className(), ['id' => 'voice_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getVote ()
		{
			return $this->hasOne(WorkMsgAuditInfoVote::className(), ['id' => 'vote_id']);
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
				'type'    => $this->type,
				'sort'    => $this->sort,
				'content' => '',
			];

			switch ($this->type) {
				case WorkMsgAuditInfoText::MSG_TYPE:
					$data['content'] = $this->text->dumpData();

					break;
				case WorkMsgAuditInfoImage::MSG_TYPE:
					$data['content'] = $this->image->dumpData();

					break;
				case WorkMsgAuditInfoVoice::MSG_TYPE:
					$data['content'] = $this->voice->dumpData();

					break;
				case WorkMsgAuditInfoVideo::MSG_TYPE:
					$data['content'] = $this->video->dumpData();

					break;
				case WorkMsgAuditInfoLocation::MSG_TYPE:
					$data['content'] = $this->location->dumpData();

					break;
				case WorkMsgAuditInfoEmotion::MSG_TYPE:
					$data['content'] = $this->emotion->dumpData();

					break;
				case WorkMsgAuditInfoFile::MSG_TYPE:
					$data['content'] = $this->file->dumpData();

					break;
				case WorkMsgAuditInfoLink::MSG_TYPE:
					$data['content'] = $this->link->dumpData();

					break;
				case WorkMsgAuditInfoWeapp::MSG_TYPE:
					$data['content'] = $this->weapp->dumpData();

					break;
				case WorkMsgAuditInfoChatrecord::MSG_TYPE:
					$data['content'] = $this->chatrecord->dumpData();

					break;
				case WorkMsgAuditInfoTodo::MSG_TYPE:
					$data['content'] = $this->todo->dumpData();

					break;
				case WorkMsgAuditInfoVote::MSG_TYPE:
					$data['content'] = $this->vote->dumpData();

					break;
				case WorkMsgAuditInfoCollect::MSG_TYPE:
					$data['content'] = $this->collect->dumpData();

					break;
				case WorkMsgAuditInfoMeeting::MSG_TYPE:
					$data['content'] = $this->meeting->dumpData();

					break;
				case WorkMsgAuditInfoDocmsg::MSG_TYPE:
					$data['content'] = $this->docmsg->dumpData();

					break;
				case WorkMsgAuditInfoMarkdown::MSG_TYPE:
					$data['content'] = $this->markdown->dumpData();

					break;
				case WorkMsgAuditInfoNews::MSG_TYPE:
					$data['content'] = $this->news->dumpData();

					break;
				case WorkMsgAuditInfoCalendar::MSG_TYPE:
					$data['content'] = $this->calendar->dumpData();

					break;
				default:
					break;
			}

			return $data;
		}

		/**
		 * @param                  $corpId
		 * @param WorkMsgAuditInfo $auditInfo
		 * @param                  $info
		 * @param bool             $needCreate
		 *
		 * @return array
		 *
		 * @throws \Throwable
		 */
		public static function create ($corpId, $auditInfo, $info, $needCreate = false)
		{
			$result = [
				'count'        => count($info['item']),
				'success'      => 0,
				'failed'       => 0,
				'success_info' => [],
				'failed_info'  => [],
			];
			$mixIds = [];
			foreach ($info['item'] as $key => $item) {
				try {
					$mixedInfo = self::findOne(['audit_info_id' => $auditInfo->id, 'type' => $item['type'], 'sort' => $key]);

					if (empty($mixedInfo) || $needCreate) {
						$mixedInfo                = new self();
						$mixedInfo->audit_info_id = $auditInfo->id;
						$mixedInfo->type          = $item['type'];
						$mixedInfo->sort          = $key;

						switch ($item['type']) {
							case WorkMsgAuditInfoText::MSG_TYPE:
								$textId             = WorkMsgAuditInfoText::create($auditInfo->id, $item['content'], true);
								$mixedInfo->text_id = $textId;

								break;
							case WorkMsgAuditInfoImage::MSG_TYPE:
								$imageId             = WorkMsgAuditInfoImage::create($auditInfo, $item['content'], true);
								$mixedInfo->image_id = $imageId;

								break;
							case WorkMsgAuditInfoVoice::MSG_TYPE:
								$voiceId             = WorkMsgAuditInfoVoice::create($auditInfo, $item['content'], true);
								$mixedInfo->voice_id = $voiceId;

								break;
							case WorkMsgAuditInfoVideo::MSG_TYPE:
								$videoId             = WorkMsgAuditInfoVideo::create($auditInfo, $item['content'], true);
								$mixedInfo->video_id = $videoId;

								break;
							case WorkMsgAuditInfoLocation::MSG_TYPE:
								$locationId             = WorkMsgAuditInfoLocation::create($auditInfo->id, $item['content'], true);
								$mixedInfo->location_id = $locationId;

								break;
							case WorkMsgAuditInfoEmotion::MSG_TYPE:
								$emotionId             = WorkMsgAuditInfoEmotion::create($auditInfo, $item['content'], true);
								$mixedInfo->emotion_id = $emotionId;

								break;
							case WorkMsgAuditInfoFile::MSG_TYPE:
								$fileId             = WorkMsgAuditInfoFile::create($auditInfo, $item['content'], true);
								$mixedInfo->file_id = $fileId;

								break;
							case WorkMsgAuditInfoLink::MSG_TYPE:
								$linkId             = WorkMsgAuditInfoLink::create($auditInfo->id, $item['content'], true);
								$mixedInfo->link_id = $linkId;

								break;
							case WorkMsgAuditInfoWeapp::MSG_TYPE:
								$weappId             = WorkMsgAuditInfoWeapp::create($auditInfo->id, $item['content'], true);
								$mixedInfo->weapp_id = $weappId;

								break;
							case WorkMsgAuditInfoChatrecord::MSG_TYPE:
								$chatRecordId             = WorkMsgAuditInfoChatrecord::create($corpId, $auditInfo, $item['content'], true);
								$mixedInfo->chatrecord_id = $chatRecordId;

								break;
							case WorkMsgAuditInfoTodo::MSG_TYPE:
								$todoId             = WorkMsgAuditInfoTodo::create($auditInfo->id, $item['content'], true);
								$mixedInfo->todo_id = $todoId;

								break;
							case WorkMsgAuditInfoVote::MSG_TYPE:
								$voteId             = WorkMsgAuditInfoVote::create($auditInfo->id, $item['content'], true);
								$mixedInfo->vote_id = $voteId;

								break;
							case WorkMsgAuditInfoCollect::MSG_TYPE:
								$collectId             = WorkMsgAuditInfoCollect::create($auditInfo->id, $item['content'], true);
								$mixedInfo->collect_id = $collectId;

								break;
							case WorkMsgAuditInfoMeeting::MSG_TYPE:
								$meetingId             = WorkMsgAuditInfoMeeting::create($auditInfo->id, $item['content'], true);
								$mixedInfo->meeting_id = $meetingId;

								break;
							case WorkMsgAuditInfoDocmsg::MSG_TYPE:
								$docmsgId             = WorkMsgAuditInfoDocmsg::create($corpId, $auditInfo->id, $item['content'], true);
								$mixedInfo->docmsg_id = $docmsgId;

								break;
							case WorkMsgAuditInfoMarkdown::MSG_TYPE:
								$markdownId             = WorkMsgAuditInfoMarkdown::create($auditInfo->id, $item['content'], true);
								$mixedInfo->markdown_id = $markdownId;

								break;
							case WorkMsgAuditInfoNews::MSG_TYPE:
								$newsId             = WorkMsgAuditInfoNews::create($auditInfo->id, $item['content'], true);
								$mixedInfo->news_id = $newsId;

								break;
							case WorkMsgAuditInfoCalendar::MSG_TYPE:
								$calendarId             = WorkMsgAuditInfoCalendar::create($corpId, $auditInfo->id, $item['content'], true);
								$mixedInfo->calendar_id = $calendarId;

								break;
							default:
								break;
						}

						if (!$mixedInfo->validate() || !$mixedInfo->save()) {
							throw new InvalidDataException(SUtils::modelError($mixedInfo));
						}
						array_push($mixIds, $mixedInfo->id);
					}

					$result['success']++;
					array_push($result['success_info'], $key);
				} catch (\Exception $e) {
					$result['failed']++;
					array_push($result['failed_info'], $key);
					Yii::error($e->getMessage(), __CLASS__ . "-" . __FUNCTION__);
				}
			}

			return implode(',', $mixIds);
		}
	}
