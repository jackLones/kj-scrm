<?php

	use yii\db\Migration;

	/**
	 * Class m200731_062205_modify_start_time_and_end_time_columns_from_interact_reply_table
	 */
	class m200731_062205_modify_start_time_and_end_time_columns_from_interact_reply_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->alterColumn('{{%interact_reply}}', 'start_time', $this->timestamp()->null()->defaultValue('0000-00-00 00:00:00')->comment('开始时间')->after('reply_type'));
			$this->alterColumn('{{%interact_reply}}', 'end_time', $this->timestamp()->null()->defaultValue('0000-00-00 00:00:00')->comment('结束时间')->after('start_time'));
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200731_062205_modify_start_time_and_end_time_columns_from_interact_reply_table cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200731_062205_modify_start_time_and_end_time_columns_from_interact_reply_table cannot be reverted.\n";

			return false;
		}
		*/
	}
