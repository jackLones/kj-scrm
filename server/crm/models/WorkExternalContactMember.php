<?php

	namespace app\models;

	use app\util\DateUtil;
	use Yii;
	use app\util\SUtils;
	use app\components\InvalidDataException;

	/**
	 * This is the model class for table "{{%work_external_contact_member}}".
	 *
	 * @property int                 $id
	 * @property int                 $external_userid 外部联系人ID
	 * @property int                 $sign_id         店铺ID
	 * @property int                 $member_id       会员id
	 * @property int                 $uc_id           用户id
	 * @property int                 $is_bind         1已绑定 0未绑定
	 * @property string              $create_time     创建时间
	 * @property int                 $follow_user_id  企业微信外部联系人ID
	 *
	 * @property WorkExternalContact $externalUser
	 * @property ApplicationSign     $sign
	 */
	class WorkExternalContactMember extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_external_contact_member}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['external_userid', 'sign_id', 'is_bind', 'follow_user_id'], 'integer'],
				[['create_time'], 'safe'],
				[['external_userid'], 'exist', 'skipOnError' => true, 'targetClass' => WorkExternalContact::className(), 'targetAttribute' => ['external_userid' => 'id']],
				[['sign_id'], 'exist', 'skipOnError' => true, 'targetClass' => ApplicationSign::className(), 'targetAttribute' => ['sign_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'              => 'ID',
				'external_userid' => '外部联系人ID',
				'sign_id'         => '店铺ID',
				'member_id'       => '会员id或有赞手机号',
				'uc_id'           => '用户id或有赞手机号',
				'is_bind'         => '1已绑定 0未绑定',
				'create_time'     => '创建时间',
				'follow_user_id'  => '企业微信外部联系人ID',
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getExternalUser ()
		{
			return $this->hasOne(WorkExternalContact::className(), ['id' => 'external_userid']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getSign ()
		{
			return $this->hasOne(ApplicationSign::className(), ['id' => 'sign_id']);
		}

		public static function add ($data)
		{
			$member = static::findOne(['external_userid' => $data['external_userid'], 'sign_id' => $data['sign_id'], 'uc_id' => $data['uc_id']]);
			if (empty($member)) {
				$member              = new WorkExternalContactMember();
				$member->create_time = DateUtil::getCurrentTime();
			}
			$member->sign_id         = $data['sign_id'];
			$member->external_userid = $data['external_userid'];
			$member->member_id       = $data['member_id'];
			$member->uc_id           = $data['uc_id'];
			$member->is_bind         = 1;
			if (!$member->validate() || !$member->save()) {
				throw new InvalidDataException(SUtils::modelError($member));
			}

			return $member->id;
		}
	}
