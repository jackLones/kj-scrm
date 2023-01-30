<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%awards_share}}".
	 *
	 * @property int        $id
	 * @property int        $join_id     参与者id
	 * @property int        $num         获得的抽奖次数
	 * @property string     $create_time 参与时间
	 *
	 * @property AwardsJoin $join
	 */
	class AwardsShare extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%awards_share}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['join_id', 'num'], 'integer'],
				[['create_time'], 'safe'],
				[['join_id'], 'exist', 'skipOnError' => true, 'targetClass' => AwardsJoin::className(), 'targetAttribute' => ['join_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => 'ID',
				'join_id'     => '参与者id',
				'num'         => '获得的抽奖次数',
				'create_time' => '参与时间',
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
		public function getJoin ()
		{
			return $this->hasOne(AwardsJoin::className(), ['id' => 'join_id']);
		}
	}
