<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%work_contact_way_department}}".
	 *
	 * @property int            $id
	 * @property int            $config_id     联系方式的配置id
	 * @property int            $department_id 成员ID
	 *
	 * @property WorkContactWay $config
	 * @property WorkDepartment $department
	 */
	class WorkContactWayDepartment extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_contact_way_department}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['config_id', 'department_id'], 'integer'],
				[['config_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkContactWay::className(), 'targetAttribute' => ['config_id' => 'id']],
				[['department_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkDepartment::className(), 'targetAttribute' => ['department_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'            => Yii::t('app', 'ID'),
				'config_id'     => Yii::t('app', '联系方式的配置id'),
				'department_id' => Yii::t('app', '成员ID'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getConfig ()
		{
			return $this->hasOne(WorkContactWay::className(), ['id' => 'config_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getDepartment ()
		{
			return $this->hasOne(WorkDepartment::className(), ['id' => 'department_id']);
		}

		/**
		 * @param $configId
		 * @param $partyId
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 */
		public static function setData ($configId, $partyId)
		{
			$wayDepartment = static::findOne(['config_id' => $configId, 'department_id' => $partyId]);

			if (empty($wayDepartment)) {
				$wayDepartment = new WorkContactWayDepartment();
			}

			$wayDepartment->config_id     = $configId;
			$wayDepartment->department_id = $partyId;

			if ($wayDepartment->dirtyAttributes) {
				if (!$wayDepartment->validate() || !$wayDepartment->save()) {
					throw new InvalidDataException(SUtils::modelError($wayDepartment));
				}
			}

			return $wayDepartment->id;
		}
	}
