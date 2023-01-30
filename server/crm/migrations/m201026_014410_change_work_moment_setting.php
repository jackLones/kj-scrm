<?php

	use yii\db\Migration;

	/**
	 * Class m201026_014410_change_work_moment_setting
	 */
	class m201026_014410_change_work_moment_setting extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn("{{%work_moment_setting}}", "remind", $this->integer(1)->defaultValue(0)->comment("行为提醒0不提醒1提醒"));
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m201026_014410_change_work_moment_setting cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m201026_014410_change_work_moment_setting cannot be reverted.\n";

			return false;
		}
		*/
	}
