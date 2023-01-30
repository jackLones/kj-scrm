<?php

	use yii\db\Migration;

	/**
	 * Class m201124_082436_add_index_in_work_external_contact_follow_user_table
	 */
	class m201124_082436_add_index_in_work_external_contact_follow_user_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createIndex('KEY_WORK_EXTERNAL_CONTACT_FOLLOW_USER_DELTYPE', '{{%work_external_contact_follow_user}}', 'del_type');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			$this->dropIndex('KEY_WORK_EXTERNAL_CONTACT_FOLLOW_USER_DELTYPE', '{{%work_external_contact_follow_user}}');
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m201124_082436_add_index_in_work_external_contact_follow_user_table cannot be reverted.\n";

			return false;
		}
		*/
	}
