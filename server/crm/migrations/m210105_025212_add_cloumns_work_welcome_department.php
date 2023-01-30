<?php

	use yii\db\Migration;

	/**
	 * Class m210105_025212_add_cloumns_work_welcome_department
	 */
	class m210105_025212_add_cloumns_work_welcome_department extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn("{{%work_welcome}}", "department", $this->string()->comment("部门id"));
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m210105_025212_add_cloumns_work_welcome_department cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m210105_025212_add_cloumns_work_welcome_department cannot be reverted.\n";

			return false;
		}
		*/
	}
