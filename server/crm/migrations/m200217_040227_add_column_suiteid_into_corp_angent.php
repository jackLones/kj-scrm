<?php

	use yii\db\Migration;

	/**
	 * Class m200217_040227_add_column_suiteid_into_corp_angent
	 */
	class m200217_040227_add_column_suiteid_into_corp_angent extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%work_corp_agent}}', 'suite_id', 'int(11) UNSIGNED NULL COMMENT \'授权平台ID\' AFTER `agentid`');

			$this->addForeignKey('KEY_WORK_CORP_AGENT_SUITEID', '{{%work_corp_agent}}', 'suite_id', '{{%work_suite_config}}', 'id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200217_040227_add_column_suiteid_into_corp_angent cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200217_040227_add_column_suiteid_into_corp_angent cannot be reverted.\n";

			return false;
		}
		*/
	}
