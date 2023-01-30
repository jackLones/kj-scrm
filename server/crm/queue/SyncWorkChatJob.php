<?php
	/**
	 * Create by PhpStorm
	 * User: fulu
	 * Date: 2020/6/29
	 */

	namespace app\queue;

	use app\models\WorkChat;
	use app\models\WorkCorp;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;

	class SyncWorkChatJob extends BaseObject implements JobInterface
	{
		/** @var WorkCorp */
		public $corp;

		/**
		 * @param \yii\queue\Queue $queue
		 *
		 * @return bool|mixed|void
		 *
		 * @throws \Throwable
		 */
		public function execute ($queue)
		{
			ini_set('memory_limit', '1024M');
			set_time_limit(0);

			try {
				$offSet = 0;
				$limit  = 500;

				while (true) {
					$res = WorkChat::getChatList($this->corp->id, $offSet, $limit);
					\Yii::error($res, 'workChatJob');

					if ($res['complete'] == true) {
						break;
					}else{
						$offSet += $limit;
						continue;
					}
				}
			} catch (\Exception $e) {
				return false;
			}
		}

	}