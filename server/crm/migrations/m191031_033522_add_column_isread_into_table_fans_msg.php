<?php

	use yii\db\Migration;

	/**
	 * Class m191031_033522_add_cloumn_isread_into_table_fans_msg
	 */
	class m191031_033522_add_column_isread_into_table_fans_msg extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%fans_msg}}', 'isread', 'tinyint(1) UNSIGNED NULL DEFAULT 0 COMMENT \'是否已读，0：未读、1：已读\' AFTER `content`');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m191031_033522_add_cloumn_isread_into_table_fans_msg cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m191031_033522_add_cloumn_isread_into_table_fans_msg cannot be reverted.\n";

			return false;
		}
		*/
	}
