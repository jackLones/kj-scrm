<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%material_pull_time}}".
	 *
	 * @property int         $id
	 * @property int         $author_id     公众号ID
	 * @property int         $material_type 素材类型：1、图文（articles）；2、图片（image）；3、语音（voice）；4、视频（video）；5、缩略图（thumb)、6：参数二维码（scene）
	 * @property string      $pull_time     最后拉取日期
	 *
	 * @property WxAuthorize $author
	 */
	class MaterialPullTime extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%material_pull_time}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['author_id', 'material_type'], 'integer'],
				[['pull_time'], 'safe'],
				[['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => WxAuthorize::className(), 'targetAttribute' => ['author_id' => 'author_id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'            => Yii::t('app', 'ID'),
				'author_id'     => Yii::t('app', '公众号ID'),
				'material_type' => Yii::t('app', '素材类型：1、图文（articles）；2、图片（image）；3、语音（voice）；4、视频（video）；5、缩略图（thumb)、6：参数二维码（scene）'),
				'pull_time'     => Yii::t('app', '素材拉取时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getAuthor ()
		{
			return $this->hasOne(WxAuthorize::className(), ['author_id' => 'author_id']);
		}
	}
