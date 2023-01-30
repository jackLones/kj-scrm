<?php

	use yii\db\Migration;

	/**
	 * Class m201117_152040_change_table_remind_time
	 */
	class m201117_152040_change_table_remind_time extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->alterColumn("{{%work_user_commission_remind_time}}", "time", $this->string(60)->comment("时间段"));
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m201117_152040_change_table_remind_time cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m201117_152040_change_table_remind_time cannot be reverted.\n";

			return false;
		}
		*/
	}
