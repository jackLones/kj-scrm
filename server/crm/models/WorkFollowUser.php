<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\queue\GetFollowUserJob;
	use app\util\DateUtil;
	use app\util\SUtils;
	use app\util\WorkUtils;
	use dovechen\yii2\weWork\Work;
	use Yii;

	/**
	 * This is the model class for table "{{%work_follow_user}}".
	 *
	 * @property int      $id
	 * @property int      $corp_id     授权的企业ID
	 * @property int      $user_id     成员ID
	 * @property int      $status      0：移除；1：可用
	 * @property string   $create_time 创建时间
	 *
	 * @property WorkUser $user
	 * @property WorkCorp $corp
	 */
	class WorkFollowUser extends \yii\db\ActiveRecord
	{
		const CANT_USE = 0;
		const CAN_USE = 1;

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_follow_user}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_id', 'user_id', 'status'], 'integer'],
				[['create_time'], 'safe'],
				[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkUser::className(), 'targetAttribute' => ['user_id' => 'id']],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => Yii::t('app', 'ID'),
				'corp_id'     => Yii::t('app', '授权的企业ID'),
				'user_id'     => Yii::t('app', '成员ID'),
				'status'      => Yii::t('app', '0：移除；1：可用'),
				'create_time' => Yii::t('app', '创建时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getUser ()
		{
			return $this->hasOne(WorkUser::className(), ['id' => 'user_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getCorp ()
		{
			return $this->hasOne(WorkCorp::className(), ['id' => 'corp_id']);
		}

		/**
		 * @param       $corpId
		 * @param int   $uid
		 * @param false $needAuthStoreUser
		 * @param false $openSubUser
		 *
		 * @return bool
		 *
		 * @throws InvalidDataException
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function getFollowUser ($corpId, $uid = 0, $needAuthStoreUser = false, $openSubUser = false)
		{
			$status   = false;
			$authCorp = WorkCorp::findOne($corpId);

			if (empty($authCorp)) {
				throw new InvalidDataException('参数不正确。');
			}

			$workApi = WorkUtils::getWorkApi($corpId, WorkUtils::EXTERNAL_API);

			if (!empty($workApi)) {
				$followInfo = $workApi->ECGetFollowUserList();
				Yii::error($followInfo, '$followInfo');
				if (!empty($followInfo)) {
					static::updateAll(['status' => static::CANT_USE], ['corp_id' => $corpId]);
					WorkUser::updateAll(['is_external' => static::CANT_USE], ['corp_id' => $corpId]);

					if (empty($followInfo['follow_user'])) {
						$status = true;
					} else {
						$jobIds = [];
						foreach ($followInfo['follow_user'] as $userId) {
							$jobId = Yii::$app->queue->push(new GetFollowUserJob([
								'corp_id' => $corpId,
								'user_id' => $userId,
							]));
							array_push($jobIds, $jobId);
						}

						$cacheKey = 'getFollowUserJob' . $corpId;
						Yii::$app->cache->set($cacheKey, $jobIds);
						Yii::$app->queue->push(new GetFollowUserJob([
							'check'           => true,
							'need_auth_store' => $needAuthStoreUser,
							'open_sub_user'   => $openSubUser,
							'uid'             => $uid,
							'cache_key'       => $cacheKey,
						]));

						$status = true;
					}
				}
			}

			return $status;
		}

		/**
		 * @param $corpId
		 * @param $userId
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 */
		public static function setFollowUser ($corpId, $userId)
		{
			$followInfo = static::findOne(['corp_id' => $corpId, 'user_id' => $userId]);

			if (empty($followInfo)) {
				$followInfo              = new WorkFollowUser();
				$followInfo->create_time = DateUtil::getCurrentTime();
			}

			if (empty($followInfo->status) || $followInfo->status == self::CANT_USE) {
				$followInfo->status = self::CAN_USE;
			}

			$followInfo->corp_id = $corpId;
			$followInfo->user_id = $userId;

			if ($followInfo->dirtyAttributes) {
				if (!$followInfo->validate() || !$followInfo->save()) {
					throw new InvalidDataException(SUtils::modelError($followInfo));
				}
			}
			WorkUser::updateAll(["is_external" => $followInfo->status], ["id" => $userId]);
//			$workUser = WorkUser::findOne($userId);
//			if (!empty($workUser)) {
//				$workUser->is_external = $followInfo->status;
//				$workUser->save();
//
//			}
			return $followInfo->id;
		}
	}
