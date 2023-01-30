<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%work_group_clock_detail}}".
	 *
	 * @property int                $id
	 * @property int                $join_id     参与者ID
	 * @property int                $punch_time  打卡时间
	 * @property int                $create_time 创建时间
	 *
	 * @property WorkGroupClockJoin $join
	 */
	class WorkGroupClockDetail extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_group_clock_detail}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['join_id', 'create_time'], 'integer'],
				[['join_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkGroupClockJoin::className(), 'targetAttribute' => ['join_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => 'ID',
				'join_id'     => '参与者ID',
				'punch_time'  => '打卡时间',
				'create_time' => '创建时间',
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
		public function getJoin ()
		{
			return $this->hasOne(WorkGroupClockJoin::className(), ['id' => 'join_id']);
		}

		/**
		 * @param $joinId
		 * @param $sTime
		 * @param $eTime
		 *
		 * @return int
		 *
		 */
		public static function isSend ($joinId, $sTime, $eTime)
		{
			$send        = 0;
			$clockDetail = WorkGroupClockDetail::find()->where(['join_id' => $joinId])->andFilterWhere(['between', 'create_time', $sTime, $eTime])->one();
			if (!empty($clockDetail)) {
				$send = 1;
			}

			return $send;
		}

		/**
		 * @param $joinId
		 * @param $time
		 *
		 * @return bool
		 *
		 */
		public static function addData ($joinId, $time)
		{
			$timeNew = date('Y-m-d', $time);
			$detail  = static::findOne(['join_id' => $joinId, 'punch_time' => $timeNew]);
			if (empty($detail)) {
				$detail              = new WorkGroupClockDetail();
				$detail->join_id     = $joinId;
				$detail->punch_time  = $timeNew;
				$detail->create_time = $time;
				$detail->save();

				$clockJoin = WorkGroupClockJoin::findOne($joinId);
				if (!empty($clockJoin)) {
					$clockJoin->last_time  = $time;
					$clockJoin->total_days = intval($clockJoin->total_days) + 1;
					if (empty($clockJoin->continue_days)) {
						$clockJoin->continue_days         = 1;
						$clockJoin->history_continue_days = 1;
					} else {
						$eTime    = $time - 86400;
						$lastDate = date("Y-m-d", $eTime);
						$continue = static::findOne(['join_id' => $joinId, 'punch_time' => $lastDate]);
						if (!empty($continue)) {
							$continueDay              = intval($clockJoin->continue_days) + 1;
							$clockJoin->continue_days = $continueDay;
							if ($continueDay > $clockJoin->history_continue_days) {
								$clockJoin->history_continue_days = $continueDay;
							}
						} else {
							$clockJoin->continue_days = 1;
						}
					}
					$clockJoin->save();
				}
			}

			return true;
		}

		public static function getData ($joinId)
		{
			$data   = [];
			$detail = WorkGroupClockDetail::find()->where(['join_id' => $joinId])->all();
			if (!empty($detail)) {
				/** @var WorkGroupClockDetail $val */
				foreach ($detail as $k => $val) {
					$time             = $val->punch_time;
					$data[$k]['date'] = $time;
				}
			}

			return $data;
		}

	}
