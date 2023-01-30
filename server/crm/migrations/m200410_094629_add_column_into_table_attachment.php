<?php

	use yii\db\Migration;

	/**
	 * Class m200410_094629_add_column_into_table_attachment
	 */
	class m200410_094629_add_column_into_table_attachment extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%attachment}}', 'search_num', 'int(11) UNSIGNED NULL DEFAULT 0 COMMENT \'搜索次数\' AFTER `sub_id`');
			$this->addColumn('{{%attachment}}', 'send_num', 'int(11) UNSIGNED NULL DEFAULT 0  COMMENT \'搜索次数\' AFTER `search_num`');
			$this->addColumn('{{%attachment}}', 'open_num', 'int(11) UNSIGNED NULL DEFAULT 0  COMMENT \'打开次数\' AFTER `send_num`');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200410_094629_add_column_into_table_attachment cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200410_094629_add_column_into_table_attachment cannot be reverted.\n";

			return false;
		}
		*/
	}
