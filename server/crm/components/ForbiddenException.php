<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2019/12/7
	 * Time: 13:31
	 */

	namespace app\components;

	/**
	 * Class ForbiddenException
	 * @package app\components
	 */
	class ForbiddenException extends CrmHttpException
	{
		const error_code = 4003;

		/**
		 * ForbiddenException constructor.
		 * @inheritDoc
		 *
		 * @param null $message
		 */
		public function __construct ($message = NULL)
		{
			parent::__construct(static::error_code, $message);
		}

		/**
		 * @return string
		 */
		public function getName ()
		{
			return "Operation/Request is forbidden.";
		}
	}