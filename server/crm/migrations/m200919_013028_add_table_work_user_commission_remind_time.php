<?php

	use yii\db\Migration;

	/**
	 * Class m200919_013028_add_table_work_user_commission_remind_time
	 */
	class m200919_013028_add_table_work_user_commission_remind_time extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable("{{%work_user_commission_remind_time}}", [
				"id"          => $this->primaryKey(11)->unsigned(),
				"remind_id"   => $this->integer(11)->unsigned()->comment("员工提醒id"),
				"time"        => $this->integer(11)->comment("时间段"),
				"create_time" => $this->integer(11)
			]);
			$this->addForeignKey('KEY_WORK_USER_COMMISSION_REMIND_TIME_REMIND_ID', '{{%work_user_commission_remind_time}}', 'remind_id', '{{%work_user_commission_remind}}', 'id');

		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200919_013028_add_table_work_user_commission_remind_time cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200919_013028_add_table_work_user_commission_remind_time cannot be reverted.\n";

			return false;
		}
		*/
	}
