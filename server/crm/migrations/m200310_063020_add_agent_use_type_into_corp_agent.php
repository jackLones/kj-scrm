<?php

	use yii\db\Migration;

	/**
	 * Class m200310_063020_add_agent_use_type_into_corp_agent
	 */
	class m200310_063020_add_agent_use_type_into_corp_agent extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%work_corp_agent}}', 'agent_use_type', 'tinyint(2) NULL DEFAULT 0 COMMENT \'应用用途：0、通用；1、侧边栏（后续在扩展）\' AFTER `agent_type`');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200310_063020_add_agent_use_type_into_corp_agent cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200310_063020_add_agent_use_type_into_corp_agent cannot be reverted.\n";

			return false;
		}
		*/
	}
