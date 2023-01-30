<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\queue\MsgAuditMediaGetJob;
	use app\util\DateUtil;
	use app\util\SUtils;
	use app\util\WorkConstUtil;
	use Yii;

	/**
	 * This is the model class for table "{{%work_msg_audit_info_image}}".
	 *
	 * @property int                              $id
	 * @property int                              $audit_info_id 会话内容ID
	 * @property string                           $sdkfileid     媒体资源的id信息
	 * @property string                           $md5sum        图片资源的md5值，供进行校验
	 * @property int                              $filesize      图片资源的文件大小
	 * @property string                           $file_path     系统内地址
	 * @property int                              $is_finish     是否已结束：0未结束、1已结束
	 * @property string                           $indexbuf      索引缓冲
	 *
	 * @property WorkMsgAuditInfoChatrecordItem[] $workMsgAuditInfoChatrecordItems
	 * @property WorkMsgAuditInfo                 $auditInfo
	 * @property WorkMsgAuditInfoMixed[]          $workMsgAuditInfoMixeds
	 */
	class WorkMsgAuditInfoImage extends \yii\db\ActiveRecord
	{
		const MSG_TYPE = 'image';

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_msg_audit_info_image}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['audit_info_id', 'filesize'], 'integer'],
				[['sdkfileid', 'file_path'], 'string'],
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
				'md5sum'        => Yii::t('app', '图片资源的md5值，供进行校验'),
				'filesize'      => Yii::t('app', '图片资源的文件大小'),
				'file_path'     => Yii::t('app', '系统内地址'),
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
			return $this->hasMany(WorkMsgAuditInfoMixed::className(), ['image_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoChatrecordItems ()
		{
			return $this->hasMany(WorkMsgAuditInfoChatrecordItem::className(), ['image_id' => 'id']);
		}

		public function dumpData ()
		{
			$data = [
				'id'        => $this->id,
				'filessize' => $this->filesize,
				'file_path' => $this->file_path,
				'is_finish' => $this->is_finish,
			];

			if (is_file(Yii::$app->basePath . $this->file_path)) {
				$imgInfo = getimagesize(Yii::$app->basePath . $this->file_path);
			} else {
				$imgInfo = getimagesize(Yii::$app->basePath . '/upload/problem.jpeg');
			}
			$data['width']  = $imgInfo[0];
			$data['height'] = $imgInfo[1];

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
			$imageInfo = self::findOne(['audit_info_id' => $auditInfo->id]);

			if (empty($imageInfo) || $needCreate) {
				$imageInfo                = new self();
				$imageInfo->audit_info_id = $auditInfo->id;
				$imageInfo->sdkfileid     = $info['sdkfileid'];
				$imageInfo->md5sum        = $info['md5sum'];
				$imageInfo->filesize      = $info['filesize'];

				$oldImageInfo = self::findOne(['md5sum' => $imageInfo->md5sum]);
				if (!empty($oldImageInfo)) {
					$imageInfo->file_path = $oldImageInfo->file_path;

					$imageInfo->indexbuf  = '';
					$imageInfo->is_finish = WorkConstUtil::MSG_AUDIT_IS_FINISH;
				} else {
					if (!empty($info['file'])) {
						$fileName             = !empty($info['file_name']) ? $info['file_name'] : "image_" . $auditInfo->msgid . ".jpg";
						$msgDate              = DateUtil::getFormattedYMD(explode('_', $auditInfo->msgid)[1]);
						$filePath             = SUtils::saveMsgAuditFile($auditInfo->audit->corp->userCorpRelations[0]->uid, $fileName, $info['file'], $msgDate);
						$imageInfo->file_path = $filePath;
					}
				}

				if (!empty($info['media_data']) && empty($imageInfo->is_finish)) {
					if (!empty($info['media_data']['indexbuf'])) {
						$imageInfo->indexbuf = $info['media_data']['indexbuf'];
					}

					$imageInfo->is_finish = $info['media_data']['is_finish'];
				}

				if (!$imageInfo->validate() || !$imageInfo->save()) {
					throw new InvalidDataException(SUtils::modelError($imageInfo));
				}

				if (!empty($imageInfo->indexbuf) && $imageInfo->is_finish == WorkConstUtil::MSG_AUDIT_NOT_FINISH) {
					\Yii::$app->msgmedia->push(new MsgAuditMediaGetJob([
						'config_id'   => $auditInfo->audit_id,
						'index_buf'   => $imageInfo->indexbuf,
						'sdk_file_id' => $imageInfo->sdkfileid,
						'file_name'   => !empty($info['file_name']) ? $info['file_name'] : "image_" . $auditInfo->msgid . ".jpg",
						'file_type'   => self::MSG_TYPE,
						'media_id'    => $imageInfo->id,
						'msg_date'    => DateUtil::getFormattedYMD(explode('_', $auditInfo->msgid)[1]),
					]));
				}

				//发送提醒
				if (!empty($auditInfo->chat_id)) {
					WorkChatRemindSend::creat($auditInfo, static::MSG_TYPE);
				}
			}

			return $imageInfo->id;
		}
	}
