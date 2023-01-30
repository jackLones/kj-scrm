<?php

	use yii\db\Migration;

	/**
	 * Class m201111_012719_change_work_moment_setting_culoumn
	 */
	class m201111_012719_change_work_moment_setting_culoumn extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->renameColumn("{{%work_moment_setting}}", "remind", "is_context");
			$this->alterColumn("{{%work_moment_setting}}", "is_context", $this->tinyInteger(1)->defaultValue(0)->comment("员工是否允许发表内容0不允许1允许")->after("agent_id"));
			$this->addColumn("{{%work_moment_setting}}", "is_audit", $this->tinyInteger(1)->defaultValue(0)->comment("员工发表内容是否审核0不允许1允许")->after("is_context"));
			$this->createIndex("WORK_MOMENT_SETTING_IS_CONTEXT", "{{%work_moment_setting}}", "is_context");
			$this->createIndex("WORK_MOMENT_SETTING_IS_AUDIT", "{{%work_moment_setting}}", "is_audit");
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m201111_012719_change_work_moment_setting_culoumn cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m201111_012719_change_work_moment_setting_culoumn cannot be reverted.\n";

			return false;
		}
		*/
	}
