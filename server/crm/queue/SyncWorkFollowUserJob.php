<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2020/1/16
	 * Time: 16:02
	 */

	namespace app\queue;

	use app\models\WorkCorp;
	use app\models\WorkFollowUser;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;

	class SyncWorkFollowUserJob extends BaseObject implements JobInterface
	{
		/** @var WorkCorp */
		public $corp;
		public $need_external = false;

		public function execute ($queue)
		{
			$run = true;

			while ($run) {
				$hasDone            = true;
				$cacheKey           = 'syncWorkUserJob' . $this->corp->id;
				$syncWorKUserJobIds = [];
				if (!empty(\Yii::$app->cache->get($cacheKey))) {
					$syncWorKUserJobIds = \Yii::$app->cache->get($cacheKey);
				}

				if (!empty($syncWorKUserJobIds)) {
					foreach ($syncWorKUserJobIds as $syncWorKUserJobId) {
						// 判断是否有未完成的 job
						if (!\Yii::$app->work->isDone($syncWorKUserJobId)) {
							$hasDone = false;
						}
					}
				}

				// 有未完成的 job 跳出本次 while 循环
				if (!$hasDone) {
					usleep(500000);
					continue;
				}

				\Yii::$app->cache->delete($cacheKey);

				\Yii::$app->cache->delete('syncWorkDepJob_' . $this->corp->id);//拉取成员完成后，删除缓存标记

				try {
					WorkFollowUser::getFollowUser($this->corp->id);

					if ($this->need_external) {
						\Yii::$app->work->push(new SyncWorkExternalContactJob([
							'corp' => $this->corp,
						]));
					}
				} catch (\Exception $e) {
					\Yii::error($e->getMessage(), 'SyncWorkFollowUserJob');
				}

				//企业标签列表、成员打上的标签
				\Yii::$app->work->push(new SyncWorkTagUserJob([
					'corp_id'  => $this->corp->id,
					'group_id' => '0',
				]));

				$run = false;
			}
		}
	}