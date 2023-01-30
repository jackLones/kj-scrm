<?php

	use yii\db\Migration;

	/**
	 * Class m200210_094214_change_column_work_external_user
	 */
	class m200210_094214_change_column_work_external_user extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->alterColumn('{{%work_external_contact}}', 'name', 'varchar(255) NULL DEFAULT NULL COMMENT \'外部联系人的姓名或别名\' AFTER `external_userid`');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200210_094214_change_column_work_external_user cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200210_094214_change_column_work_external_user cannot be reverted.\n";

			return false;
		}
		*/
	}
