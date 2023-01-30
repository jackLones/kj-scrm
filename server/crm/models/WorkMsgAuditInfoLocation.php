<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%work_msg_audit_info_location}}".
	 *
	 * @property int                              $id
	 * @property int                              $audit_info_id 会话内容ID
	 * @property double                           $longitude     经度
	 * @property double                           $latitude      纬度
	 * @property string                           $address       地址信息
	 * @property string                           $title         位置信息的title
	 * @property int                              $zoom          缩放比例
	 *
	 * @property WorkMsgAuditInfoChatrecordItem[] $workMsgAuditInfoChatrecordItems
	 * @property WorkMsgAuditInfo                 $auditInfo
	 * @property WorkMsgAuditInfoMixed[]          $workMsgAuditInfoMixeds
	 */
	class WorkMsgAuditInfoLocation extends \yii\db\ActiveRecord
	{
		const MSG_TYPE = 'location';

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_msg_audit_info_location}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['audit_info_id', 'zoom'], 'integer'],
				[['longitude', 'latitude'], 'number'],
				[['address'], 'string'],
				[['title'], 'string', 'max' => 255],
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
				'longitude'     => Yii::t('app', '经度'),
				'latitude'      => Yii::t('app', '纬度'),
				'address'       => Yii::t('app', '地址信息'),
				'title'         => Yii::t('app', '位置信息的title'),
				'zoom'          => Yii::t('app', '缩放比例'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditInfoChatrecordItems ()
		{
			return $this->hasMany(WorkMsgAuditInfoChatrecordItem::className(), ['location_id' => 'id']);
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
			return $this->hasMany(WorkMsgAuditInfoMixed::className(), ['location_id' => 'id']);
		}

		public function dumpData ()
		{
			return [
				'longitude' => $this->longitude,
				'latitude'  => $this->latitude,
				'address'   => $this->address,
				'title'     => $this->title,
				'zoom'      => $this->zoom,
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
			$locationInfo = self::findOne(['audit_info_id' => $infoId]);

			if (empty($locationInfo) || $needCreate) {
				$locationInfo                = new self();
				$locationInfo->audit_info_id = $infoId;
				$locationInfo->longitude     = $info['longitude'];
				$locationInfo->latitude      = $info['latitude'];
				$locationInfo->address       = $info['address'];
				$locationInfo->title         = $info['title'];
				$locationInfo->zoom          = $info['zoom'];

				if (!$locationInfo->validate() || !$locationInfo->save()) {
					throw new InvalidDataException(SUtils::modelError($locationInfo));
				}
			}

			return $locationInfo->id;
		}
	}
