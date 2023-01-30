<?php

	use yii\db\Migration;

	/**
	 * Class m201229_084748_add_cloumns_public_user_keys
	 */
	class m201229_084748_add_cloumns_public_user_keys extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createIndex("KEY_PUBLIC_SEA_CONTACT_FOLLOW_LAST_TIME","{{%public_sea_contact_follow_user}}","last_follow_time");
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m201229_084748_add_cloumns_public_user_keys cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m201229_084748_add_cloumns_public_user_keys cannot be reverted.\n";

			return false;
		}
		*/
	}
