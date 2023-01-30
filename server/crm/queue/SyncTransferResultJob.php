<?php
	/**
	 * Create by PhpStorm
	 * User: wangpan
	 * Date: 2020/8/26
	 * Time: 9:30
	 */

	namespace app\queue;

	use app\models\WorkDismissUserDetail;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;

	class SyncTransferResultJob extends BaseObject implements JobInterface
	{

		public $corpId;

		public function execute ($queue)
		{
			try {
				\Yii::error($this->corpId, 'corpId');
				WorkDismissUserDetail::syncData($this->corpId);
			} catch (\Exception $e) {
				return false;
			}
		}
	}