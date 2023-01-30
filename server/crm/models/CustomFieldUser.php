<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%custom_field_user}}".
	 *
	 * @property int $id
	 * @property int $uid     商户的uid
	 * @property int $fieldid 属性字段表id
	 * @property int $time    时间
	 * @property int $status  0关闭，1开启
	 * @property int $sort    排序值
	 */
	class CustomFieldUser extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%custom_field_user}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['uid', 'fieldid', 'time'], 'required'],
				[['uid', 'fieldid', 'time', 'status', 'sort'], 'integer'],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'      => Yii::t('app', 'ID'),
				'uid'     => Yii::t('app', '商户的uid'),
				'fieldid' => Yii::t('app', '属性字段表id'),
				'time'    => Yii::t('app', '时间'),
				'status'  => Yii::t('app', '0关闭，1开启'),
				'sort'    => Yii::t('app', '排序值'),
			];
		}
	}
