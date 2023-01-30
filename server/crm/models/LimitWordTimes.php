<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%limit_word_times}}".
	 *
	 * @property int       $id
	 * @property int       $uid          账户id
	 * @property int       $word_id      敏感词id
	 * @property int       $corp_id      企业ID
	 * @property int       $staff_times  员工触发次数
	 * @property int       $custom_times 客户触发次数
	 *
	 * @property WorkCorp  $corp
	 * @property User      $u
	 * @property LimitWord $word
	 */
	class LimitWordTimes extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%limit_word_times}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['uid', 'word_id', 'corp_id', 'staff_times', 'custom_times'], 'integer'],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
				[['uid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['uid' => 'uid']],
				[['word_id'], 'exist', 'skipOnError' => true, 'targetClass' => LimitWord::className(), 'targetAttribute' => ['word_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'           => Yii::t('app', 'ID'),
				'uid'          => Yii::t('app', '账户id'),
				'word_id'      => Yii::t('app', '敏感词id'),
				'corp_id'      => Yii::t('app', '企业ID'),
				'staff_times'  => Yii::t('app', '员工触发次数'),
				'custom_times' => Yii::t('app', '客户触发次数'),
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

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getCorp ()
		{
			return $this->hasOne(WorkCorp::className(), ['id' => 'corp_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getU ()
		{
			return $this->hasOne(User::className(), ['uid' => 'uid']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWord ()
		{
			return $this->hasOne(LimitWord::className(), ['id' => 'word_id']);
		}
	}
