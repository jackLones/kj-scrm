<?php

	use yii\db\Migration;

	/**
	 * Class m200610_022634_change_table_work_chat
	 */
	class m200610_022634_change_table_work_chat extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createIndex('IDX_WORK_CHAT_CHAT_ID', '{{%work_chat}}', 'chat_id(8)');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200610_022634_change_table_work_chat cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200610_022634_change_table_work_chat cannot be reverted.\n";

			return false;
		}
		*/
	}
