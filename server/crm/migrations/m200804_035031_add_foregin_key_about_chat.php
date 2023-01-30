<?php

	use yii\db\Migration;

	/**
	 * Class m200804_035031_add_foregin_key_about_chat
	 */
	class m200804_035031_add_foregin_key_about_chat extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addForeignKey('KEY_WORK_CHAT_CONTACT_WAY_CORPID', '{{%work_chat_contact_way}}', 'corp_id', '{{%work_corp}}', 'id');

			$this->addForeignKey('KEY_WORK_CHAT_REMIND_CORPID', '{{%work_chat_remind}}', 'corp_id', '{{%work_corp}}', 'id');
			$this->addForeignKey('KEY_WORK_CHAT_REMIND_AGENTID', '{{%work_chat_remind}}', 'agentid', '{{%work_corp_agent}}', 'id');

			$this->alterColumn('{{%work_chat_remind}}', 'keyword', $this->string(255)->null()->comment('关键词集合'));
			$this->createIndex('IDX_WORK_CHAT_REMIND', '{{%work_chat_remind}}', 'keyword(16)');

			$this->addForeignKey('KEY_WORK_CHAT_REMIND_SEND_CORPID', '{{%work_chat_remind_send}}', 'corp_id', '{{%work_corp}}', 'id');
			$this->addForeignKey('KEY_WORK_CHAT_REMIND_SEND_AUDITINFOID', '{{%work_chat_remind_send}}', 'audit_info_id', '{{%work_msg_audit_info}}', 'id');
			$this->addForeignKey('KEY_WORK_CHAT_REMIND_SEND_REMINDID', '{{%work_chat_remind_send}}', 'remind_id', '{{%work_chat_remind}}', 'id');

			$this->addForeignKey('KEY_WORK_CHAT_STATISTIC_CORPID', '{{%work_chat_statistic}}', 'corp_id', '{{%work_corp}}', 'id');
			$this->addForeignKey('KEY_WORK_CHAT_STATISTIC_OWNERID', '{{%work_chat_statistic}}', 'owner_id', '{{%work_user}}', 'id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			$this->dropForeignKey('KEY_WORK_CHAT_CONTACT_WAY_CORPID', '{{%work_chat_contact_way}}');

			$this->dropForeignKey('KEY_WORK_CHAT_REMIND_CORPID', '{{%work_chat_remind}}');
			$this->dropForeignKey('KEY_WORK_CHAT_REMIND_AGENTID', '{{%work_chat_remind}}');

			$this->dropIndex('IDX_WORK_CHAT_REMIND', '{{%work_chat_remind}}');
			$this->alterColumn('{{%work_chat_remind}}', 'keyword', $this->text()->comment('关键词集合'));

			$this->dropForeignKey('KEY_WORK_CHAT_REMIND_SEND_CORPID', '{{%work_chat_remind_send}}');
			$this->dropForeignKey('KEY_WORK_CHAT_REMIND_SEND_AUDITINFOID', '{{%work_chat_remind_send}}');
			$this->dropForeignKey('KEY_WORK_CHAT_REMIND_SEND_REMINDID', '{{%work_chat_remind_send}}');

			$this->dropForeignKey('KEY_WORK_CHAT_STATISTIC_CORPID', '{{%work_chat_statistic}}');
			$this->dropForeignKey('KEY_WORK_CHAT_STATISTIC_OWNERID', '{{%work_chat_statistic}}');
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200804_035031_add_foregin_key_about_chat cannot be reverted.\n";

			return false;
		}
		*/
	}
