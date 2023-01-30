<?php

	use yii\db\Migration;

	/**
	 * Class m191121_032557_change_table_interact_reply
	 */
	class m191121_032557_change_table_interact_reply extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%interact_reply}}', 'close_time', 'timestamp COMMENT \'关闭时间\' AFTER `push_num`');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m191121_032557_change_table_interact_reply cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m191121_032557_change_table_interact_reply cannot be reverted.\n";

			return false;
		}
		*/
	}
