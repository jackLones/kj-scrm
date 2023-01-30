<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2021/05/10
	 * Time: 19:36
	 */

	namespace app\queue;

	use app\models\WorkFollowUser;
	use app\models\WorkUser;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;

	class GetFollowUserJob extends BaseObject implements JobInterface
	{
		public $corp_id;
		public $user_id;
		public $check = false;
		public $need_auth_store = false;
		public $open_sub_user = false;
		public $uid = 0;
		public $cache_key = 'getFollowUserJob';

		/**
		 * @param \yii\queue\Queue $queue
		 *
		 * @return false
		 */
		public function execute ($queue)
		{
			if (!$this->check) {
				try {
					$workUserId = WorkUser::getUserId($this->corp_id, $this->user_id);
					WorkFollowUser::setFollowUser($this->corp_id, $workUserId);
				} catch (\Exception $e) {
					\Yii::error($e->getMessage(), __CLASS__);

					return false;
				}
			} else {
				if ($this->need_auth_store || $this->open_sub_user) {
					$run = true;

					while ($run) {
						$hasDone             = true;
						$getFollowUserJobIds = [];
						if (!empty(\Yii::$app->cache->get($this->cache_key))) {
							$getFollowUserJobIds = \Yii::$app->cache->get($this->cache_key);
						}

						if (!empty($getFollowUserJobIds)) {
							foreach ($getFollowUserJobIds as $syncWorKUserJobId) {
								// 判断是否有未完成的 job
								if (!\Yii::$app->queue->isDone($syncWorKUserJobId)) {
									$hasDone = false;
								}
							}
						}

						if (!$hasDone) {
							usleep(500000);
							continue;
						}

						// 所有的job都完成后在做这个同步操作
						if ($this->need_auth_store) {
							\Yii::$app->queue->push(new AuthStoreUserJob([
								'storeAll' => 1,
								'corpId'   => $this->corp_id,
							]));
						}

						if ($this->open_sub_user) {
							\Yii::$app->queue->push(new OpenSubUserJob([
								'corp_id' => $this->corp_id,
								'uid'     => $this->uid,
							]));
						}

						\Yii::$app->cache->delete($this->cache_key);

						$run = false;
					}
				}
			}

			return true;
		}
	}