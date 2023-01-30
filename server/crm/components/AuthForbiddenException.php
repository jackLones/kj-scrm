<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2020/08/20
	 * Time: 14:47
	 */

	namespace app\components;

	/**
	 * Class AuthForbiddenException
	 * @package app\components
	 */
	class AuthForbiddenException extends CrmHttpException
	{
		const error_code = 14003;

		/**
		 * AuthForbiddenException constructor.
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
			return "Oauth is forbidden.";
		}
	}