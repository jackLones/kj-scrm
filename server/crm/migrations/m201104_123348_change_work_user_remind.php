<?php

	use yii\db\Migration;

	/**
	 * Class m201104_123348_change_work_user_remind
	 */
	class m201104_123348_change_work_user_remind extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createIndex("WORK_USER_REMIND_OPEN_STATUS", "{{%work_user_commission_remind}}", "open_status");
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m201104_123348_change_work_user_remind cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m201104_123348_change_work_user_remind cannot be reverted.\n";

			return false;
		}
		*/
	}
