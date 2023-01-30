<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%awards_join_detail}}".
	 *
	 * @property int    $id
	 * @property int    $awards_join_id 当前参与者
	 * @property int    $external_id    外部联系人id
	 * @property string $create_time    参与时间
	 */
	class AwardsJoinDetail extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%awards_join_detail}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['awards_join_id', 'external_id'], 'integer'],
				[['create_time'], 'safe'],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'             => 'ID',
				'awards_join_id' => '当前参与者',
				'external_id'    => '外部联系人id',
				'create_time'    => '参与时间',
			];
		}
	}
