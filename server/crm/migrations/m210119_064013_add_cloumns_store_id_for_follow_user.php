<?php

	use yii\db\Migration;

	/**
	 * Class m210119_064013_add_cloumns_store_id_for_follow_user
	 */
	class m210119_064013_add_cloumns_store_id_for_follow_user extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn("{{%work_external_contact_follow_user}}", "store_id", $this->integer(11)->unsigned()->comment("门店id"));
			$this->addForeignKey("WORK_EXTERNAL_CONTACT_FOLLOW_USER_STORE_ID", "{{%work_external_contact_follow_user}}", "store_id", "{{%auth_store}}", "id", "SET NULL");
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m210119_064013_add_cloumns_store_id_for_follow_user cannot be reverted.\n";
			$this->dropColumn("{{%work_external_contact_follow_user}}", "store_id");
			$this->dropForeignKey("WORK_EXTERNAL_CONTACT_FOLLOW_USER_STORE_ID", "{{%work_external_contact_follow_user}}");

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m210119_064013_add_cloumns_store_id_for_follow_user cannot be reverted.\n";

			return false;
		}
		*/
	}
