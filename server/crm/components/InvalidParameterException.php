<?php
	/**
	 * Created by PhpStorm.
	 * User: Dove Chen
	 * Date: 19-09-07
	 * Time:  上午09:03
	 */

	namespace app\components;

	class InvalidParameterException extends CrmHttpException
	{
		const error_code = 1001;

		public function __construct ($message = NULL, $code = InvalidParameterException::error_code)
		{
			parent::__construct($code, $message);
		}

		public function getName ()
		{
			return "Invalid parameters";
		}

	}