<?php

	namespace app\models;

	use Yii;
	use yii\base\Model;

	class AdminLoginForm extends Model
	{
		public $account;
		public $password;
		public $rememberMe = true;

		private $_adminUser = false;

		/**
		 * @return array the validation rules.
		 */
		public function rules ()
		{
			return [
				// username and password are both required
				[['account', 'password'], 'required'],
				// rememberMe must be a boolean value
				['rememberMe', 'boolean'],
				// password is validated by validatePassword()
				['password', 'validatePassword'],
			];
		}

		/**
		 * Validates the password.
		 * This method serves as the inline validation for password.
		 *
		 * @param string $attribute the attribute currently being validated
		 * @param array  $params    the additional name-value pairs given in the rule
		 */
		public function validatePassword ($attribute, $params)
		{
			if (!$this->hasErrors()) {
				$adminUser = $this->getUser();

				if (!$adminUser || !$adminUser->validatePassword($this->password)) {
					$this->addError($attribute, 'Incorrect username or password.');
				}
			}
		}

		/**
		 * Logs in a user using the provided username and password.
		 * @return bool whether the user is logged in successfully
		 */
		public function login ()
		{
			if ($this->validate()) {
				return Yii::$app->adminUser->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
			}

			return false;
		}

		/**
		 * Finds user by [[username]]
		 *
		 * @return User|null
		 */
		public function getUser ()
		{
			if ($this->_adminUser === false) {
				$this->_adminUser = AdminUser::findIdentityByIdentifier($this->account);
			}

			return $this->_adminUser;
		}
	}