<?php

	use yii\db\Migration;

	/**
	 * Class m201105_082958_change_table_work_moments_audit_base_id
	 */
	class m201105_082958_change_table_work_moments_audit_base_id extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn("{{%work_moments_audit}}", "base_id", $this->integer(11)->unsigned()->comment("base_id"));
			$this->addForeignKey("WORK_MOMENT_AUDIT_BASE_ID", "{{%work_moments_audit}}", "base_id", "{{%work_moments_base}}", "id");
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m201105_082958_change_table_work_moments_audit_base_id cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m201105_082958_change_table_work_moments_audit_base_id cannot be reverted.\n";

			return false;
		}
		*/
	}
