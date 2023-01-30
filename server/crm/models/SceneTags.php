<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%scene_tags}}".
	 *
	 * @property int    $id
	 * @property int    $scene_id    参数二维码ID
	 * @property int    $tag_id      标签ID
	 * @property string $create_time 创建时间
	 *
	 * @property Tags   $tag
	 * @property Scene  $scene
	 */
	class SceneTags extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%scene_tags}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['scene_id', 'tag_id'], 'integer'],
				[['create_time'], 'safe'],
				[['tag_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tags::className(), 'targetAttribute' => ['tag_id' => 'id']],
				[['scene_id'], 'exist', 'skipOnError' => true, 'targetClass' => Scene::className(), 'targetAttribute' => ['scene_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => Yii::t('app', 'ID'),
				'scene_id'    => Yii::t('app', '参数二维码ID'),
				'tag_id'      => Yii::t('app', '标签ID'),
				'create_time' => Yii::t('app', '创建时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getTag ()
		{
			return $this->hasOne(Tags::className(), ['id' => 'tag_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getScene ()
		{
			return $this->hasOne(Scene::className(), ['id' => 'scene_id']);
		}
	}
