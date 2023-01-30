<?php

	namespace app\models;

	use Yii;
	use app\util\SUtils;
	use app\components\InvalidDataException;

	/**
	 * This is the model class for table "{{%work_chat_group}}".
	 *
	 * @property int      $id
	 * @property int      $corp_id    授权的企业ID
	 * @property string   $group_name 群分组名称，长度限制为32个字以内（汉字或英文字母），分组名不可与其他组名重名
	 * @property int      $sort       排序
	 * @property int      $status     是否有效1是0否
	 *
	 * @property WorkCorp $corp
	 */
	class WorkChatGroup extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_chat_group}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_id', 'sort', 'status'], 'integer'],
				[['group_name'], 'string', 'max' => 32],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'         => Yii::t('app', 'ID'),
				'corp_id'    => Yii::t('app', '授权的企业ID'),
				'group_name' => Yii::t('app', '群分组名称，长度限制为32个字以内（汉字或英文字母），分组名不可与其他组名重名'),
				'sort'       => Yii::t('app', '排序'),
				'status'     => Yii::t('app', '是否有效1是0否'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getCorp ()
		{
			return $this->hasOne(WorkCorp::className(), ['id' => 'corp_id']);
		}

		public function dumpData ()
		{
			$result = [
				'id'         => $this->id,
				'key'        => $this->id,
				'group_name' => $this->group_name,
			];

			return $result;
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
		 * @param int $id
		 * @param     $corp_id
		 * @param     $groupName
		 *
		 * @return bool
		 *
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \app\components\InvalidDataException
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function add ($id = 0, $corp_id, $groupName)
		{
			if (empty($id)) {
				$workGroup             = new WorkChatGroup();
				$workGroup->corp_id    = $corp_id;
				$workGroup->group_name = $groupName;
				$workGroup->status     = 1;
				if (!$workGroup->validate() || !$workGroup->save()) {
					throw new InvalidDataException(SUtils::modelError($workGroup));
				}
			} else {
				$workGroup             = static::findOne($id);
				$workGroup->group_name = $groupName;
				if (!$workGroup->validate() || !$workGroup->save()) {
					throw new InvalidDataException(SUtils::modelError($workGroup));
				}
			}

			return $workGroup->id;
		}
	}
