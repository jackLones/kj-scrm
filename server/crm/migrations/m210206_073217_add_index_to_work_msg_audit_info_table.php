<?php

	use yii\db\Migration;

	/**
	 * Class m210206_073217_add_index_to_work_msg_audit_info_table
	 */
	class m210206_073217_add_index_to_work_msg_audit_info_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createIndex("IDX-WORK_MSG_AUDIT_INFO-ROOMID", "{{%work_msg_audit_info}}", 'roomid(8)');
			$this->createIndex("IDX-WORK_MSG_AUDIT_INFO-ROOMID_CHATID", "{{%work_msg_audit_info}}", ['roomid(8)', 'chat_id']);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			$this->dropIndex("IDX-WORK_MSG_AUDIT_INFO-ROOMID", "{{%work_msg_audit_info}}");
			$this->dropIndex("IDX-WORK_MSG_AUDIT_INFO-ROOMID_CHATID", "{{%work_msg_audit_info}}");
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m210206_073217_add_index_to_work_msg_audit_info_table cannot be reverted.\n";

			return false;
		}
		*/
	}
