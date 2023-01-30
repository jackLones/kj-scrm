<?php

	use yii\db\Migration;

	/**
	 * Class m200104_075732_change_table_welcome_user_department
	 */
	class m200104_075732_change_table_welcome_user_department extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->dropForeignKey('KEY_WORK_WELCOME_USERID', '{{%work_welcome}}');
			$this->dropForeignKey('KEY_WORK_WELCOME_DEPARTMENTID', '{{%work_welcome}}');

			$this->addForeignKey('KEY_WORK_WELCOME_USERID', '{{%work_welcome}}', 'user_id', '{{%work_user}}', 'id');
			$this->addForeignKey('KEY_WORK_WELCOME_DEPARTMENTID', '{{%work_welcome}}', 'department_id', '{{%work_department}}', 'id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200104_075732_change_table_welcome_user_department cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200104_075732_change_table_welcome_user_department cannot be reverted.\n";

			return false;
		}
		*/
	}
