<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%work_msg_audit_info_collect_details}}".
	 *
	 * @property int                     $id
	 * @property int                     $collect_id 填表信息ID
	 * @property int                     $detail_id  表项id
	 * @property string                  $ques       表项名称
	 * @property string                  $type       表项类型，有Text文本,Number数字,Date日期,Time时间
	 *
	 * @property WorkMsgAuditInfoCollect $collect
	 */
	class WorkMsgAuditInfoCollectDetails extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_msg_audit_info_collect_details}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['collect_id', 'detail_id'], 'integer'],
				[['ques'], 'string', 'max' => 64],
				[['type'], 'string', 'max' => 8],
				[['collect_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAuditInfoCollect::className(), 'targetAttribute' => ['collect_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'         => Yii::t('app', 'ID'),
				'collect_id' => Yii::t('app', '填表信息ID'),
				'detail_id'  => Yii::t('app', '表项id'),
				'ques'       => Yii::t('app', '表项名称'),
				'type'       => Yii::t('app', '表项类型，有Text文本,Number数字,Date日期,Time时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getCollect ()
		{
			return $this->hasOne(WorkMsgAuditInfoCollect::className(), ['id' => 'collect_id']);
		}

		public function dumpData ()
		{
			return [
				'detail_id' => $this->detail_id,
				'ques'      => $this->ques,
				'type'      => $this->type,
			];
		}

		/**
		 * @param $collectId
		 * @param $detail
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 */
		public static function create ($collectId, $detail)
		{
			$detailInfo = self::findOne(['collect_id' => $collectId, 'detail_id' => $detail['id']]);

			if (empty($detailInfo)) {
				$detailInfo             = new self();
				$detailInfo->collect_id = $collectId;
				$detailInfo->detail_id  = $detail['id'];
			}

			$detailInfo->ques = $detail['ques'];
			$detailInfo->type = $detail['type'];

			if ($detailInfo->dirtyAttributes) {
				if (!$detailInfo->validate() || !$detailInfo->save()) {
					throw new InvalidDataException(SUtils::modelError($detailInfo));
				}
			}

			return $detailInfo->id;
		}
	}
