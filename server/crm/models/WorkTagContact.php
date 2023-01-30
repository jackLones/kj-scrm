<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%work_tag_contact}}".
	 *
	 * @property int                 $id
	 * @property int                 $tag_id     授权的企业的标签ID
	 * @property int                 $contact_id 授权的企业的成员ID
	 *
	 * @property WorkExternalContact $contact
	 * @property WorkTag             $tag
	 */
	class WorkTagContact extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_tag_contact}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['tag_id', 'contact_id'], 'integer'],
				[['contact_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkExternalContact::className(), 'targetAttribute' => ['contact_id' => 'id']],
				[['tag_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkTag::className(), 'targetAttribute' => ['tag_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'         => 'ID',
				'tag_id'     => '授权的企业的标签ID',
				'contact_id' => '授权的企业的成员ID',
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
		public function getContact ()
		{
			return $this->hasOne(WorkExternalContact::className(), ['id' => 'contact_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getTag ()
		{
			return $this->hasOne(WorkTag::className(), ['id' => 'tag_id']);
		}



		/**
		 * 根据外部联系人id获取标签名称
		 *
		 * @param int $id   部门id
		 * @param int $type 0客户1员工
		 * @param int $from_unique
		 * @param array $userId
		 * @param string|int $corp_id
		 *
		 * @return array
		 *
		 */
		public static function getTagNameByContactId ($id, $type,$from_unique=0, $userId=[],$corp_id = 0)
		{
			$name = [];
			if (!empty($id) && !empty($corp_id)) {
				if ($type == 0) {
					if ($from_unique == 1) {
						$followUser = WorkExternalContactFollowUser::findOne($id);
						if (!empty($followUser)) {
							$contactUser = WorkExternalContactFollowUser::find()->where(['external_userid' => $followUser->external_userid, 'user_id' => $userId])->select('id')->asArray()->all();
							if (!empty($contactUser)) {
								$id = array_column($contactUser,"id");
							}
						}
					}
					$contact = WorkTagFollowUser::find()->where(['follow_user_id' => $id, 'status' => 1])->groupBy('tag_id')->select('tag_id')->asArray()->all();
				} else {
					$contact = WorkTagUser::find()->where(['user_id' => $id])->select('tag_id')->asArray()->all();
				}
				if (!empty($contact)) {
					$tagIds = array_column($contact, 'tag_id');
					if (!empty($tagIds)) {
						$WorkTag = WorkTag::find()->where(["and",["corp_id"=>$corp_id],["in","id",$tagIds]])->andWhere(["is_del"=>0])->select("tagname")->asArray()->all();
						if(!empty($WorkTag)){
							$name = array_column($WorkTag,"tagname");
						}
					}
				}
			}

			return $name;
		}

		/**
		 *
		 * @param $type
		 * @param $tagId
		 *
		 * @return boolean
		 *
		 */
		public static function deleteTag ($type, $tagId)
		{
			if ($type == 0) {
				WorkTagContact::deleteAll(['tag_id' => $tagId]);
				WorkTagFollowUser::deleteAll(['tag_id' => $tagId]);
			} else {
				WorkTagUser::deleteAll(['tag_id' => $tagId]);
			}

			return true;
		}

	}
