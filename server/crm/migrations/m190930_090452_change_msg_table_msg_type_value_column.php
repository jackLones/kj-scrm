<?php

	use yii\db\Migration;

	/**
	 * Class m190930_090452_change_msg_table_msg_type_value_column
	 */
	class m190930_090452_change_msg_table_msg_type_value_column extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->dropIndex('KEY_WX_MSG_TYPE_VALUE', '{{%wx_msg}}');

			$this->alterColumn('{{%wx_msg}}', 'msg_type_value', 'text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT \'消息类别\' AFTER `msg_type`');

			$this->createIndex('KEY_WX_MSG_TYPE_VALUE', '{{%wx_msg}}', 'msg_type_value(20)');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m190930_090452_change_msg_table_msg_type_value_column cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m190930_090452_change_msg_table_msg_type_value_column cannot be reverted.\n";

			return false;
		}
		*/
	}
