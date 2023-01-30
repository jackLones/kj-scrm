<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2019/10/14
	 * Time: 16:43
	 */

	namespace app\queue;

	use yii\base\BaseObject;
	use yii\queue\JobInterface;

	class LogJob extends BaseObject implements JobInterface
	{
		public $data;
		public $appid;

		public function execute ($queue)
		{
			\Yii::error($this->data, 'logJob');
		}
	}