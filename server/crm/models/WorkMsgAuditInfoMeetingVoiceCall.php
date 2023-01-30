<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\queue\MsgAuditMediaGetJob;
	use app\util\DateUtil;
	use app\util\SUtils;
	use app\util\WorkConstUtil;
	use Yii;

	/**
	 * This is the model class for table "{{%work_msg_audit_info_meeting_voice_call}}".
	 *
	 * @property int              $id
	 * @property int              $audit_info_id   会话内容ID
	 * @property string           $voiceid         音频id
	 * @property int              $endtime         音频结束时间
	 * @property string           $sdkfileid       媒体资源的id信息
	 * @property string           $demofiledata    文档分享对象数据
	 * @property string           $sharescreendata 屏幕共享数据
	 * @property string           $filename        文件名称
	 * @property int              $filesize        文件大小
	 * @property string           $file_path       系统内地址
	 * @property int              $is_finish       是否已结束：0未结束、1已结束
	 * @property string           $indexbuf        索引缓冲
	 *
	 * @property WorkMsgAuditInfo $auditInfo
	 */
	class WorkMsgAuditInfoMeetingVoiceCall extends \yii\db\ActiveRecord
	{
		const MSG_TYPE = 'meeting_voice_call';

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_msg_audit_info_meeting_voice_call}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['audit_info_id', 'endtime', 'filesize'], 'integer'],
				[['sdkfileid', 'demofiledata', 'sharescreendata', 'filename', 'file_path'], 'string'],
				[['voiceid'], 'string', 'max' => 64],
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
				'id'              => Yii::t('app', 'ID'),
				'audit_info_id'   => Yii::t('app', '会话内容ID'),
				'voiceid'         => Yii::t('app', '音频id'),
				'endtime'         => Yii::t('app', '音频结束时间'),
				'sdkfileid'       => Yii::t('app', '媒体资源的id信息'),
				'demofiledata'    => Yii::t('app', '文档分享对象数据'),
				'sharescreendata' => Yii::t('app', '屏幕共享数据'),
				'filename'        => Yii::t('app', '文件名称'),
				'filesize'        => Yii::t('app', '文件大小'),
				'file_path'       => Yii::t('app', '系统内地址'),
				'is_finish'       => Yii::t('app', '是否已结束：0未结束、1已结束'),
				'indexbuf'        => Yii::t('app', '索引缓冲'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getAuditInfo ()
		{
			return $this->hasOne(WorkMsgAuditInfo::className(), ['id' => 'audit_info_id']);
		}

		public function dumpData ($corpId = '', $otherData = [])
		{
			$data = [
				'id'        => $this->id,
				'voiceid'   => $this->voiceid,
				'endtime'   => $this->endtime,
				'filename'  => $this->filename,
				'filesize'  => $this->filesize,
				'file_path' => $this->file_path
			];
			if (!empty($corpId)) {
				//音频时间
				$data['voice_time'] = $this->endtime - intval($this->auditInfo->msgtime / 1000);
				//参与人数
				$toList             = explode(',', $this->auditInfo->tolist);
				$data['take_count'] = count($toList);
				$takeData           = [];
				foreach ($toList as $toInfo) {
					$takeName = '未知';
					$avatar   = '';
					switch (SUtils::getUserType($toInfo)) {
						case SUtils::IS_WORK_USER:
							$workUser = WorkUser::findOne(['corp_id' => $corpId, 'userid' => $toInfo]);
							if (!empty($workUser)) {
								$takeName = $workUser->name;
								$avatar   = $workUser->avatar;
							}
							break;
						case SUtils::IS_EXTERNAL_USER:
							$contact = WorkExternalContact::findOne(['corp_id' => $corpId, 'external_userid' => $toInfo]);
							if (!empty($contact)) {
								$takeName = $contact->name;
								$avatar   = $contact->avatar;
							} else {
								$avatar = SUtils::makeGravatar($toInfo);
							}
							break;
					}
					array_push($takeData, ['take_name' => $takeName, 'avatar' => $avatar]);
				}
				$data['take_data'] = $takeData;

				//文档共享数据
				$data['doc_data'] = WorkMsgAuditInfoVoipDocShare::find()->where(['voipid' => $this->voiceid])->select('filename,file_path,filesize')->all();

				//获取屏幕共享数据
				$shareData = [];
				if (!empty($this->sharescreendata) && !empty($corpId)) {
					$shareScreenData = json_decode($this->sharescreendata, 1);
					foreach ($shareScreenData as $shareScreen) {
						$shareName = '未知';
						$avatar    = '';
						switch (SUtils::getUserType($shareScreen['share'])) {
							case SUtils::IS_WORK_USER:
								$workUser = WorkUser::findOne(['corp_id' => $corpId, 'userid' => $shareScreen['share']]);
								if (!empty($workUser)) {
									$shareName = $workUser->name;
									$avatar    = $workUser->avatar;
								}
								break;
							case SUtils::IS_EXTERNAL_USER:
								$contact = WorkExternalContact::findOne(['corp_id' => $corpId, 'external_userid' => $shareScreen['share']]);
								if (!empty($contact)) {
									$shareName = $contact->name;
									$avatar    = $contact->avatar;
								} else {
									$avatar = SUtils::makeGravatar($shareScreen['share']);
								}
								break;
						}
						$shareTime = ($shareScreen['endtime'] > $shareScreen['starttime']) ? $shareScreen['endtime'] - $shareScreen['starttime'] : 0;
						$screen    = [
							'share_name' => $shareName,
							'share_time' => $shareTime,
							'avatar'     => $avatar,
						];
						array_push($shareData, $screen);
					}
				}
				$data['share_data'] = $shareData;
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
		public static function create ($auditInfo, $voiceid, $info, $needCreate = false)
		{
			$voiceInfo = self::findOne(['audit_info_id' => $auditInfo->id]);
			if (empty($voiceInfo) || $needCreate) {
				$voiceInfo                = new self();
				$voiceInfo->audit_info_id = $auditInfo->id;
				$voiceInfo->voiceid       = $voiceid;
				$voiceInfo->sdkfileid     = $info['sdkfileid'];
				$voiceInfo->endtime       = $info['endtime'];
				$demoFileData             = $shareScreenData = '';
				if (isset($info['demofiledata'])) {
					$demoFileData = json_encode($info['demofiledata'], JSON_UNESCAPED_UNICODE);
				}
				if (isset($info['sharescreendata'])) {
					$shareScreenData = json_encode($info['sharescreendata'], JSON_UNESCAPED_UNICODE);
				}
				$voiceInfo->demofiledata    = $demoFileData;
				$voiceInfo->sharescreendata = $shareScreenData;

				if (!empty($info['file'])) {
					$fileName             = !empty($info['file_name']) ? $info['file_name'] . ".mp3" : "meeting_voice_call_" . $auditInfo->msgid . ".mp3";
					$voiceInfo->filename  = $fileName;
					$msgDate              = DateUtil::getFormattedYMD(explode('_', $auditInfo->msgid)[1]);
					$filePath             = SUtils::saveMsgAuditFile($auditInfo->audit->corp->userCorpRelations[0]->uid, $fileName, $info['file'], $msgDate);
					$voiceInfo->file_path = $filePath;
					$voiceInfo->filesize  = filesize(\Yii::getAlias('@app') . $filePath);
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
						'file_name'   => !empty($info['file_name']) ? $info['file_name'] . ".mp3" : "meeting_voice_call_" . $auditInfo->msgid . ".mp3",
						'file_type'   => self::MSG_TYPE,
						'media_id'    => $voiceInfo->id,
						'msg_date'    => DateUtil::getFormattedYMD(explode('_', $auditInfo->msgid)[1]),
					]));
				}
			}

			return $voiceInfo->id;
		}
	}
