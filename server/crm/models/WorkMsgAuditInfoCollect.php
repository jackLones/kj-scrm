<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%work_msg_audit_info_collect}}".
	 *
	 * @property int                              $id
	 * @property int                              $audit_info_id 会话内容ID
	 * @property string                           $room_name     填表消息所在的群名称
	 * @property string                           $creator       创建者在群中的名字
	 * @property string                           $create_time   创建的时间
	 * @property string                           $title         表名
	 *
	 * @property WorkMsgAuditInfo                 $auditInfo
	 * @property WorkMsgAuditInfoCollectDetails[] $workMsgAuditInfoCollectDetails
	 * @property WorkMsgAuditInfoMixed[]          $workMsgAuditInfoMixeds
	 */
	class WorkMsgAuditInfoCollect extends \yii\db\ActiveRecord
	{
		const MSG_TYPE = 'collect';

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_msg_audit_info_collect}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['audit_info_id'], 'integer'],
				[['room_name', 'creator', 'title'], 'string', 'max' => 64],
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
				'room_name'     => Yii::t('app', '填表消息所在的群名称'),
				'creator'       => Yii::t('app', '创建者在群中的名字'),
				'create_time'   => Yii::t('app', '创建的时间'),
				'title'         => Yii::t('app', '表名'),
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
		public function getWorkMsgAuditInfoCollectDetails ()
		{
			return $this->hasMany(WorkMsgAuditInfoCollectDetails::className(), ['collect_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoMixeds ()
		{
			return $this->hasMany(WorkMsgAuditInfoMixed::className(), ['collect_id' => 'id']);
		}

		public function dumpData ()
		{
			$details = [];
			foreach ($this->workMsgAuditInfoCollectDetails as $workMsgAuditInfoCollectDetail) {
				array_push($details, $workMsgAuditInfoCollectDetail->dumpData());
			}

			return [
				'room_name'   => $this->room_name,
				'creator'     => $this->creator,
				'create_time' => $this->create_time,
				'title'       => $this->title,
				'details'     => $details,
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
			$collectInfo = self::findOne(['audit_info_id' => $infoId]);

			if (empty($collectInfo) || $needCreate) {
				$collectInfo                = new self();
				$collectInfo->audit_info_id = $infoId;
				$collectInfo->room_name     = $info['room_name'];
				$collectInfo->creator       = $info['creator'];
				$collectInfo->create_time   = $info['create_time'];
				$collectInfo->title         = $info['title'];

				if (!$collectInfo->validate() || !$collectInfo->save()) {
					throw new InvalidDataException(SUtils::modelError($collectInfo));
				}

				foreach ($info['details'] as $detail) {
					try {
						WorkMsgAuditInfoCollectDetails::create($collectInfo->id, $detail);
					} catch (\Exception $e) {
						Yii::error($e->getMessage(), __CLASS__ . "-" . __FUNCTION__ . 'setDetail');
					}
				}
			}

			return $collectInfo->id;
		}
	}
