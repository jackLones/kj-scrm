<?php

	use yii\db\Migration;

	/**
	 * Class m200522_094925_add_chat_id_into_attachment_statistic
	 */
	class m200522_094925_add_chat_id_into_attachment_statistic extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn("{{%attachment_statistic}}", 'chat_id', 'int(11) UNSIGNED NULL COMMENT \'客户群ID\' AFTER `external_id`');

			$this->addForeignKey("KEY_ATTACHMENT_STATISTIC_CHATID", '{{%attachment_statistic}}', 'chat_id', '{{%work_chat}}', 'id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200522_094925_add_chat_id_into_attachment_statistic cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200522_094925_add_chat_id_into_attachment_statistic cannot be reverted.\n";

			return false;
		}
		*/
	}
