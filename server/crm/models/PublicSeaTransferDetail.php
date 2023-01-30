<?php

	namespace app\models;

	use app\util\SUtils;
	use app\util\WorkUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%public_sea_transfer_detail}}".
	 *
	 * @property int                 $id
	 * @property int                 $uid             账户ID
	 * @property int                 $sea_id          公海客户ID
	 * @property int                 $corp_id         授权的企业ID
	 * @property int                 $external_userid 企微外部联系人id
	 * @property int                 $handover_userid 原归属成员id
	 * @property int                 $takeover_userid 接替成员的id
	 * @property int                 $status          分配状态：0待分配1已分配2客户拒绝3接替成员客户达到上限4分配中5未知
	 * @property int                 $allocate_time   分配时间
	 * @property int                 $add_time        添加时间
	 *
	 * @property WorkCorp            $corp
	 * @property WorkExternalContact $externalContact
	 * @property WorkUser            $handoverUser
	 */
	class PublicSeaTransferDetail extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%public_sea_transfer_detail}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['uid', 'sea_id', 'corp_id', 'external_userid', 'handover_userid', 'takeover_userid', 'status', 'allocate_time', 'add_time'], 'integer'],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
				[['external_userid'], 'exist', 'skipOnError' => true, 'targetClass' => WorkExternalContact::className(), 'targetAttribute' => ['external_userid' => 'id']],
				[['handover_userid'], 'exist', 'skipOnError' => true, 'targetClass' => WorkUser::className(), 'targetAttribute' => ['handover_userid' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'              => Yii::t('app', 'ID'),
				'uid'             => Yii::t('app', '账户ID'),
				'sea_id'          => Yii::t('app', '公海客户ID'),
				'corp_id'         => Yii::t('app', '授权的企业ID'),
				'external_userid' => Yii::t('app', '企微外部联系人id'),
				'handover_userid' => Yii::t('app', '原归属成员id'),
				'takeover_userid' => Yii::t('app', '接替成员的id'),
				'status'          => Yii::t('app', '分配状态：0待分配1已分配2客户拒绝3接替成员客户达到上限4分配中5未知'),
				'allocate_time'   => Yii::t('app', '分配时间'),
				'add_time'        => Yii::t('app', '添加时间'),
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
		public function getExternalContact ()
		{
			return $this->hasOne(WorkExternalContact::className(), ['id' => 'external_userid']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getHandoverUser ()
		{
			return $this->hasOne(WorkUser::className(), ['id' => 'handover_userid']);
		}

		//删除后的重新添加更新删除状态
		public static function updateDelType ($corpId, $externalUserId, $externalId, $userId)
		{
			try {
				/**@var WorkExternalContactFollowUser $followUser * */
				$followUser = WorkExternalContactFollowUser::find()->where(['external_userid' => $externalId, 'user_id' => $userId])->andWhere(['!=', 'del_type', WorkExternalContactFollowUser::WORK_CON_EX])->one();
				if (!empty($followUser)) {
					\Yii::error($followUser->id, 'updateDelTypeId');
					$workApi = WorkUtils::getWorkApi($corpId, WorkUtils::EXTERNAL_API);
					if (!empty($workApi)) {
						//获取成员的客户列表
						$externalUserInfo    = $workApi->ECList($followUser->userid);
						$externalContactInfo = SUtils::Object2Array($externalUserInfo);
						if (empty($externalContactInfo['external_userid'])) {
							return '';
						}
						if (!in_array($externalUserId, $externalContactInfo['external_userid'])) {
							return '';
						}
						//获取客户成员列表
						$externalUserInfo    = $workApi->ECGet($externalUserId);
						$externalContactInfo = SUtils::Object2Array($externalUserInfo);
						if (empty($externalContactInfo['follow_user'])) {
							return '';
						}
						$userIdArr = array_column($externalContactInfo['follow_user'], 'userid');
						if (!in_array($followUser->userid, $userIdArr)) {
							return '';
						}
						$followUser->del_type = WorkExternalContactFollowUser::WORK_CON_EX;
						$followUser->update();

						$claimUser = PublicSeaClaimUser::findOne(['corp_id' => $corpId, 'external_userid' => $externalId, 'new_user_id' => $userId, 'new_follow_user_id' => 0]);
						if (!empty($claimUser)) {
							$followUser->other_way = 1;
							$followUser->update();
						}

						PublicSeaClaimUser::updateAll(['new_follow_user_id' => $followUser->id, 'status' => 1], ['corp_id' => $corpId, 'external_userid' => $externalId, 'new_user_id' => $userId, 'new_follow_user_id' => 0]);
					}
				}
			} catch (\Exception $e) {
				\Yii::error($e->getMessage(), 'updateDelType');
			}
		}
	}
