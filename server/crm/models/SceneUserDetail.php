<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\DateUtil;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%scene_user_detail}}".
	 *
	 * @property int    $id
	 * @property int    $scene_id  参数二维码ID
	 * @property int    $fans_id   粉丝ID
	 * @property int    $is_new    是否是新粉丝
	 * @property string $scan_time 扫码时间
	 *
	 * @property Scene  $scene
	 */
	class SceneUserDetail extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%scene_user_detail}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['scene_id', 'fans_id', 'is_new'], 'integer'],
				[['scan_time'], 'safe'],
				[['scene_id'], 'exist', 'skipOnError' => true, 'targetClass' => Scene::className(), 'targetAttribute' => ['scene_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'        => Yii::t('app', 'ID'),
				'scene_id'  => Yii::t('app', '参数二维码ID'),
				'fans_id'   => Yii::t('app', '粉丝ID'),
				'is_new'    => Yii::t('app', '是否是新粉丝'),
				'scan_time' => Yii::t('app', '扫码时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getScene ()
		{
			return $this->hasOne(Scene::className(), ['id' => 'scene_id']);
		}

		/**
		 * @param int $sceneId
		 * @param int $fansId
		 * @param int $is_new
		 *
		 * @return bool
		 * @throws InvalidDataException
		 */
		public static function create ($sceneId, $fansId, $is_new = 0)
		{
			$sceneUserDetail = static::findOne(['scene_id' => $sceneId, 'fans_id' => $fansId]);
			if (empty($sceneUserDetail)) {
				$sceneUserDetail           = new SceneUserDetail();
				$sceneUserDetail->scene_id = $sceneId;
				$sceneUserDetail->fans_id  = $fansId;
			}
			$sceneUserDetail->is_new    = $is_new;
			$sceneUserDetail->scan_time = DateUtil::getCurrentTime();
			if (!$sceneUserDetail->validate() || !$sceneUserDetail->save()) {
				throw new InvalidDataException(SUtils::modelError($sceneUserDetail));
			}

			return true;
		}

		//删除扫码用户记录
		public static function deleteRecord ($sceneId, $fansId)
		{
			$sceneUserDetail = static::findOne(['scene_id' => $sceneId, 'fans_id' => $fansId]);
			if (!empty($sceneUserDetail)) {
				$sceneUserDetail->delete();
			}
		}
	}
