<?php

	namespace app\models;

	use app\queue\UserRemindSendJob;
	use app\queue\WorkFollowMsgSendingJob;
	use Yii;

	/**
	 * This is the model class for table "{{%work_user_commission_remind_time}}".
	 *
	 * @property int                      $id
	 * @property int                      $remind_id 员工提醒id
	 * @property string                   $time      时间段
	 * @property int                      $create_time
	 *
	 * @property WorkUserCommissionRemind $remind
	 */
	class WorkUserCommissionRemindTime extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_user_commission_remind_time}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['remind_id', 'create_time'], 'integer'],
				[['time'], 'string', 'max' => 60],
				[['remind_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkUserCommissionRemind::className(), 'targetAttribute' => ['remind_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => Yii::t('app', 'ID'),
				'remind_id'   => Yii::t('app', '员工提醒id'),
				'time'        => Yii::t('app', '时间段'),
				'create_time' => Yii::t('app', 'Create Time'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getRemind ()
		{
			return $this->hasOne(WorkUserCommissionRemind::className(), ['id' => 'remind_id']);
		}

		/**
		 * 设置分段时间
		 *
		 * @param $id
		 * @param $data
		 */
		public static function setRemindTimeSelect ($id, $data)
		{
			$remindTimeAll = self::find()->where(["remind_id"=>$id])->asArray()->all();
			if(!empty($remindTimeAll)){
				$remindTimeAll = array_column($remindTimeAll,"time","id");
				$temp = array_diff($remindTimeAll,$data);
				$temp = array_keys($temp);
				self::deleteAll(["in","id",$temp]);
			}
			foreach ($data as $time) {
				if (empty($time)) {
					continue;
				}
				$remindTime = self::findOne(["remind_id"=>$id,"time"=>$time]);
				if(empty($remindTime)){
					$remindTime              = new self();
					$remindTime->time        = $time;
					$remindTime->remind_id   = $id;
					$remindTime->create_time = time();
					$temp                    = $remindTime->save();
					$sendTime = strtotime(date('Y-m-d') . ' ' . $time);
					$second   = $sendTime - time();
					if ($second < 0) {
						continue;
					}
					\Yii::$app->work->delay($second)->push(new UserRemindSendJob([
						'remindId' => $id,
						'time'     => $time,
					]));
				}
			}

		}

	}
