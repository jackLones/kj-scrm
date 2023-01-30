<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\DateUtil;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%user_corp_relation}}".
	 *
	 * @property int      $id
	 * @property int      $uid         用户ID
	 * @property int      $corp_id     授权ID
	 * @property string   $update_time 更新时间
	 * @property string   $create_time 创建时间
	 *
	 * @property WorkCorp $corp
	 * @property User     $u
	 */
	class UserCorpRelation extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%user_corp_relation}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['uid', 'corp_id'], 'integer'],
				[['update_time', 'create_time'], 'safe'],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
				[['uid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['uid' => 'uid']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => Yii::t('app', 'ID'),
				'uid'         => Yii::t('app', '用户ID'),
				'corp_id'     => Yii::t('app', '授权ID'),
				'update_time' => Yii::t('app', '更新时间'),
				'create_time' => Yii::t('app', '创建时间'),
			];
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
		 * @param $uid
		 * @param $corpId
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 */
		public static function setRelation ($uid, $corpId)
		{
			$relation = static::findOne(['corp_id' => $corpId]);

			if (empty($relation)) {
				$relation              = new UserCorpRelation();
				$relation->create_time = DateUtil::getCurrentTime();
			} else {
				$relation->update_time = DateUtil::getCurrentTime();
			}

			if (empty($relation->uid)) {
				$relation->uid = $uid;
			} else if ($relation->uid != $uid) {
				throw new InvalidDataException('该企业微信已被其他账号授权使用，不可重复授权。');
			}

			$relation->corp_id = $corpId;

			if (!$relation->validate() || !$relation->save()) {
				throw new InvalidDataException(SUtils::modelError($relation));
			}

			return $relation->id;
		}
	}
