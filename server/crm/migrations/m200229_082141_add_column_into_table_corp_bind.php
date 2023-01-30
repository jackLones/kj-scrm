<?php

	use yii\db\Migration;

	/**
	 * Class m200229_082141_add_column_into_table_corp_bind
	 */
	class m200229_082141_add_column_into_table_corp_bind extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%work_corp_bind}}', 'book_status', 'tinyint(1) UNSIGNED NULL DEFAULT 0 COMMENT \'是否开启通讯录事件0：不开启；1：开启\' AFTER `book_access_token_expires`');
			$this->addColumn('{{%work_corp_bind}}', 'external_status', 'tinyint(1) UNSIGNED NULL DEFAULT 0 COMMENT \'是否开启外部联系人事件0：不开启；1：开启\' AFTER `external_access_token_expires`');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200229_082141_add_column_into_table_corp_bind cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200229_082141_add_column_into_table_corp_bind cannot be reverted.\n";

			return false;
		}
		*/
	}
