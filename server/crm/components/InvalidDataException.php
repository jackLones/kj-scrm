<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2019/9/16
	 * Time: 19:23
	 */

	namespace app\components;

	class InvalidDataException extends CrmHttpException
	{
		const error_code = 1003;

		public function __construct ($message = NULL)
		{
			parent::__construct(static::error_code, $message);
		}

		public function getName ()
		{
			return "Invalid parameters";
		}
	}