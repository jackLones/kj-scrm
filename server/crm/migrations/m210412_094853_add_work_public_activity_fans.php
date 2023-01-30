<?php

	use yii\db\Migration;

	/**
	 * Class m210412_094853_add_work_public_activity_fans
	 */
	class m210412_094853_add_work_public_activity_fans extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn("{{%work_public_activity_fans_user}}", "qc_url", $this->string(255)->comment("渠道活码url"));
			$this->addColumn("{{%work_public_activity_fans_user}}", "config_id", $this->string(255)->comment("渠道活码config"));
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m210412_094853_add_work_public_activity_fans cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{
	
		}
	
		public function down()
		{
			echo "m210412_094853_add_work_public_activity_fans cannot be reverted.\n";
	
			return false;
		}
		*/
	}
