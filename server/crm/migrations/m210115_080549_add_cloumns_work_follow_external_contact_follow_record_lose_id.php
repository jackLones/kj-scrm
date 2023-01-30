<?php

	use yii\db\Migration;

	/**
	 * Class m210115_080549_add_cloumns_work_follow_external_contact_follow_record_lose_id
	 */
	class m210115_080549_add_cloumns_work_follow_external_contact_follow_record_lose_id extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn("{{%work_external_contact_follow_record}}", "lose_id", $this->integer(11)->unsigned()->comment("输单原因id"));
			$this->addColumn("{{%public_sea_contact_follow_record}}", "lose_id", $this->integer(11)->unsigned()->comment("输单原因id"));
			$this->addForeignKey("WORK_EXTERNAL_CONTACT_FOLLOW_RECORD_LOSER_ID", "{{%work_external_contact_follow_record}}", "lose_id", "{{%follow_lose_msg}}", "id", "SET NULL");
			$this->addForeignKey("PUBLIC_SEA_CONTACT_FOLLOW_RECORD", "{{%public_sea_contact_follow_record}}", "lose_id", "{{%follow_lose_msg}}", "id", "SET NULL");
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m210115_080549_add_cloumns_work_follow_external_contact_follow_record_lose_id cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m210115_080549_add_cloumns_work_follow_external_contact_follow_record_lose_id cannot be reverted.\n";

			return false;
		}
		*/
	}
