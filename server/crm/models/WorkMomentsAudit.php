<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%work_moments_audit}}".
	 *
	 * @property int               $id
	 * @property int               $audit_people 审核人id
	 * @property int               $type         1主账户，2子账户，3员工
	 * @property string            $reply        回复
	 * @property string            $create_time  创建时间
	 * @property int               $base_id      base_id
	 *
	 * @property WorkMomentsBase   $base
	 * @property WorkMomentsBase[] $workMomentsBases
	 */
	class WorkMomentsAudit extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_moments_audit}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['audit_people', 'type', 'base_id'], 'integer'],
				[['reply'], 'string'],
				[['create_time'], 'safe'],
				[['base_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMomentsBase::className(), 'targetAttribute' => ['base_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'           => Yii::t('app', 'ID'),
				'audit_people' => Yii::t('app', '审核人id'),
				'type'         => Yii::t('app', '1主账户，2子账户，3员工'),
				'reply'        => Yii::t('app', '回复'),
				'create_time'  => Yii::t('app', '创建时间'),
				'base_id'      => Yii::t('app', 'base_id'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getBase ()
		{
			return $this->hasOne(WorkMomentsBase::className(), ['id' => 'base_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMomentsBases ()
		{
			return $this->hasMany(WorkMomentsBase::className(), ['audit_id' => 'id']);
		}

		/**
		 * @param $data
		 */
		public static function setData ($data)
		{
			if (empty($data["audit_people"]) || empty($data["base_id"]) || empty($data["type"])) {
				throw  new InvalidDataException("数据不完整");
			}
			$audit               = new self();
			$audit->audit_people = $data["audit_people"];
			$audit->type         = $data["type"];
			$audit->base_id      = $data["base_id"];
			$audit->reply        = $data["reply"];
			if (!$audit->validate() || !$audit->save()) {
				throw  new InvalidDataException(SUtils::modelError($audit));
			}
			return $audit;
		}

	}
