<?php
	/**
	 * Create by PhpStorm
	 * User: fulu
	 * Date: 2020/12/14
	 */

	namespace app\queue;

	use app\models\PublicSeaCustomer;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;

	class SyncWorkImportPsCustomerCreateJob extends BaseObject implements JobInterface
	{
		public $import;

		/**
		 * @param \yii\queue\Queue $queue
		 *
		 * @return bool|mixed|void
		 *
		 * @throws \Throwable
		 */
		public function execute ($queue)
		{
			ini_set('memory_limit', '2048M');
			set_time_limit(0);

			try {
				PublicSeaCustomer::create($this->import);
			} catch (\Exception $e) {
				return false;
			}
		}

	}