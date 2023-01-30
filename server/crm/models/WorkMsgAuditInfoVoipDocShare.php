<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\queue\MsgAuditMediaGetJob;
	use app\util\DateUtil;
	use app\util\SUtils;
	use app\util\WorkConstUtil;
	use Yii;

	/**
	 * This is the model class for table "{{%work_msg_audit_info_voip_doc_share}}".
	 *
	 * @property int              $id
	 * @property int              $audit_info_id 会话内容ID
	 * @property string           $voipid        音频id
	 * @property string           $filename      文件名称
	 * @property string           $md5sum        资源的md5值，供进行校验
	 * @property int              $filesize      语音消息大小
	 * @property string           $file_path     系统内地址
	 * @property string           $sdkfileid     媒体资源的id信息
	 * @property int              $is_finish     是否已结束：0未结束、1已结束
	 * @property string           $indexbuf      索引缓冲
	 *
	 * @property WorkMsgAuditInfo $auditInfo
	 */
	class WorkMsgAuditInfoVoipDocShare extends \yii\db\ActiveRecord
	{
		const MSG_TYPE = 'voip_doc_share';

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_msg_audit_info_voip_doc_share}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['audit_info_id', 'filesize'], 'integer'],
				[['filename', 'file_path', 'sdkfileid'], 'string'],
				[['voipid'], 'string', 'max' => 64],
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
				'voipid'        => Yii::t('app', '音频id'),
				'filename'      => Yii::t('app', '文件名称'),
				'md5sum'        => Yii::t('app', '资源的md5值，供进行校验'),
				'filesize'      => Yii::t('app', '语音消息大小'),
				'file_path'     => Yii::t('app', '系统内地址'),
				'sdkfileid'     => Yii::t('app', '媒体资源的id信息'),
				'is_finish'     => Yii::t('app', '是否已结束：0未结束、1已结束'),
				'indexbuf'      => Yii::t('app', '索引缓冲'),
			];
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
				'id'        => $this->id,
				'voipid'    => $this->voipid,
				'filename'  => $this->filename,
				'filesize'  => $this->filesize,
				'file_path' => $this->file_path
			];

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
		public static function create ($auditInfo, $voipid, $info, $needCreate = false)
		{
			$shareInfo = self::findOne(['audit_info_id' => $auditInfo->id]);
			if (empty($shareInfo) || $needCreate) {
				$shareInfo                = new self();
				$shareInfo->audit_info_id = $auditInfo->id;
				$shareInfo->voipid        = $voipid;
				$shareInfo->filename      = $info['filename'];
				$shareInfo->filesize      = $info['filesize'];
				$shareInfo->sdkfileid     = $info['sdkfileid'];
				$shareInfo->md5sum        = $info['md5sum'];

				$oldShareInfo = self::findOne(['md5sum' => $shareInfo->md5sum]);
				if (!empty($oldShareInfo)) {
					$shareInfo->file_path = $oldShareInfo->file_path;

					$shareInfo->indexbuf  = '';
					$shareInfo->is_finish = WorkConstUtil::MSG_AUDIT_IS_FINISH;
				} else {
					if (!empty($info['file'])) {
						$fileName             = !empty($info['file_name']) ? $info['file_name'] : "voip_doc_share_" . $auditInfo->msgid . ".txt";
						$msgDate              = DateUtil::getFormattedYMD(explode('_', $auditInfo->msgid)[1]);
						$filePath             = SUtils::saveMsgAuditFile($auditInfo->audit->corp->userCorpRelations[0]->uid, $fileName, $info['file'], $msgDate);
						$shareInfo->file_path = $filePath;
					}
				}

				if (!empty($info['media_data']) && empty($shareInfo->is_finish)) {
					if (!empty($info['media_data']['indexbuf'])) {
						$shareInfo->indexbuf = $info['media_data']['indexbuf'];
					}

					$shareInfo->is_finish = $info['media_data']['is_finish'];
				}

				if (!$shareInfo->validate() || !$shareInfo->save()) {
					throw new InvalidDataException(SUtils::modelError($shareInfo));
				}

				if (!empty($shareInfo->indexbuf) && $shareInfo->is_finish == WorkConstUtil::MSG_AUDIT_NOT_FINISH) {
					\Yii::$app->msgmedia->push(new MsgAuditMediaGetJob([
						'config_id'   => $auditInfo->audit_id,
						'index_buf'   => $shareInfo->indexbuf,
						'sdk_file_id' => $shareInfo->sdkfileid,
						'file_name'   => !empty($info['file_name']) ? $info['file_name'] : "voip_doc_share_" . $auditInfo->msgid . ".txt",
						'file_type'   => self::MSG_TYPE,
						'media_id'    => $shareInfo->id,
						'msg_date'    => DateUtil::getFormattedYMD(explode('_', $auditInfo->msgid)[1]),
					]));
				}
			}

			return $shareInfo->id;
		}
	}
