<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%custom_field_option}}".
	 *
	 * @property int    $id
	 * @property int    $uid     商户id
	 * @property int    $fieldid 高级属性字段表id
	 * @property int    $value   对应的值
	 * @property string $match   对应值的选项信息
	 * @property int    $is_del  是否删除
	 */
	class CustomFieldOption extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%custom_field_option}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['uid', 'fieldid', 'value', 'is_del'], 'integer'],
				[['fieldid'], 'required'],
				[['match'], 'string', 'max' => 255],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'      => Yii::t('app', 'ID'),
				'uid'     => Yii::t('app', '商户id'),
				'fieldid' => Yii::t('app', '高级属性字段表id'),
				'value'   => Yii::t('app', '对应的值'),
				'match'   => Yii::t('app', '对应值的选项信息'),
				'is_del'  => Yii::t('app', '是否删除'),
			];
		}
	}
