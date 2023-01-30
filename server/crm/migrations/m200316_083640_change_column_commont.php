<?php

	use yii\db\Migration;

	/**
	 * Class m200316_083640_change_column_commont
	 */
	class m200316_083640_change_column_commont extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->alterColumn('{{%work_corp_agent}}', 'agent_type', 'tinyint(2) UNSIGNED NULL DEFAULT 2 COMMENT \'应用类型：1、基础；2、自建；3、授权；4、小程序\' AFTER `access_token_expires`');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200316_083640_change_column_commont cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200316_083640_change_column_commont cannot be reverted.\n";

			return false;
		}
		*/
	}
