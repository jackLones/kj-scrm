<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\DateUtil;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%work_external_contact_follow_statistic}}".
	 *
	 * @property int                 $id
	 * @property int                 $corp_id         授权的企业ID
	 * @property int                 $external_userid 外部联系人ID
	 * @property int                 $user_id         成员ID
	 * @property int                 $follow_id       跟进状态ID
	 * @property string              $data_time       统计时间
	 * @property int                 $type            类型1日2周3月
	 * @property string              $create_time     创建时间
	 *
	 * @property WorkExternalContact $externalUser
	 * @property WorkCorp            $corp
	 * @property Follow              $follow
	 * @property WorkUser            $user
	 */
	class WorkExternalContactFollowStatistic extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_external_contact_follow_statistic}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_id', 'external_userid', 'user_id', 'follow_id', 'type'], 'integer'],
				[['create_time'], 'safe'],
				[['data_time'], 'string', 'max' => 64],
				[['external_userid'], 'exist', 'skipOnError' => true, 'targetClass' => WorkExternalContact::className(), 'targetAttribute' => ['external_userid' => 'id']],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
				[['follow_id'], 'exist', 'skipOnError' => true, 'targetClass' => Follow::className(), 'targetAttribute' => ['follow_id' => 'id']],
				[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkUser::className(), 'targetAttribute' => ['user_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'              => Yii::t('app', 'ID'),
				'corp_id'         => Yii::t('app', '授权的企业ID'),
				'external_userid' => Yii::t('app', '外部联系人ID'),
				'user_id'         => Yii::t('app', '成员ID'),
				'follow_id'       => Yii::t('app', '跟进状态ID'),
				'data_time'       => Yii::t('app', '统计时间'),
				'type'            => Yii::t('app', '类型1日2周3月'),
				'create_time'     => Yii::t('app', '创建时间'),
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
		public function getExternalUser ()
		{
			return $this->hasOne(WorkExternalContact::className(), ['id' => 'external_userid']);
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
		public function getFollow ()
		{
			return $this->hasOne(Follow::className(), ['id' => 'follow_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getUser ()
		{
			return $this->hasOne(WorkUser::className(), ['id' => 'user_id']);
		}

		/**
		 * 按每日、每周、每月 统计客户跟进数据
		 *
		 * @param int    $type 1日、2周、3月
		 * @param string $startTime
		 *
		 */
		public static function followStatistic ($type, $startTime = '')
		{
			try {
				if (empty($startTime)) {
					$startTime = time();
				}

				if ($type == 1) {
					$startDate = date('Y-m-d', strtotime('-1 day', $startTime));
					$endData   = $startDate . ' 23:59:59';
					$data_time = $startDate;
				} elseif ($type == 2) {
					$startDate = date('Y-m-d', mktime(0, 0, 0, date('m', $startTime), date('d', $startTime) - date('w', $startTime) + 1 - 7, date('Y', $startTime)));
					$endData   = date("Y-m-d H:i:s", mktime(23, 59, 59, date("m", $startTime), date("d", $startTime) - date("w", $startTime) + 7 - 7, date("Y", $startTime)));
					$data_time = $startDate;
				} elseif ($type == 3) {
					$data_time1 = date('Y-m', strtotime('-1 month ', $startTime));
					$startDate  = $data_time1 . '-01';
					$endData    = date('Y-m-t 23:59:59', strtotime('-1 month', $startTime));
					$data_time  = $startDate;
				} else {
					return '';
				}

				$date1    = strtotime($startDate);
				$date2    = strtotime($endData);
				$workCorp = WorkCorp::find()->all();
				if (!empty($workCorp)) {
					/** @var WorkCorp $corp */
					foreach ($workCorp as $corp) {
						$followUser = WorkExternalContactFollowUser::find()->alias('f');
						$followUser = $followUser->leftJoin('{{%work_external_contact}} c', '`f`.`external_userid` = `c`.`id`')->where(['c.corp_id' => $corp->id]);
						$followUser = $followUser->andFilterWhere(['between', 'f.createtime', $date1, $date2]);
						$followUser = $followUser->select('c.corp_id,f.user_id,f.external_userid,f.follow_id')->asArray()->all();
						if (!empty($followUser)) {
							foreach ($followUser as $user) {
								$followStatistic = WorkExternalContactFollowStatistic::findOne(['type' => $type, 'data_time' => $data_time, 'corp_id' => $user['corp_id'], 'user_id' => $user['user_id'], 'external_userid' => $user['external_userid'], 'follow_id' => $user['follow_id']]);
								if (empty($followStatistic)) {
									$followStatistic                  = new WorkExternalContactFollowStatistic();
									$followStatistic->corp_id         = $user['corp_id'];
									$followStatistic->external_userid = $user['external_userid'];
									$followStatistic->user_id         = $user['user_id'];
									$followStatistic->data_time       = $data_time;
									$followStatistic->type            = $type;
									$followStatistic->create_time     = DateUtil::getCurrentTime();

									if (!empty($user['follow_id'])) {
										$followStatistic->follow_id = $user['follow_id'];
									}

									if (!$followStatistic->validate() || !$followStatistic->save()) {
										throw new InvalidDataException(SUtils::modelError($followStatistic));
									}
								}
							}
						}
					}
				}
			} catch (\Exception $e) {
				\Yii::error($e->getMessage(), 'followStatistic');
			}
		}

		//跟进统计补充数据
		public static function supplyData ($corpId)
		{
			ini_set('memory_limit', '1024M');
			set_time_limit(0);

			if (empty($corpId)) {
				return false;
			}
			$createTime = DateUtil::getCurrentTime();

			//最早的统计记录
			$followInfo = self::find()->where(['corp_id' => $corpId, 'type' => 1])->orderBy(['data_time' => SORT_ASC])->one();
			if (!empty($followInfo)) {
				$endTime = strtotime($followInfo->data_time);
			} else {
				$endTime = strtotime(date('Y-m-d')) - 86400;
			}
			$page     = 1;
			$pageSize = 500;
			while (true) {
				$offset = ($page - 1) * $pageSize;

				$followUser = WorkExternalContactFollowUser::find()->alias('f');
				$followUser = $followUser->leftJoin('{{%work_external_contact}} c', '`f`.`external_userid` = `c`.`id`')->where(['c.corp_id' => $corpId]);
				$followUser = $followUser->andFilterWhere(['<', 'f.createtime', $endTime]);
				$followUser = $followUser->select('f.user_id,f.external_userid,f.follow_id,f.createtime')->limit($pageSize)->offset($offset)->asArray()->all();

				if (empty($followUser)) {
					break;
				}

				foreach ($followUser as $user) {
					$dataTime       = date('Y-m-d', $user['createtime']);
					$followStatistic = WorkExternalContactFollowStatistic::findOne(['type' => 1, 'data_time' => $dataTime, 'corp_id' => $corpId, 'user_id' => $user['user_id'], 'external_userid' => $user['external_userid'], 'follow_id' => $user['follow_id']]);
					if (empty($followStatistic)) {
						try {
							$followStatistic                  = new WorkExternalContactFollowStatistic();
							$followStatistic->corp_id         = $corpId;
							$followStatistic->external_userid = $user['external_userid'];
							$followStatistic->user_id         = $user['user_id'];
							$followStatistic->data_time       = $dataTime;
							$followStatistic->type            = 1;
							$followStatistic->create_time     = $createTime;

							if (!empty($user['follow_id'])) {
								$followStatistic->follow_id = $user['follow_id'];
							} else {
								continue;
							}

							if (!$followStatistic->validate() || !$followStatistic->save()) {
								throw new InvalidDataException(SUtils::modelError($followStatistic));
							}
						} catch (\Exception $e) {

						}
					}
				}

				$page++;
			}

			return true;
		}
	}
