<?php

	use yii\db\Migration;

	/**
	 * Class m201109_054859_change_work_moment_base_title_column
	 */
	class m201109_054859_change_work_moment_base_title_column extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->alterColumn("{{%work_moments_base}}", "title", $this->string(255)->comment("标题"));
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m201109_054859_change_work_moment_base_title_column cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m201109_054859_change_work_moment_base_title_column cannot be reverted.\n";

			return false;
		}
		*/
	}
