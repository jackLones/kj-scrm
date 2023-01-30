<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\queue\MsgAuditMediaGetJob;
	use app\util\DateUtil;
	use app\util\SUtils;
	use app\util\WorkConstUtil;
	use Yii;

	/**
	 * This is the model class for table "{{%work_msg_audit_info_file}}".
	 *
	 * @property int                              $id
	 * @property int                              $audit_info_id 会话内容ID
	 * @property string                           $sdkfileid     媒体资源的id信息
	 * @property string                           $md5sum        资源的md5值，供进行校验
	 * @property string                           $filename      文件名称
	 * @property string                           $fileext       文件类型后缀
	 * @property int                              $filesize      资源的文件大小
	 * @property string                           $local_path    系统内地址
	 * @property int                              $is_finish     是否已结束：0未结束、1已结束
	 * @property string                           $indexbuf      索引缓冲
	 *
	 * @property WorkMsgAuditInfo                 $auditInfo
	 * @property WorkMsgAuditInfoMixed[]          $workMsgAuditInfoMixeds
	 * @property WorkMsgAuditInfoChatrecordItem[] $workMsgAuditInfoChatrecordItems
	 */
	class WorkMsgAuditInfoFile extends \yii\db\ActiveRecord
	{
		const MSG_TYPE = 'file';

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_msg_audit_info_file}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['audit_info_id', 'filesize'], 'integer'],
				[['sdkfileid', 'local_path'], 'string'],
				[['md5sum'], 'string', 'max' => 32],
				[['filename'], 'string', 'max' => 64],
				[['fileext'], 'string', 'max' => 8],
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
				'filename'      => Yii::t('app', '文件名称'),
				'fileext'       => Yii::t('app', '文件类型后缀'),
				'filesize'      => Yii::t('app', '资源的文件大小'),
				'local_path'    => Yii::t('app', '系统内地址'),
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

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoMixeds ()
		{
			return $this->hasMany(WorkMsgAuditInfoMixed::className(), ['file_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoChatrecordItems ()
		{
			return $this->hasMany(WorkMsgAuditInfoChatrecordItem::className(), ['file_id' => 'id']);
		}

		public function dumpData ()
		{
			return [
				'filename'   => $this->filename,
				'fileext'    => $this->fileext,
				'filesize'   => $this->filesize,
				'local_path' => $this->local_path,
				'is_finish'  => $this->is_finish,
			];
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
			$fileInfo = self::findOne(['audit_info_id' => $auditInfo->id]);

			if (empty($fileInfo) || $needCreate) {
				$fileInfo                = new self();
				$fileInfo->audit_info_id = $auditInfo->id;
				$fileInfo->sdkfileid     = $info['sdkfileid'];
				$fileInfo->md5sum        = $info['md5sum'];
				$fileInfo->filename      = $info['filename'];
				$fileInfo->fileext       = $info['fileext'];
				$fileInfo->filesize      = $info['filesize'];

				$oldFileInfo = self::findOne(['md5sum' => $fileInfo->md5sum]);
				if (!empty($oldFileInfo)) {
					$fileInfo->local_path = $oldFileInfo->local_path;

					$fileInfo->indexbuf  = '';
					$fileInfo->is_finish = WorkConstUtil::MSG_AUDIT_IS_FINISH;
				} else {
					if (!empty($info['file'])) {
						$fileName             = !empty($info['file_name']) ? $info['file_name'] : "file_" . $auditInfo->msgid . "." . $info['fileext'];
						$msgDate              = DateUtil::getFormattedYMD(explode('_', $auditInfo->msgid)[1]);
						$filePath             = SUtils::saveMsgAuditFile($auditInfo->audit->corp->userCorpRelations[0]->uid, $fileName, $info['file'], $msgDate);
						$fileInfo->local_path = $filePath;
					}
				}

				if (!empty($info['media_data']) && empty($fileInfo->is_finish)) {
					if (!empty($info['media_data']['indexbuf'])) {
						$fileInfo->indexbuf = $info['media_data']['indexbuf'];
					}

					$fileInfo->is_finish = $info['media_data']['is_finish'];
				}

				if (!$fileInfo->validate() || !$fileInfo->save()) {
					throw new InvalidDataException(SUtils::modelError($fileInfo));
				}

				if (!empty($fileInfo->indexbuf) && $fileInfo->is_finish == WorkConstUtil::MSG_AUDIT_NOT_FINISH) {
					\Yii::$app->msgmedia->push(new MsgAuditMediaGetJob([
						'config_id'   => $auditInfo->audit_id,
						'index_buf'   => $fileInfo->indexbuf,
						'sdk_file_id' => $fileInfo->sdkfileid,
						'file_name'   => !empty($info['file_name']) ? $info['file_name'] : "file_" . $auditInfo->msgid . "." . $info['fileext'],
						'file_type'   => self::MSG_TYPE,
						'media_id'    => $fileInfo->id,
						'msg_date'    => DateUtil::getFormattedYMD(explode('_', $auditInfo->msgid)[1]),
					]));
				}
			}

			return $fileInfo->id;
		}
	}
