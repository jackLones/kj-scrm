<?php

	use yii\db\Migration;

	/**
	 * Class m200119_021351_add_column_into_table_work_agent
	 */
	class m200119_021351_add_column_into_table_work_agent extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%work_corp_agent}}', 'secret', 'char(64) NULL COMMENT \'应用secret\' AFTER `agentid`');
			$this->addColumn('{{%work_corp_agent}}', 'agent_type', 'tinyint(2) UNSIGNED NULL DEFAULT 2 COMMENT \'应用类型：1、基础；2、自建；3、授权\' AFTER `secret`');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200119_021351_add_column_into_table_work_agent cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200119_021351_add_column_into_table_work_agent cannot be reverted.\n";

			return false;
		}
		*/
	}
