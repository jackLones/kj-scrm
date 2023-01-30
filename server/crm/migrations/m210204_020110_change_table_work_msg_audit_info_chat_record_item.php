<?php

	use yii\db\Migration;

	/**
	 * Class m210204_020110_change_table_work_msg_audit_info_chat_record_item
	 */
	class m210204_020110_change_table_work_msg_audit_info_chat_record_item extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->dropForeignKey('pig_fk-work_msg_audit_info_chatrecord_item-mixed_id', '{{%work_msg_audit_info_chatrecord_item}}');
			$this->dropIndex('pig_idx-work_msg_audit_info_chatrecord_item-mixed_id', '{{%work_msg_audit_info_chatrecord_item}}');
			$this->alterColumn('{{%work_msg_audit_info_chatrecord_item}}', 'mixed_id', 'varchar(250) DEFAULT NULL COMMENT \'混合消息ID\'');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m210204_020110_change_table_work_msg_audit_info_chat_record_item cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m210204_020110_change_table_work_msg_audit_info_chat_record_item cannot be reverted.\n";

			return false;
		}
		*/
	}
