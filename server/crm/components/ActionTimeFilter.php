<?php
	/**
	 * Created by PhpStorm.
	 * User: Dove Chen
	 * Date: 19-09-11
	 * Time: 14:19
	 */

	namespace app\components;

	use yii;
	use yii\base\ActionFilter;

	class ActionTimeFilter extends ActionFilter
	{
		private $_startTime;

		public function beforeAction ($action)
		{
			$this->_startTime = microtime(true);

			return parent::beforeAction($action);
		}

		public function afterAction ($action, $result)
		{
			$time = microtime(true) - $this->_startTime;
			Yii::info("Action '{$action->uniqueId}' spent $time second.");

			return parent::afterAction($action, $result);
		}
	}