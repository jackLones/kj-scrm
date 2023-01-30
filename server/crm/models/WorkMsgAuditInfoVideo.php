<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\queue\MsgAuditMediaGetJob;
	use app\util\DateUtil;
	use app\util\SUtils;
	use app\util\WorkConstUtil;
	use Yii;

	/**
	 * This is the model class for table "{{%work_msg_audit_info_video}}".
	 *
	 * @property int                              $id
	 * @property int                              $audit_info_id 会话内容ID
	 * @property string                           $sdkfileid     媒体资源的id信息
	 * @property string                           $md5sum        资源的md5值，供进行校验
	 * @property int                              $filesize      资源的文件大小
	 * @property int                              $play_length   视频播放长度
	 * @property string                           $video_path    系统内地址
	 * @property int                              $is_finish     是否已结束：0未结束、1已结束
	 * @property string                           $indexbuf      索引缓冲
	 *
	 * @property WorkMsgAuditInfoChatrecordItem[] $workMsgAuditInfoChatrecordItems
	 * @property WorkMsgAuditInfoMixed[]          $workMsgAuditInfoMixeds
	 * @property WorkMsgAuditInfo                 $auditInfo
	 */
	class WorkMsgAuditInfoVideo extends \yii\db\ActiveRecord
	{
		const MSG_TYPE = 'video';

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_msg_audit_info_video}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['audit_info_id', 'filesize', 'play_length'], 'integer'],
				[['sdkfileid', 'video_path'], 'string'],
				[['md5sum'], 'string', 'max' => 32],
				[['indexbuf'], 'string', 'max' => 255],
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
				'audit_info_id' => Yii::t('app', '会话内容ID'),
				'sdkfileid'     => Yii::t('app', '媒体资源的id信息'),
				'md5sum'        => Yii::t('app', '资源的md5值，供进行校验'),
				'filesize'      => Yii::t('app', '资源的文件大小'),
				'play_length'   => Yii::t('app', '视频播放长度'),
				'video_path'    => Yii::t('app', '系统内地址'),
				'is_finish'     => Yii::t('app', '是否已结束：0未结束、1已结束'),
				'indexbuf'      => Yii::t('app', '索引缓冲'),
			];
		}

		public function dumpData ()
		{
			$data = [
				'id'          => $this->id,
				'video_size'  => $this->filesize,
				'play_length' => $this->play_length,
				'local_path'  => $this->video_path,
				'is_finish'   => $this->is_finish
			];

			if (is_file(Yii::$app->basePath . $this->video_path)) {
				exec("ffmpeg -i " . Yii::$app->basePath . $this->video_path . " 2>&1 | grep ': Video:' | awk -F ':' '{print $4}' | awk -F 'x' '{print $2}'", $runData);
				Yii::error(Yii::$app->basePath . $this->video_path);
				Yii::error($runData);
				if (!empty($runData)) {
					$wString = explode(' ', rtrim($runData[0]));
					$width   = $wString[count($wString) - 1];
				} else {
					$width = NULL;
				}

				$runData = [];
				exec("ffmpeg -i " . Yii::$app->basePath . $this->video_path . " 2>&1 | grep ': Video:' | awk -F ':' '{print $4}' | awk -F 'x' '{print $3}'", $runData);
				Yii::error($runData);
				if (!empty($runData)) {
					$hString = explode(' ', ltrim($runData[0]));
					$height  = trim($hString[0], ',');
				} else {
					$height = NULL;
				}
			} else {
				$videoInfo = getimagesize(Yii::$app->basePath . '/upload/problem.jpeg');
				$width     = $videoInfo[0];
				$height    = $videoInfo[1];
			}
			$data['width']  = $width;
			$data['height'] = $height;

			return $data;
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoChatrecordItems ()
		{
			return $this->hasMany(WorkMsgAuditInfoChatrecordItem::className(), ['video_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoMixeds ()
		{
			return $this->hasMany(WorkMsgAuditInfoMixed::className(), ['video_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getAuditInfo ()
		{
			return $this->hasOne(WorkMsgAuditInfo::className(), ['id' => 'audit_info_id']);
		}

		/**
		 * @param WorkMsgAuditInfo $auditInfo
		 * @param                  $info
		 * @param bool             $needCreate
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 */
		public static function create ($auditInfo, $info, $needCreate = false)
		{
			$videoInfo = self::findOne(['audit_info_id' => $auditInfo->id]);

			if (empty($videoInfo) || $needCreate) {
				$videoInfo                = new self();
				$videoInfo->audit_info_id = $auditInfo->id;
				$videoInfo->sdkfileid     = $info['sdkfileid'];
				$videoInfo->md5sum        = $info['md5sum'];
				$videoInfo->filesize      = $info['filesize'];
				$videoInfo->play_length   = $info['play_length'];

				$oldVideoInfo = self::findOne(['md5sum' => $videoInfo->md5sum]);
				if (!empty($oldVideoInfo)) {
					$videoInfo->video_path = $oldVideoInfo->video_path;

					$videoInfo->indexbuf  = '';
					$videoInfo->is_finish = WorkConstUtil::MSG_AUDIT_IS_FINISH;
				} else {
					if (!empty($info['file'])) {
						$fileName              = !empty($info['file_name']) ? $info['file_name'] : "video_" . $auditInfo->msgid . ".mp4";
						$msgDate               = DateUtil::getFormattedYMD(explode('_', $auditInfo->msgid)[1]);
						$filePath              = SUtils::saveMsgAuditFile($auditInfo->audit->corp->userCorpRelations[0]->uid, $fileName, $info['file'], $msgDate);
						$videoInfo->video_path = $filePath;
					}
				}

				if (!empty($info['media_data']) && empty($videoInfo->is_finish)) {
					if (!empty($info['media_data']['indexbuf'])) {
						$videoInfo->indexbuf = $info['media_data']['indexbuf'];
					}

					$videoInfo->is_finish = $info['media_data']['is_finish'];
				}

				if (!$videoInfo->validate() || !$videoInfo->save()) {
					throw new InvalidDataException(SUtils::modelError($videoInfo));
				}

				if (!empty($videoInfo->indexbuf) && $videoInfo->is_finish == WorkConstUtil::MSG_AUDIT_NOT_FINISH) {
					\Yii::$app->msgmedia->push(new MsgAuditMediaGetJob([
						'config_id'   => $auditInfo->audit_id,
						'index_buf'   => $videoInfo->indexbuf,
						'sdk_file_id' => $videoInfo->sdkfileid,
						'file_name'   => !empty($info['file_name']) ? $info['file_name'] : "video_" . $auditInfo->msgid . ".mp4",
						'file_type'   => self::MSG_TYPE,
						'media_id'    => $videoInfo->id,
						'msg_date'    => DateUtil::getFormattedYMD(explode('_', $auditInfo->msgid)[1]),
					]));
				}

				//发送提醒
				if (!empty($auditInfo->chat_id)) {
					WorkChatRemindSend::creat($auditInfo, static::MSG_TYPE);
				}
			}

			return $videoInfo->id;
		}
	}
