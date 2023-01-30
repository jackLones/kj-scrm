<?php

	use yii\db\Migration;

	/**
	 * Class m201105_015604_change_table_work_moments_base
	 */
	class m201105_015604_change_table_work_moments_base extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn("{{%work_moments_base}}", "user_id", $this->integer(11)->unsigned()->comment("创建员工id")->after("sub_id"));
			$this->addForeignKey("WORK_MOMENT_BASE_WORK_USER_ID", "{{%work_moments_base}}", "user_id", "{{%work_user}}", "id");
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m201105_015604_change_table_work_moments_base cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m201105_015604_change_table_work_moments_base cannot be reverted.\n";

			return false;
		}
		*/
	}
