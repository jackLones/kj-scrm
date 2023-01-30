<?php

	use yii\db\Migration;

	/**
	 * Class m200611_094544_add_forigenkey_into_table_work_chat_contact_way_group_table
	 */
	class m200611_094544_add_forigenkey_into_table_work_chat_contact_way_group_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addForeignKey("KEY_WORK_CHAT_CONTACT_WAY_GROUP_PARENTID", "{{%work_chat_contact_way_group}}", "parent_id", "{{%work_chat_contact_way_group}}", "id");
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			$this->dropForeignKey("KEY_WORK_CHAT_CONTACT_WAY_GROUP_PARENTID", "{{%work_chat_contact_way_group}}");
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200611_094544_add_forigenkey_into_table_work_chat_contact_way_group_table cannot be reverted.\n";

			return false;
		}
		*/
	}
