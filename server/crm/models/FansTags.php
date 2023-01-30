<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\DateUtil;
	use app\util\SUtils;
	use Yii;
	use yii\db\Expression;

	/**
	 * This is the model class for table "{{%fans_tags}}".
	 *
	 * @property int    $id
	 * @property int    $fans_id     粉丝ID
	 * @property int    $tags_id     标签ID
	 * @property string $create_time 创建时间
	 *
	 * @property Tags   $tags
	 * @property Fans   $fans
	 */
	class FansTags extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%fans_tags}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['fans_id', 'tags_id'], 'integer'],
				[['create_time'], 'safe'],
				[['tags_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tags::className(), 'targetAttribute' => ['tags_id' => 'id']],
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
				'tags_id'     => Yii::t('app', '标签ID'),
				'create_time' => Yii::t('app', '创建时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getTags ()
		{
			return $this->hasOne(Tags::className(), ['id' => 'tags_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getFans ()
		{
			return $this->hasOne(Fans::className(), ['id' => 'fans_id']);
		}

		/**
		 * 创建标签关系
		 *
		 * @param integer $fansId
		 * @param integer $tagsId
		 *
		 * @return bool
		 * @throws InvalidDataException
		 */
		public static function create ($fansId, $tagsId)
		{
			$fansTags = static::findOne(['fans_id' => $fansId, 'tags_id' => $tagsId]);

			if (empty($fansTags)) {
				$fansTags              = new FansTags();
				$fansTags->create_time = DateUtil::getCurrentTime();
			}

			$fansTags->fans_id = $fansId;
			$fansTags->tags_id = $tagsId;

			if ($fansTags->dirtyAttributes) {
				if (!$fansTags->validate() || !$fansTags->save()) {
					throw new InvalidDataException(SUtils::modelError($fansTags));
				}
			}

			return true;
		}

		/**
		 * @title           打标签和移除标签时去除无效的openid
		 *
		 * @param $tagId
		 * @param $openids
		 * @param $type 1 新增 2 移除
		 *
		 * @remark          Create by PhpStorm. User: wangpan. Date: 2019/11/26 9:16
		 * @number          0
		 *
		 */
		public static function getNewOpenids ($tagId, $openids, $type)
		{
			$result = [];
			if ($type == 1) {
				//新增
				foreach ($openids as $v) {
					$data = Fans::find()->alias('f');
					$data = $data->leftJoin('{{%fans_tags}} ft', '`ft`.`fans_id` = `f`.`id`');
					$data = $data->andWhere(['f.openid' => $v, 'ft.tags_id' => $tagId]);
					$data = $data->one();
					if (empty($data)) {
						array_push($result, $v);
					}
				}
			} else {
				foreach ($openids as $v) {
					$data = Fans::find()->alias('f');
					$data = $data->leftJoin('{{%fans_tags}} ft', '`ft`.`fans_id` = `f`.`id`');
					$data = $data->andWhere(['f.openid' => $v, 'ft.tags_id' => $tagId]);
					$data = $data->one();
					if (!empty($data)) {
						array_push($result, $v);
					}
				}
			}

			return $result;
		}

	}
