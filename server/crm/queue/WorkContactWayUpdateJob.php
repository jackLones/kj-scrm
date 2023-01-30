<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2021/01/12
	 * Time: 14:45
	 */

	namespace app\queue;

	use app\models\WorkContactWay;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;

	class WorkContactWayUpdateJob extends BaseObject implements JobInterface
	{
		public $contactWay;
		public $day;
		public $date;
		public $newTime;

		public function execute ($queue)
		{
			\Yii::error($this->contactWay['id'], 'way_id');
			$resultData = WorkContactWay::getDepartUser($this->contactWay, $this->day, $this->date, $this->newTime);
			$userId     = $resultData['userId'];
			$partyId    = $resultData['partyId'];

			\Yii::error($userId, '$userId-Hour');
			\Yii::error($partyId, '$partyId-Hour');

			//开启了员工每日添加上限
			$userId = WorkContactWay::getUserId($userId, $this->contactWay);

			//判断是否开启了分时段自动通过
			$verify = !(boolean) $this->contactWay['skip_verify'];
			$verify = WorkContactWay::getVerify($this->contactWay, $verify, time());

			\Yii::error($userId, '$userId-Hour1');
			\Yii::error($partyId, '$partyId-Hour1');
			\Yii::error($verify, '$verify1');

			if (!empty($userId) || !empty($partyId)) {
				$contactWayInfo = [
					'type'        => (int) $this->contactWay['type'],
					'scene'       => (int) $this->contactWay['scene'],
					'style'       => (int) $this->contactWay['style'],
					'remark'      => $this->contactWay['remark'],
					'skip_verify' => $verify,
					'state'       => $this->contactWay['state'],
					'user'        => $userId,
					'party'       => $partyId,
					'config_id'   => $this->contactWay['config_id'],
				];
				try {
					$result = WorkContactWay::editContact($this->contactWay['corp_id'], $contactWayInfo);
					\Yii::error($result, 'editContact-' . $this->contactWay['corp_id'] . '-' . $this->contactWay['id']);
				} catch (\Exception $e) {
					$message = $e->getMessage();
					\Yii::error($contactWayInfo, '$contactWayInfo');
					\Yii::error($message, '$message');
				}

			}
		}
	}