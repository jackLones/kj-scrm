<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\queue\MsgAuditMediaGetJob;
	use app\util\DateUtil;
	use app\util\SUtils;
	use app\util\WorkConstUtil;
	use Yii;

	/**
	 * This is the model class for table "{{%work_msg_audit_info_emotion}}".
	 *
	 * @property int                              $id
	 * @property int                              $audit_info_id 会话内容ID
	 * @property int                              $type          表情类型，png或者gif.1表示gif 2表示png
	 * @property int                              $width         表情图片宽度
	 * @property int                              $height        表情图片高度
	 * @property string                           $sdkfileid     媒体资源的id信息
	 * @property string                           $md5sum        资源的md5值，供进行校验
	 * @property int                              $imagesize     资源的文件大小
	 * @property string                           $local_path    系统内地址
	 * @property int                              $is_finish     是否已结束：0未结束、1已结束
	 * @property string                           $indexbuf      索引缓冲
	 *
	 * @property WorkMsgAuditInfo                 $auditInfo
	 * @property WorkMsgAuditInfoMixed[]          $workMsgAuditInfoMixeds
	 * @property WorkMsgAuditInfoChatrecordItem[] $workMsgAuditInfoChatrecordItems
	 */
	class WorkMsgAuditInfoEmotion extends \yii\db\ActiveRecord
	{
		const MSG_TYPE = 'emotion';

		const GIF_EMOTION = 1;
		const PNG_EMOTION = 2;

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_msg_audit_info_emotion}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['audit_info_id', 'type', 'width', 'height', 'imagesize'], 'integer'],
				[['sdkfileid', 'local_path'], 'string'],
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
				'type'          => Yii::t('app', '表情类型，png或者gif.1表示gif 2表示png'),
				'width'         => Yii::t('app', '表情图片宽度'),
				'height'        => Yii::t('app', '表情图片高度'),
				'sdkfileid'     => Yii::t('app', '媒体资源的id信息'),
				'md5sum'        => Yii::t('app', '资源的md5值，供进行校验'),
				'imagesize'     => Yii::t('app', '资源的文件大小'),
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
			return $this->hasMany(WorkMsgAuditInfoMixed::className(), ['emotion_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoChatrecordItems ()
		{
			return $this->hasMany(WorkMsgAuditInfoChatrecordItem::className(), ['emotion_id' => 'id']);
		}

		public function dumpData ()
		{
			return [
				'type'       => $this->type,
				'width'      => $this->width,
				'height'     => $this->height,
				'imagesize'  => $this->imagesize,
				'local_path' => $this->local_path,
				'is_finish'  => $this->is_finish,
			];
		}

		/**
		 * @param $type
		 *
		 * @return string
		 *
		 */
		public static function getEmotionType ($type)
		{
			$data = [
				self::GIF_EMOTION => 'gif',
				self::PNG_EMOTION => 'png',
			];

			return !empty($data[$type]) ? $data[$type] : 'jpg';
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
			$emotionInfo = self::findOne(['audit_info_id' => $auditInfo->id]);

			if (empty($emotionInfo) || $needCreate) {
				$emotionInfo                = new self();
				$emotionInfo->audit_info_id = $auditInfo->id;
				$emotionInfo->type          = $info['type'];
				$emotionInfo->width         = $info['width'];
				$emotionInfo->height        = $info['height'];
				$emotionInfo->sdkfileid     = $info['sdkfileid'];
				$emotionInfo->md5sum        = $info['md5sum'];
				$emotionInfo->imagesize     = $info['imagesize'];

				$oldEmotionInfo = self::findOne(['md5sum' => $emotionInfo->md5sum]);
				if (!empty($oldEmotionInfo)) {
					$emotionInfo->local_path = $oldEmotionInfo->local_path;

					$emotionInfo->indexbuf  = '';
					$emotionInfo->is_finish = WorkConstUtil::MSG_AUDIT_IS_FINISH;
				} else {
					if (!empty($info['file'])) {
						$fileName                = !empty($info['file_name']) ? $info['file_name'] : "emotion_" . $auditInfo->msgid . "." . self::getEmotionType($emotionInfo['type']);
						$msgDate                 = DateUtil::getFormattedYMD(explode('_', $auditInfo->msgid)[1]);
						$filePath                = SUtils::saveMsgAuditFile($auditInfo->audit->corp->userCorpRelations[0]->uid, $fileName, $info['file'], $msgDate);
						$emotionInfo->local_path = $filePath;
					}
				}

				if (!empty($info['media_data']) && empty($emotionInfo->is_finish)) {
					if (!empty($info['media_data']['indexbuf'])) {
						$emotionInfo->indexbuf = $info['media_data']['indexbuf'];
					}

					$emotionInfo->is_finish = $info['media_data']['is_finish'];
				}

				if (!$emotionInfo->validate() || !$emotionInfo->save()) {
					throw new InvalidDataException(SUtils::modelError($emotionInfo));
				}

				if (!empty($emotionInfo->indexbuf) && $emotionInfo->is_finish == WorkConstUtil::MSG_AUDIT_NOT_FINISH) {
					\Yii::$app->msgmedia->push(new MsgAuditMediaGetJob([
						'config_id'   => $auditInfo->audit_id,
						'index_buf'   => $emotionInfo->indexbuf,
						'sdk_file_id' => $emotionInfo->sdkfileid,
						'file_name'   => !empty($info['file_name']) ? $info['file_name'] : "emotion_" . $auditInfo->msgid . "." . self::getEmotionType($emotionInfo['type']),
						'file_type'   => self::MSG_TYPE,
						'media_id'    => $emotionInfo->id,
						'msg_date'    => DateUtil::getFormattedYMD(explode('_', $auditInfo->msgid)[1]),
					]));
				}
			}

			return $emotionInfo->id;
		}
	}
