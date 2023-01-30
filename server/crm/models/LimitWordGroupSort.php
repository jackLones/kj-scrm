<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%limit_word_group_sort}}".
	 *
	 * @property int  $id
	 * @property int  $uid      账户id
	 * @property int  $group_id 分组id
	 * @property int  $sort     分组排序
	 *
	 * @property User $u
	 */
	class LimitWordGroupSort extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%limit_word_group_sort}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['uid', 'group_id', 'sort'], 'integer'],
				[['uid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['uid' => 'uid']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'       => Yii::t('app', 'ID'),
				'uid'      => Yii::t('app', '账户id'),
				'group_id' => Yii::t('app', '分组id'),
				'sort'     => Yii::t('app', '分组排序'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getU ()
		{
			return $this->hasOne(User::className(), ['uid' => 'uid']);
		}
	}
