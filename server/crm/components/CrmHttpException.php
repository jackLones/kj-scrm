<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2019-09-07
	 * Time: 09:03
	 */

	namespace app\components;

	use yii\web\HttpException;

	class CrmHttpException extends HttpException
	{
		public $errorCode;

		public function __construct ($code = 0, $message = NULL, \Exception $previous = NULL)
		{
			$this->errorCode = $code;
			parent::__construct(200, $message, $code, $previous);
		}

		public function getName ()
		{
			return "Crm exception in Crm service.";
		}
	}