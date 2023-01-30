<?php

	use yii\db\Migration;

	/**
	 * Class m200217_043212_delete_column_from_corp
	 */
	class m200217_043212_delete_column_from_corp extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->dropForeignKey('KEY_WORK_CORP_SUITE_ID', '{{%work_corp}}');
			$this->dropIndex('KEY_WORK_CORP_SUITE_ID', '{{%work_corp}}');

			$this->dropColumn('{{%work_corp}}', 'suite_id');
			$this->dropColumn('{{%work_corp}}', 'access_token');
			$this->dropColumn('{{%work_corp}}', 'access_token_expires');
			$this->dropColumn('{{%work_corp}}', 'permanent_code');
			$this->dropColumn('{{%work_corp}}', 'auth_user_info');
			$this->dropColumn('{{%work_corp}}', 'dealer_corp_info');
			$this->dropColumn('{{%work_corp}}', 'auth_type');
			$this->dropColumn('{{%work_corp}}', 'sync_user_time');
			$this->dropColumn('{{%work_corp}}', 'last_tag_time');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200217_043212_delete_column_from_corp cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200217_043212_delete_column_from_corp cannot be reverted.\n";

			return false;
		}
		*/
	}
