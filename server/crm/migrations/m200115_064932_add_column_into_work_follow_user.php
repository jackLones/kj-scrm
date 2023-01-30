<?php

	use yii\db\Migration;

	/**
	 * Class m200115_064932_add_column_into_work_follow_user
	 */
	class m200115_064932_add_column_into_work_follow_user extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%work_follow_user}}', 'status', 'tinyint(2) UNSIGNED NULL DEFAULT 1 COMMENT \'0：移除；1：可用\' AFTER `user_id`');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200115_064932_add_column_into_work_follow_user cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200115_064932_add_column_into_work_follow_user cannot be reverted.\n";

			return false;
		}
		*/
	}
