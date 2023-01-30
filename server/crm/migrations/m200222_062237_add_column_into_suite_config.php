<?php

	use yii\db\Migration;

	/**
	 * Class m200222_062237_add_column_into_suite_config
	 */
	class m200222_062237_add_column_into_suite_config extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%work_suite_config}}', 'name', 'char(64) NULL COMMENT \'应用名字\' AFTER `suite_id`');
			$this->addColumn('{{%work_suite_config}}', 'logo_url', 'varchar(255) NULL COMMENT \'应用方形头像\' AFTER `name`');
			$this->addColumn('{{%work_suite_config}}', 'description', 'text NULL COMMENT \'应用详情\' AFTER `logo_url`');
			$this->addColumn('{{%work_suite_config}}', 'redirect_domain', 'varchar(255) NULL COMMENT \'应用可信域名\' AFTER `description`');
			$this->addColumn('{{%work_suite_config}}', 'home_url', 'varchar(255) NULL COMMENT \'应用主页url\' AFTER `redirect_domain`');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200222_062237_add_column_into_suite_config cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200222_062237_add_column_into_suite_config cannot be reverted.\n";

			return false;
		}
		*/
	}
