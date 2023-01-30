<?php

	use yii\db\Migration;

	/**
	 * Class m201224_015437_change_table_radar_link_statistic
	 */
	class m201224_015437_change_table_radar_link_statistic extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->dropForeignKey("{{%fk-radar_link_statistic-external_id}}", "{{%radar_link_statistic}}");
			$this->addForeignKey("{{%fk-radar_link_statistic-external_id}}", "{{%radar_link_statistic}}", "external_id", "{{%work_external_contact}}", "id", "CASCADE", "CASCADE");

			$this->dropForeignKey("{{%fk-radar_link_statistic-user_id}}", "{{%radar_link_statistic}}");
			$this->addForeignKey("{{%fk-radar_link_statistic-user_id}}", "{{%radar_link_statistic}}", "user_id", "{{%work_user}}", "id", "CASCADE", "CASCADE");

			$this->dropForeignKey("{{%fk-radar_link_statistic-chat_id}}", "{{%radar_link_statistic}}");
			$this->addForeignKey("{{%fk-radar_link_statistic-chat_id}}", "{{%radar_link_statistic}}", "chat_id", "{{%work_chat}}", "id", "CASCADE", "CASCADE");
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m201224_015437_change_table_radar_link_statistic cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m201224_015437_change_table_radar_link_statistic cannot be reverted.\n";

			return false;
		}
		*/
	}
