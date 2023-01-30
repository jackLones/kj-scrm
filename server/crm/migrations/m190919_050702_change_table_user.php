<?php

	use yii\db\Migration;

	/**
	 * Class m190919_050702_change_table_user
	 */
	class m190919_050702_change_table_user extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%user}}', 'auth_token', 'char(12) NULL COMMENT \'校验token\' AFTER `access_token_expire`');

			$this->createIndex('KEY_USER_AUTHTOKEN', '{{%user}}', 'auth_token');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m190919_050702_change_table_user cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m190919_050702_change_table_user cannot be reverted.\n";

			return false;
		}
		*/
	}
