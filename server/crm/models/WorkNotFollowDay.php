<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%work_not_follow_day}}".
	 *
	 * @property int $id
	 * @property int $uid    uid
	 * @property int $day    未跟进天数
	 * @property int $is_del 是否删除1是0否
	 * @property int $time   创建时间
	 */
	class WorkNotFollowDay extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_not_follow_day}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['uid'], 'required'],
				[['uid', 'day', 'is_del', 'time'], 'integer'],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'     => Yii::t('app', 'ID'),
				'uid'    => Yii::t('app', 'uid'),
				'day'    => Yii::t('app', '未跟进天数'),
				'is_del' => Yii::t('app', '是否删除1是0否'),
				'time'   => Yii::t('app', '创建时间'),
			];
		}

		/**
		 *
		 * @return object|\yii\db\Connection|null
		 *
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function getDb ()
		{
			return Yii::$app->get('mdb');
		}
	}
