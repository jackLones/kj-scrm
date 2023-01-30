<?php

	use yii\db\Migration;

	/**
	 * Class m191121_033358_change_table_interact_reply
	 */
	class m191121_033358_change_table_interact_reply extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->alterColumn('{{%interact_reply}}', 'close_time', 'timestamp NULL COMMENT \'关闭时间\' AFTER `push_num`');
			$this->alterColumn('{{%interact_reply}}', 'start_time', 'timestamp NOT NULL DEFAULT \'0000-00-00 00:00:00\' COMMENT \'开始时间\' AFTER `reply_type`');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m191121_033358_change_table_interact_reply cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m191121_033358_change_table_interact_reply cannot be reverted.\n";

			return false;
		}
		*/
	}
