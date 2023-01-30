<?php
	/**
	 * Create by PhpStorm
	 * User: wangpan
	 * Date: 2020/2/5
	 * Time: 17:06
	 */

	namespace app\queue;

	use app\models\WorkCorp;
	use app\models\WorkExternalContact;
	use app\models\WorkTag;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;

	class SyncWorkExternalContactJob extends BaseObject implements JobInterface
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
			ini_set('memory_limit', '4096M');
			set_time_limit(0);

			try {
				//客户标签列表
				WorkTag::syncWorkTagExternal($this->corp->id);
				$this->corp->last_customer_tag_time = time();
				$this->corp->save();

				$run = true;
				while ($run) {
					$cacheKey = 'syncWorkDepJob_' . $this->corp->id;
					if (!empty(\Yii::$app->cache->get($cacheKey))) {
						usleep(500000);
						continue;
					}

					//获取客户列表
					WorkExternalContact::getExternalContactList($this->corp->id);

					$run = false;
				}

				//跟进统计补充数据
				$second = 14400;
				\Yii::$app->queue->delay($second)->push(new SyncClockJob([
					'isFollow' => 1,
					'corpId'   => $this->corp->id,
				]));
			} catch (\Exception $e) {
				\Yii::error($e->getMessage(), 'syncWorkExternalContactJob-error');
				return false;
			}
		}

	}