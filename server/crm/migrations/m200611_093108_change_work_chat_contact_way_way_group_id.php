<?php

	use yii\db\Migration;

	/**
	 * Class m200611_093108_change_work_chat_contact_way_way_group_id
	 */
	class m200611_093108_change_work_chat_contact_way_way_group_id extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->dropForeignKey("KEY_WAY_GROUP_ID", "{{%work_chat_contact_way}}");

			$this->addForeignKey("KEY_WORK_CHAT_CONTACT_WAY_WAYGROUPID", "{{%work_chat_contact_way}}", "way_group_id", "{{%work_chat_contact_way_group}}", "id");
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			$this->dropForeignKey("KEY_WORK_CHAT_CONTACT_WAY_WAYGROUPID", "{{%work_chat_contact_way}}");

			$this->addForeignKey("KEY_WAY_GROUP_ID", "{{%work_chat_contact_way}}", "way_group_id", "{{%work_chat_contact_way_group}}", "uid");
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200611_093108_change_work_chat_contact_way_way_group_id cannot be reverted.\n";

			return false;
		}
		*/
	}
