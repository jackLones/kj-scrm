<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%work_tag_department}}".
	 *
	 * @property int            $id
	 * @property int            $tag_id        授权的企业的标签ID
	 * @property int            $department_id 授权的企业的部门ID
	 *
	 * @property WorkDepartment $department
	 * @property WorkTag        $tag
	 */
	class WorkTagDepartment extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_tag_department}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['tag_id', 'department_id'], 'integer'],
				[['department_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkDepartment::className(), 'targetAttribute' => ['department_id' => 'id']],
				[['tag_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkTag::className(), 'targetAttribute' => ['tag_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'            => Yii::t('app', 'ID'),
				'tag_id'        => Yii::t('app', '授权的企业的标签ID'),
				'department_id' => Yii::t('app', '授权的企业的部门ID'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getDepartment ()
		{
			return $this->hasOne(WorkDepartment::className(), ['id' => 'department_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getTag ()
		{
			return $this->hasOne(WorkTag::className(), ['id' => 'tag_id']);
		}

		/**
		 * @param $tagId
		 * @param $departmentId
		 *
		 * @return int
		 */
		public static function setTagDepartment ($tagId, $departmentId)
		{
			$workTagUser = static::findOne(['tag_id' => $tagId, 'department_id' => $departmentId]);

			if (empty($workTag)) {
				$workTagUser = new WorkTagDepartment();

				$workTagUser->tag_id        = $tagId;
				$workTagUser->department_id = $departmentId;

				$workTagUser->save();
			}

			return $workTagUser->id;
		}
	}
