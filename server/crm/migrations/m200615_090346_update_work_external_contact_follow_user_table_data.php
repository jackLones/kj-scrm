<?php

	use yii\db\Migration;

	/**
	 * Class m200615_090346_update_work_external_contact_follow_user_table_data
	 */
	class m200615_090346_update_work_external_contact_follow_user_table_data extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->update('{{%work_external_contact_follow_user}}', ['add_way' => 1], ['<>', 'fission_id', 0]);
			$this->update('{{%work_external_contact_follow_user}}', ['add_way' => 1], ['<>', 'award_id', 0]);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200615_090346_update_work_external_contact_follow_user_table_data cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200615_090346_update_work_external_contact_follow_user_table_data cannot be reverted.\n";

			return false;
		}
		*/
	}
