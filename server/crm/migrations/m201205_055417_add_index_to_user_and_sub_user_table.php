<?php

	use yii\db\Migration;

	/**
	 * Class m201205_055417_add_index_to_user_and_sub_user_table
	 */
	class m201205_055417_add_index_to_user_and_sub_user_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createIndex('KEY_USER_ACCESSTOKEN', '{{%user}}', 'access_token(6)');
			$this->createIndex('KEY_SUB_USER_ACCESSTOKEN', '{{%sub_user}}', 'access_token(6)');
			$this->createIndex('KEY_SUB_USER_ACCESSTOKEN_STATUS', '{{%sub_user}}', ['access_token(6)', 'status']);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			$this->dropIndex('KEY_SUB_USER_ACCESSTOKEN_STATUS', '{{%sub_user}}');
			$this->dropIndex('KEY_SUB_USER_ACCESSTOKEN', '{{%sub_user}}');
			$this->dropIndex('KEY_USER_ACCESSTOKEN', '{{%user}}');
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m201205_055417_add_index_to_user_and_sub_user_table cannot be reverted.\n";

			return false;
		}
		*/
	}
