<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%work_per_tag_follow_user}}".
	 *
	 * @property int                           $id
	 * @property int                           $corp_id        企业微信ID
	 * @property string                        $group_name     标签分组名称
	 * @property string                        $tag_name       标签名称
	 * @property int                           $status         状态0不显示1显示
	 * @property int                           $follow_user_id 外部联系人对应的ID
	 *
	 * @property WorkCorp                      $corp
	 * @property WorkExternalContactFollowUser $followUser
	 */
	class WorkPerTagFollowUser extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_per_tag_follow_user}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_id', 'follow_user_id', 'status'], 'integer'],
				[['group_name', 'tag_name'], 'string', 'max' => 255],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
				[['follow_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkExternalContactFollowUser::className(), 'targetAttribute' => ['follow_user_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'             => Yii::t('app', 'ID'),
				'corp_id'        => Yii::t('app', '企业微信ID'),
				'group_name'     => Yii::t('app', '标签分组名称'),
				'tag_name'       => Yii::t('app', '标签名称'),
				'status'         => Yii::t('app', '状态0不显示1显示'),
				'follow_user_id' => Yii::t('app', '外部联系人对应的ID'),
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
		public function getFollowUser ()
		{
			return $this->hasOne(WorkExternalContactFollowUser::className(), ['id' => 'follow_user_id']);
		}

		/**
		 * @param $id
		 * @param $from_unique
		 * @param $userId
		 *
		 * @return array
		 *
		 */
		public static function getTagName ($id, $from_unique = 0, $userId=[])
		{
			$name     = [];
			if ($from_unique == 1) {
				$followUser = WorkExternalContactFollowUser::findOne($id);
				if (!empty($followUser)) {
					$contactUser = WorkExternalContactFollowUser::find()->where(['external_userid' => $followUser->external_userid, 'user_id' => $userId])->select("id")->asArray()->all();
					$id = array_column($contactUser,"id");
				}
			}

			$tagFollow = static::find()->where(['follow_user_id' => $id, 'status' => 1])->groupBy('tag_name')->asArray()->all();
			if (!empty($tagFollow)) {
				foreach ($tagFollow as $follow) {
					array_push($name, $follow['tag_name']);
				}
			}

			return $name;
		}

	}
