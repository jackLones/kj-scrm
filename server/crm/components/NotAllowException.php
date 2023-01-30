<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2019-09-07
	 * Time: 09:49
	 */

	namespace app\components;

	class NotAllowException extends CrmHttpException
	{
		const error_code = 1002;

		public function __construct ($message = NULL)
		{
			parent::__construct(static::error_code, $message);
		}

		public function getName ()
		{
			return "Operation/Request is not allowed.";
		}
	}