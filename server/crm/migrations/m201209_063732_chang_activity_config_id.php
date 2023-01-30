<?php

	use yii\db\Migration;

	/**
	 * Class m201209_063732_chang_activity_config_id
	 */
	class m201209_063732_chang_activity_config_id extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn("{{%work_public_activity}}", "config_id", $this->string(255)->comment("渠道活码id")->after("qc_url"));
			$this->addColumn("{{%work_public_activity}}", "config_del", $this->integer(1)->defaultValue(0)->comment("1已刪除0未刪除")->after("config_id"));
			$this->createIndex("SHORT_URL_ACTIVITY", "{{%work_public_activity_url}}", 'short_url(5)');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m201209_063732_chang_activity_config_id cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m201209_063732_chang_activity_config_id cannot be reverted.\n";

			return false;
		}
		*/
	}
