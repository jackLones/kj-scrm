<?php

	use yii\db\Migration;

	/**
	 * Class m200108_022749_add_column_into_contact_way
	 */
	class m200108_022749_add_column_into_contact_way extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%work_contact_way}}', 'add_num', 'int(11) UNSIGNED NULL DEFAULT 0 COMMENT \'添加人数\' AFTER `qr_code`');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200108_022749_add_column_into_contact_way cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200108_022749_add_column_into_contact_way cannot be reverted.\n";

			return false;
		}
		*/
	}
