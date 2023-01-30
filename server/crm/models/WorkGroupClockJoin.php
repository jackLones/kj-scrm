<?php

	namespace app\models;

	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%work_group_clock_join}}".
	 *
	 * @property int                    $id
	 * @property int                    $activity_id             活动ID
	 * @property int                    $external_id             外部联系人id
	 * @property string                 $openid                  openid未知客户
	 * @property string                 $name                    姓名
	 * @property string                 $mobile                  手机号
	 * @property string                 $region                  地区
	 * @property string                 $city                    城市
	 * @property string                 $county                  县
	 * @property string                 $detail                  详细地址
	 * @property string                 $remark                  备注
	 * @property int                    $continue_days           连续打卡天数
	 * @property int                    $history_continue_days   历史最高连续打卡天数
	 * @property int                    $total_days              累计打卡天数
	 * @property int                    $create_time             创建时间
	 * @property int                    $last_time               最近打卡时间
	 *
	 * @property WorkGroupClockDetail[] $workGroupClockDetails
	 * @property WorkGroupClockActivity $activity
	 * @property WorkExternalContact    $external
	 * @property WorkGroupClockPrize[]  $workGroupClockPrizes
	 */
	class WorkGroupClockJoin extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_group_clock_join}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['activity_id', 'external_id', 'continue_days', 'total_days', 'create_time'], 'integer'],
				[['openid'], 'string', 'max' => 64],
				[['name', 'mobile'], 'string', 'max' => 32],
				[['region', 'city', 'county'], 'string', 'max' => 60],
				[['detail', 'remark'], 'string', 'max' => 255],
				[['activity_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkGroupClockActivity::className(), 'targetAttribute' => ['activity_id' => 'id']],
				[['external_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkExternalContact::className(), 'targetAttribute' => ['external_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'                    => 'ID',
				'activity_id'           => '活动ID',
				'external_id'           => '外部联系人id',
				'openid'                => 'openid未知客户',
				'name'                  => '姓名',
				'mobile'                => '手机号',
				'region'                => '地区',
				'city'                  => '城市',
				'county'                => '县',
				'detail'                => '详细地址',
				'remark'                => '备注',
				'continue_days'         => '连续打卡天数',
				'history_continue_days' => '历史最高连续打卡天数',
				'total_days'            => '累计打卡天数',
				'create_time'           => '创建时间',
				'last_time'             => '最近打卡时间',
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
		public function getWorkGroupClockDetails ()
		{
			return $this->hasMany(WorkGroupClockDetail::className(), ['join_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getActivity ()
		{
			return $this->hasOne(WorkGroupClockActivity::className(), ['id' => 'activity_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getExternal ()
		{
			return $this->hasOne(WorkExternalContact::className(), ['id' => 'external_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkGroupClockPrizes ()
		{
			return $this->hasMany(WorkGroupClockPrize::className(), ['join_id' => 'id']);
		}

		/**
		 * @param $data
		 *
		 * @return bool
		 *
		 */
		public static function addData ($data)
		{
			$join = '';
			if (empty($data['openid']) && !empty($data['external_id'])) {
				$join = self::findOne(['activity_id' => $data['activity_id'], 'external_id' => $data['external_id']]);
				if (empty($join)) {
					$join              = new WorkGroupClockJoin();
					$join->create_time = time();
				}
			}
			if (!empty($data['openid']) && empty($data['external_id'])) {
				$join = self::findOne(['activity_id' => $data['activity_id'], 'openid' => $data['openid']]);
				if (empty($join)) {
					$join              = new WorkGroupClockJoin();
					$join->create_time = time();
				}
			}
			if (!empty($join)) {
				$join->activity_id = $data['activity_id'];
				$join->external_id = $data['external_id'];
				$join->openid      = $data['openid'];
				if (!$join->validate() || !$join->save()) {
					\Yii::error(SUtils::modelError($join), 'joinError');
				}
			}

			return $join->id;

		}

		/**
		 * @param     $activityId
		 * @param     $external_userid
		 * @param     $openid
		 * @param int $type 0、连续打卡 1、累计打卡
		 *
		 * @return int
		 *
		 */
		public static function getDays ($activityId, $external_userid, $openid, $type = 0)
		{
			$chooseDays = 0;
			$joinId     = 0;
			if (!empty($external_userid)) {
				$join = WorkGroupClockJoin::findOne(['activity_id' => $activityId, 'external_id' => $external_userid]);
				if (!empty($join)) {
					$joinId = $join->id;
					if ($type == 0) {
						$chooseDays = $join->continue_days;
					} else {
						$chooseDays = $join->total_days;
					}

				}
			} else {
				//未知客户
				$join = WorkGroupClockJoin::findOne(['activity_id' => $activityId, 'openid' => $openid]);
				if (!empty($join)) {
					$joinId = $join->id;
					if ($type == 0) {
						$chooseDays = $join->continue_days;
					} else {
						$chooseDays = $join->total_days;
					}

				}
			}

			return [
				'choose_days' => $chooseDays,
				'join_id'     => $joinId,
				'clock_join'  => $join
			];
		}

		//跟新群打卡参与表中external_id
		public static function updateJoinExternalId ($corpId, $externalId, $openId)
		{
			if (empty($corpId) || empty($externalId)) {
				return '';
			}
			if (empty($openId)) {
				$contact = WorkExternalContact::findOne($externalId);
				if (!empty($contact)) {
					$openId = $contact->openid;
				} else {
					return '';
				}
			}

			try {
				$clockList = WorkGroupClockActivity::find()->alias('wca');
				$clockList = $clockList->leftJoin('{{%work_group_clock_join}} cj', 'wca.id = cj.activity_id');
				$clockList = $clockList->where(['wca.corp_id' => $corpId, 'cj.openid' => $openId, 'cj.external_id' => NULL]);
				$clockList = $clockList->select('cj.id')->asArray()->all();
				if (!empty($clockList)) {
					$joinIds = array_column($clockList, 'id');
					WorkGroupClockJoin::updateAll(['external_id' => $externalId], ['id' => $joinIds]);
				}
			} catch (\Exception $e) {
				\Yii::error($e->getMessage(), 'updateJoinExternalId');
			}
		}

	}
