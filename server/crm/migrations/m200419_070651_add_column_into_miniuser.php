<?php

	use yii\db\Migration;

	/**
	 * Class m200419_070651_add_column_into_miniuser
	 */
	class m200419_070651_add_column_into_miniuser extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%mini_user}}', 'remark', 'varchar(255) DEFAULT NULL COMMENT \'小程序用户备注\' AFTER `openid`');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200419_070651_add_column_into_miniuser cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200419_070651_add_column_into_miniuser cannot be reverted.\n";

			return false;
		}
		*/
	}
