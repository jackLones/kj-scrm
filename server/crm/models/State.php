<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%state}}".
	 *
	 * @property int    $id
	 * @property string $short_prefix 短地址前缀
	 * @property string $short_url    短地址
	 * @property string $redirect_url 跳转地址
	 * @property string $create_time  创建日期
	 */
	class State extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%state}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['redirect_url'], 'string'],
				[['create_time'], 'safe'],
				[['short_prefix'], 'string', 'max' => 4],
				[['short_url'], 'string', 'max' => 16],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'           => Yii::t('app', 'ID'),
				'short_prefix' => Yii::t('app', '短地址前缀'),
				'short_url'    => Yii::t('app', '短地址'),
				'redirect_url' => Yii::t('app', '跳转地址'),
				'create_time'  => Yii::t('app', '创建日期'),
			];
		}
	}
