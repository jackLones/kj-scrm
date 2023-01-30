<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%work_handover_user}}".
	 *
	 * @property int    $id
	 * @property string $handover_userid 离职成员的userid
	 * @property string $external_userid 外部联系人userid
	 * @property string $dimission_time  成员离职时间
	 */
	class WorkHandoverUser extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_handover_user}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['handover_userid', 'external_userid'], 'string', 'max' => 64],
				[['dimission_time'], 'string', 'max' => 16],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'              => Yii::t('app', 'ID'),
				'handover_userid' => Yii::t('app', '离职成员的userid'),
				'external_userid' => Yii::t('app', '外部联系人userid'),
				'dimission_time'  => Yii::t('app', '成员离职时间'),
			];
		}
	}
