<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\queue\MsgAuditMediaGetJob;
	use app\util\DateUtil;
	use app\util\SUtils;
	use app\util\WorkConstUtil;
	use Yii;

	/**
	 * This is the model class for table "{{%work_msg_audit_info_voice}}".
	 *
	 * @property int                     $id
	 * @property int                     $audit_info_id 会话内容ID
	 * @property int                     $voice_size    语音消息大小
	 * @property int                     $play_length   播放长度
	 * @property string                  $sdkfileid     媒体资源的id信息
	 * @property string                  $md5sum        资源的md5值，供进行校验
	 * @property string                  $voice_path    系统内地址
	 * @property int                     $is_finish     是否已结束：0未结束、1已结束
	 * @property string                  $indexbuf      索引缓冲
	 *
	 * @property WorkMsgAuditInfoMixed[] $workMsgAuditInfoMixeds
	 * @property WorkMsgAuditInfo        $auditInfo
	 */
	class WorkMsgAuditInfoVoice extends \yii\db\ActiveRecord
	{
		const MSG_TYPE = 'voice';

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_msg_audit_info_voice}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['audit_info_id', 'voice_size', 'play_length'], 'integer'],
				[['sdkfileid', 'voice_path'], 'string'],
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
				'voice_size'    => Yii::t('app', '语音消息大小'),
				'play_length'   => Yii::t('app', '播放长度'),
				'sdkfileid'     => Yii::t('app', '媒体资源的id信息'),
				'md5sum'        => Yii::t('app', '资源的md5值，供进行校验'),
				'voice_path'    => Yii::t('app', '系统内地址'),
				'is_finish'     => Yii::t('app', '是否已结束：0未结束、1已结束'),
				'indexbuf'      => Yii::t('app', '索引缓冲'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoMixeds ()
		{
			return $this->hasMany(WorkMsgAuditInfoMixed::className(), ['voice_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getAuditInfo ()
		{
			return $this->hasOne(WorkMsgAuditInfo::className(), ['id' => 'audit_info_id']);
		}

		public function dumpData ()
		{
			$data = [
				'id'          => $this->id,
				'voice_size'  => $this->voice_size,
				'play_length' => $this->play_length,
				'local_path'  => $this->voice_path,
				'is_finish'   => $this->is_finish
			];

			$suffix    = 'amr';
			$voiceData = explode('.', $this->voice_path);
			if (count($voiceData) == 2) {
				$suffix = $voiceData[1];
			}

			if ($suffix == 'amr' && !is_file(Yii::$app->basePath . $this->voice_path)) {
				$data['local_path'] = '';
			} else {
				if (count($voiceData) == 2) {
					if ($suffix != 'mp3') {
						$audioFileName = $voiceData[0] . '.mp3';

						shell_exec('ffmpeg -i ' . Yii::$app->basePath . $this->voice_path . ' ' . Yii::$app->basePath . $audioFileName);

						$this->voice_path = $audioFileName;
						$this->update();

						$data['local_path'] = $this->voice_path;
					} else {
						$originalPath = $voiceData[0] . '.amr';
						if (!is_file(Yii::$app->basePath . $this->voice_path) && is_file(Yii::$app->basePath . $originalPath)) {
							shell_exec('ffmpeg -i ' . Yii::$app->basePath . $originalPath . ' ' . Yii::$app->basePath . $this->voice_path);

							$data['local_path'] = $this->voice_path;
						}
					}
				}
			}

			return $data;
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
			$voiceInfo = self::findOne(['audit_info_id' => $auditInfo->id]);

			if (empty($voiceInfo) || $needCreate) {
				$voiceInfo                = new self();
				$voiceInfo->audit_info_id = $auditInfo->id;
				$voiceInfo->voice_size    = $info['voice_size'];
				$voiceInfo->play_length   = $info['play_length'];
				$voiceInfo->sdkfileid     = $info['sdkfileid'];
				$voiceInfo->md5sum        = $info['md5sum'];

				$oldVoiceInfo = self::findOne(['md5sum' => $voiceInfo->md5sum]);
				if (!empty($oldVoiceInfo)) {
					$voiceInfo->voice_path = $oldVoiceInfo->voice_path;

					$voiceInfo->indexbuf  = '';
					$voiceInfo->is_finish = WorkConstUtil::MSG_AUDIT_IS_FINISH;
				} else {
					if (!empty($info['file'])) {
						$fileName              = !empty($info['file_name']) ? $info['file_name'] : "voice_" . $auditInfo->msgid . ".amr";
						$msgDate               = DateUtil::getFormattedYMD(explode('_', $auditInfo->msgid)[1]);
						$filePath              = SUtils::saveMsgAuditFile($auditInfo->audit->corp->userCorpRelations[0]->uid, $fileName, $info['file'], $msgDate);
						$voiceInfo->voice_path = $filePath;
					}
				}

				if (!empty($info['media_data']) && empty($voiceInfo->is_finish)) {
					if (!empty($info['media_data']['indexbuf'])) {
						$voiceInfo->indexbuf = $info['media_data']['indexbuf'];
					}

					$voiceInfo->is_finish = $info['media_data']['is_finish'];
				}

				if (!$voiceInfo->validate() || !$voiceInfo->save()) {
					throw new InvalidDataException(SUtils::modelError($voiceInfo));
				}

				if (!empty($voiceInfo->indexbuf) && $voiceInfo->is_finish == WorkConstUtil::MSG_AUDIT_NOT_FINISH) {
					\Yii::$app->msgmedia->push(new MsgAuditMediaGetJob([
						'config_id'   => $auditInfo->audit_id,
						'index_buf'   => $voiceInfo->indexbuf,
						'sdk_file_id' => $voiceInfo->sdkfileid,
						'file_name'   => !empty($info['file_name']) ? $info['file_name'] : "voice_" . $auditInfo->msgid . ".amr",
						'file_type'   => self::MSG_TYPE,
						'media_id'    => $voiceInfo->id,
						'msg_date'    => DateUtil::getFormattedYMD(explode('_', $auditInfo->msgid)[1]),
					]));
				}

				//发送提醒
				if (!empty($auditInfo->chat_id)) {
					WorkChatRemindSend::creat($auditInfo, static::MSG_TYPE);
				}
			}

			return $voiceInfo->id;
		}
	}
