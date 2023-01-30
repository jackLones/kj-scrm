<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%location}}".
	 *
	 * @property int    $id
	 * @property int    $fans_id     粉丝ID
	 * @property string $lng         经度
	 * @property string $lat         纬度
	 * @property string $create_time 创建时间
	 *
	 * @property Fans   $fans
	 */
	class Location extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%location}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['fans_id', 'lng', 'lat'], 'required'],
				[['fans_id'], 'integer'],
				[['lng', 'lat'], 'number'],
				[['create_time'], 'safe'],
				[['fans_id'], 'exist', 'skipOnError' => true, 'targetClass' => Fans::className(), 'targetAttribute' => ['fans_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => Yii::t('app', 'ID'),
				'fans_id'     => Yii::t('app', '粉丝ID'),
				'lng'         => Yii::t('app', '经度'),
				'lat'         => Yii::t('app', '纬度'),
				'create_time' => Yii::t('app', '创建时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getFans ()
		{
			return $this->hasOne(Fans::className(), ['id' => 'fans_id']);
		}
	}
