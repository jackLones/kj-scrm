<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%work_msg_audit_info_todo}}".
	 *
	 * @property int                     $id
	 * @property int                     $audit_info_id 会话内容ID
	 * @property string                  $title         待办的来源文本
	 * @property string                  $content       待办的具体内容
	 *
	 * @property WorkMsgAuditInfoMixed[] $workMsgAuditInfoMixeds
	 * @property WorkMsgAuditInfo        $auditInfo
	 */
	class WorkMsgAuditInfoTodo extends \yii\db\ActiveRecord
	{
		const MSG_TYPE = 'todo';

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_msg_audit_info_todo}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['audit_info_id'], 'integer'],
				[['content'], 'string'],
				[['title'], 'string', 'max' => 64],
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
				'title'         => Yii::t('app', '待办的来源文本'),
				'content'       => Yii::t('app', '待办的具体内容'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoMixeds ()
		{
			return $this->hasMany(WorkMsgAuditInfoMixed::className(), ['todo_id' => 'id']);
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
			return [
				'title'   => $this->title,
				'content' => $this->content
			];
		}

		/**
		 * @param      $infoId
		 * @param      $info
		 * @param bool $needCreate
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 */
		public static function create ($infoId, $info, $needCreate = false)
		{
			$todoInfo = self::findOne(['audit_info_id' => $infoId]);

			if (empty($todoInfo) || $needCreate) {
				$todoInfo                = new self();
				$todoInfo->audit_info_id = $infoId;
				$todoInfo->title         = $info['title'];
				$todoInfo->content       = $info['content'];

				if (!$todoInfo->validate() || !$todoInfo->save()) {
					throw new InvalidDataException(SUtils::modelError($todoInfo));
				}
			}

			return $todoInfo->id;
		}
	}
