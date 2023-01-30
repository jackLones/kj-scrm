<?php

	namespace app\queue;

	use app\models\WorkMomentsBase;
	use app\models\WorkMomentSetting;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;

	class MomentsJob extends BaseObject implements JobInterface
	{
		public $momentsId;

		public function execute ($queue)
		{
			try {
				if (\Yii::$app->cache->exists("$this->momentsId" . "moment")) {
					return;
				}
				\Yii::$app->cache->set("$this->momentsId" . "moment", 1, 5);
				$base = WorkMomentsBase::findOne($this->momentsId);
				\Yii::error($base, '$info');
				if (empty($base)) {
					return;
				}
				if ($base->send_success == 1 || $base->status == 2) {
					return;
				}
				$setting = WorkMomentSetting::findOne(["corp_id" => $base->corp_id]);
				$info    = json_decode($base->info, true);
				WorkMomentsBase::setMomentContext($base, $setting->agent_id, $info, true);
                $userKey = WorkMomentsBase::getUserKey($base);
                if ($base->advanced_setting == 1 && !empty($userKey)) {
                    WorkMomentsBase::send($base, $setting->agent_id, $userKey, $info);
                }
			} catch (\Exception $e) {
				\Yii::error($e->getMessage(), "sym-moments");
				\Yii::error($e->getFile(), "sym-moments");
				\Yii::error($e->getLine(), "sym-moments");
			}
		}
	}