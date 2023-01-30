<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2020/2/10
	 * Time: 13:29
	 */

	namespace app\queue;

	use app\models\WorkExternalContact;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;

	/**
	 * Class WorkExternalContactGetJob
	 * @package app\queue
	 */
	class WorkExternalContactGetJob extends BaseObject implements JobInterface
	{
		public $corpid;
		public $external_userid;

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
				WorkExternalContact::getUserSuite($this->corpid, $this->external_userid);
			} catch (\Exception $e) {
				\Yii::error($e->getMessage(), 'WorkExternalContactGetJob');

				return false;
			}
		}
	}