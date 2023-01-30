<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%work_public_activity_url}}".
	 *
	 * @property int    $id
	 * @property string $short_url 短连接
	 * @property string $url       原始连接
	 * @property int    $create_time
	 */
	class WorkPublicActivityUrl extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_public_activity_url}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['short_url', 'url', 'create_time'], 'required'],
				[['create_time'], 'integer'],
				[['short_url'], 'string', 'max' => 60],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => Yii::t('app', 'ID'),
				'short_url'   => Yii::t('app', '短连接'),
				'url'         => Yii::t('app', '原始连接'),
				'create_time' => Yii::t('app', 'Create Time'),
			];
		}
	}
