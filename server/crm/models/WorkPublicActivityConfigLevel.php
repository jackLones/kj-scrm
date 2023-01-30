<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%work_public_activity_config_level}}".
	 *
	 * @property int                $id
	 * @property int                $activity_id              活动id
	 * @property int                $is_open                  是否开启
	 * @property int                $type                     1事物，2红包
	 * @property int                $level                    等级
	 * @property string             $prize_name               奖品名称
	 * @property string             $money_amount             红包金额
	 * @property int                $money_count              红包数量
	 * @property int                $number                   助力次数
	 * @property int                $num                      奖品数量
	 * @property int                $num_old                  常量库存
	 * @property int                $money_count_old          常量库存
	 * @property int                $create_time              修改时间
	 * @property int                $update_time              修改时间
	 *
	 * @property WorkPublicActivity $activity
	 */
	class WorkPublicActivityConfigLevel extends \yii\db\ActiveRecord
	{
		const RED_TYPE = 2;
		const THINK_TYPE = 1;

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_public_activity_config_level}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['activity_id', 'is_open', 'type', 'level', 'money_count', 'num_old', 'money_count_old', 'number', 'num', 'create_time', 'update_time'], 'integer'],
				[['money_amount'], 'number'],
				[['prize_name'], 'string', 'max' => 60],
				[['activity_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkPublicActivity::className(), 'targetAttribute' => ['activity_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'           => Yii::t('app', 'ID'),
				'activity_id'  => Yii::t('app', '活动id'),
				'is_open'      => Yii::t('app', '是否开启'),
				'type'         => Yii::t('app', '1事物，2红包'),
				'level'        => Yii::t('app', '等级'),
				'prize_name'   => Yii::t('app', '奖品名称'),
				'money_amount' => Yii::t('app', '红包金额'),
				'money_count'  => Yii::t('app', '红包数量'),
				'number'       => Yii::t('app', '助力次数'),
				'num'          => Yii::t('app', '奖品数量'),
				'create_time'  => Yii::t('app', '修改时间'),
				'update_time'  => Yii::t('app', '修改时间'),
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
		public function getActivity ()
		{
			return $this->hasOne(WorkPublicActivity::className(), ['id' => 'activity_id']);
		}

		public static function activitySuccess ($activityId, $level_id, $openid, $data)
		{
			if (empty($openid) || empty($activityId)) {
				throw new InvalidDataException("参数不完整");
			}
			try {
				$activity = WorkPublicActivity::findOne($activityId);
				if (!$activity) {
					throw new InvalidDataException("活动不存在");
				}
//				if ($activity->is_over != 1 || $activity->end_time < time()) {
//					throw new InvalidDataException("活动已结束");
//				}
//				if (time() >= $activity->tickets_start && $activity->tickets_end >= time()) {
				if ($activity->type == 2) {
					$fans = WorkExternalContact::findOne(['openid' => $openid]);
					if (empty($fans)) {
						throw new InvalidDataException("未添加企业联系人");
					}
					$fansInfo = WorkPublicActivityFansUser::findOne(['external_userid' => $fans->id, "activity_id" => $activityId]);
				} else {
					$fans = Fans::findOne(['openid' => $openid]);
					if (empty($fans)) {
						throw new InvalidDataException("未关注公众号");
					}
					$fansInfo = WorkPublicActivityFansUser::findOne(['fans_id' => $fans->id, "activity_id" => $activityId]);
				}

				if (empty($fansInfo)) {
					throw new InvalidDataException("活动人员不存在");
				}

				$Transaction = \Yii::$app->db->beginTransaction();
				if ($activity->flow == 1 && !empty($fansInfo->prize)) {
					$prize = WorkPublicActivityPrizeUser::findOne($fansInfo->prize);
					if (!empty($fansInfo->prize) && $prize->status == 1) {
						return ["error" => 0, "msg" => '已兑奖'];
					}
					$prize->setAttributes($data);
					if (!$prize->validate() && !$prize->save()) {
						$Transaction->rollBack();
						throw new InvalidDataException(SUtils::modelError($prize));
					}
				} else {
					$prize = new WorkPublicActivityPrizeUser();
					/** @var WorkPublicActivityConfigLevel $level * */
					$level = WorkPublicActivityConfigLevel::find()->where(["activity_id" => $activityId])->andWhere("$fansInfo->activity_num >= number")->orderBy("id asc")->one();
					if (empty($level)) {
						throw new InvalidDataException("未达获奖标准");
					}
					if ($level->type == 1 && $level->num <= 0) {
						throw new InvalidDataException("奖品库存为0");
					}
					if ($level->type == 2 && $level->money_count <= 0) {
						throw new InvalidDataException("红包库存为0");
					}
					$level->num = $level->num - 1;
					if ($level->type == 2) {
						$data["status"] = 1;
					}
					$data["level_id"]    = $level->id;
					$data["level"]       = $level->level;
					$data["activity_id"] = $activityId;
					$data["public_id"]   = $fansInfo->id;
					$data["create_time"] = time();
					$prize->setAttributes($data);
					if (!$prize->validate() && !$prize->save()) {
						$Transaction->rollBack();
						throw new InvalidDataException(SUtils::modelError($prize));
					}
					$level_end = json_decode($activity->level_end, 1);
					if (!empty($level_end)) {
						$tmp = array_pop($level_end);
						if ($tmp == $level->level && ($level->num == 0 || $level->money_count == 0)) {
							$activity->is_over = 3;
							$activity->save();
						}
					}
					$fansInfo->prize = $prize->id;
					$fansInfo->save();
					$level->update_time = time();
					$level->save();
				}
				$fansInfo->is_form = 1;
				$fansInfo->save();
				$prize->save();

				$Transaction->commit();

				return ["error" => 0];
//				}
//				throw new InvalidDataException("不在兑奖时间");
			} catch (\Exception $e) {
				throw new InvalidDataException($e->getMessage());
			}

		}
	}
