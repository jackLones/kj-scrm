<?php

	namespace app\queue;

	use app\models\WorkUserCommissionRemind;
	use app\models\WorkUserCommissionRemindTime;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;
	use yii\queue\Queue;

	class UserRemindSendJob extends BaseObject implements JobInterface
	{
		public $remindId;
		public $time;
		public function execute ($queue)
		{
			\Yii::error('UserRemindSendJob-line','UserRemindSendJob-line');
			try {
				$RemindTime = WorkUserCommissionRemindTime::find()->where(["remind_id"=>$this->remindId,"time"=>$this->time])->exists();
				if($RemindTime){
					WorkUserCommissionRemind::sendMessage($this->remindId,$this->time);
				}
			}catch (\Exception $e){
				\Yii::error($e->getLine(),"UserRemindSendJob-line");
				\Yii::error($e->getMessage(),"UserRemindSendJob-message");
			}
		}
	}