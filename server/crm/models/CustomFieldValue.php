<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\queue\ChangeUserAndChangeTagsJob;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%custom_field_value}}".
	 *
	 * @property int    $id
	 * @property int    $uid     商户id
	 * @property int    $type    类型：1客户2粉丝3客户群
	 * @property int    $cid     用户id
	 * @property int    $user_id 员工id
	 * @property int    $fieldid 属性字段表id
	 * @property string $value   用户属性值
	 * @property int    $time    编辑时间
	 */
	class CustomFieldValue extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%custom_field_value}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['uid', 'type', 'cid', 'user_id', 'fieldid', 'time'], 'integer'],
				[['cid', 'fieldid'], 'required'],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'      => Yii::t('app', 'ID'),
				'uid'     => Yii::t('app', '商户id'),
				'type'    => Yii::t('app', '类型：1客户2粉丝3客户群'),
				'cid'     => Yii::t('app', '用户id'),
				'user_id' => Yii::t('app', '员工id'),
				'fieldid' => Yii::t('app', '属性字段表id'),
				'value'   => Yii::t('app', '用户属性值'),
				'time'    => Yii::t('app', '编辑时间'),
			];
		}

		public function afterSave ($insert, $changedAttributes)
		{
			parent::afterSave($insert, $changedAttributes);

			//beenlee 用户自定义属性变更 检查并打标签
			if ($this->type == 1 && !empty($changedAttributes)) {
				$externalInfo = WorkExternalContactFollowUser::findOne($this->cid);
				if ($externalInfo) {
					$dateJob                = [];
					$dateJob['type']        = 5;
					$dateJob['corp_id']     = $externalInfo->externalUser->corp_id;
					$dateJob['uid']         = $this->uid;
					$dateJob['external_id'] = $externalInfo->external_userid;
//					\Yii::$app->queue->push(new ChangeUserAndChangeTagsJob($dateJob));
				}
			}
		}

		/**
		 * @param     $field
		 * @param     $externalContactId
		 * @param     $value
		 * @param     $fromEvent
		 * @param int $unshare_field 不共享会员画像1是0否
		 * @param int $user_id       员工id
		 * @param int $uid
		 */
		public static function add ($field, $externalContactId, $value, $fromEvent, $unshare_field = 0, $user_id = 0, $uid = 0)
		{
			$customField = CustomField::findOne(['key' => $field]);
			if (!empty($customField)) {
				if ($unshare_field == 0){
					$customFieldValue = static::findOne(['type' => 1, 'fieldid' => $customField->id, 'cid' => $externalContactId]);
				}else{
					$customFieldValue = static::findOne(['type' => 1, 'fieldid' => $customField->id, 'cid' => $externalContactId, 'user_id' => $user_id]);
				}
				if (empty($customFieldValue)) {
					$customFieldValue          = new CustomFieldValue();
					$customFieldValue->type    = 1;
					$customFieldValue->uid     = $uid;
					$customFieldValue->cid     = $externalContactId;
					$customFieldValue->fieldid = $customField->id;
					$customFieldValue->value   = $value;
					$customFieldValue->time    = time();
					if ($unshare_field == 1){
						$customFieldValue->user_id = $user_id;
					}
					if (!$customFieldValue->validate() || !$customFieldValue->save()) {
						\Yii::error(SUtils::modelError($customField), '$customFieldValue');
					}
				}else{
					if (empty($customFieldValue->value)) {
						$customFieldValue->value = $value;
					} else {
						if ($fromEvent == WorkExternalContact::EVENT_EXTERNAL_CONTACT) {
							//通过事件过来
							$customFieldValue->value = $value;
						}
					}

					if (!$customFieldValue->validate() || !$customFieldValue->save()) {
						\Yii::error(SUtils::modelError($customField), '$customFieldValue');
					}

				}
			}
		}


	}
