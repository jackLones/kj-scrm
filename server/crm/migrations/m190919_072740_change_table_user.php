<?php

	use yii\db\Migration;

	/**
	 * Class m190919_072740_change_table_user
	 */
	class m190919_072740_change_table_user extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->alterColumn('{{%user}}', 'access_token_expire', 'int(11) UNSIGNED NULL DEFAULT NULL COMMENT \'对接验证字符串失效时间戳\' AFTER `access_token`');

			$this->dropIndex('KEY_USER_AUTHTOKEN', '{{%user}}');
			$this->dropColumn('{{%user}}', 'auth_token');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m190919_072740_change_table_user cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m190919_072740_change_table_user cannot be reverted.\n";

			return false;
		}
		*/
	}
