<?php

	use yii\db\Migration;

	/**
	 * Class m201219_074916_change_work_user_key
	 */
	class m201219_074916_change_work_user_key_t extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			/**attachment_statistic*/
			$this->dropForeignKey("KEY_ATTACHMENT_STATISTIC_USERID", "{{%attachment_statistic}}");
			$this->addForeignKey("KEY_ATTACHMENT_STATISTIC_USERID", "{{%attachment_statistic}}", "user_id", "{{%work_user}}", "id", "CASCADE", "CASCADE");

			/**public_sea_contact_follow_user*/
			$this->dropForeignKey("KEY_PUBLIC_SEA_CONTACT_FOLLOW_USER_USERID", "{{%public_sea_contact_follow_user}}");
			$this->addForeignKey("KEY_PUBLIC_SEA_CONTACT_FOLLOW_USER_USERID", "{{%public_sea_contact_follow_user}}", "user_id", "{{%work_user}}", "id", "CASCADE", "CASCADE");

			/**public_sea_transfer_detail*/
			$this->dropForeignKey("KEY_PUBLIC_SEA_TRANSFER_DETAIL_HANDOVER_USERID", "{{%public_sea_transfer_detail}}");
			$this->addForeignKey("KEY_PUBLIC_SEA_TRANSFER_DETAIL_HANDOVER_USERID", "{{%public_sea_transfer_detail}}", "handover_userid", "{{%work_user}}", "id", "CASCADE", "CASCADE");

			/**red_pack_chat_send_rule*/
			$this->dropForeignKey("KEY_RED_PACK_CHAT_SEND_RULE_USER_ID", "{{%red_pack_chat_send_rule}}");
			$this->addForeignKey("KEY_RED_PACK_CHAT_SEND_RULE_USER_ID", "{{%red_pack_chat_send_rule}}", "user_id", "{{%work_user}}", "id", "CASCADE", "CASCADE");

			/**wait_project*/
			$this->dropForeignKey("KEY_WAIT_PROJECT_USER_ID", "{{%wait_project}}");
			$this->addForeignKey("KEY_WAIT_PROJECT_USER_ID", "{{%wait_project}}", "user_id", "{{%work_user}}", "id", "CASCADE", "CASCADE");

			/**work_chat*/
			$this->dropForeignKey("KEY_WORK_CHAT_OWNERID", "{{%work_chat}}");
			$this->addForeignKey("KEY_WORK_CHAT_OWNERID", "{{%work_chat}}", "owner_id", "{{%work_user}}", "id", "CASCADE", "CASCADE");

			/**work_chat_info*/
			$this->dropForeignKey("KEY_WORK_CHAT_info_work_userid", "{{%work_chat_info}}");
			$this->addForeignKey("KEY_WORK_CHAT_info_work_userid", "{{%work_chat_info}}", "user_id", "{{%work_user}}", "id", "CASCADE", "CASCADE");

			/**work_chat_statistic*/
			$this->dropForeignKey("KEY_WORK_CHAT_STATISTIC_OWNERID", "{{%work_chat_statistic}}");
			$this->addForeignKey("KEY_WORK_CHAT_STATISTIC_OWNERID", "{{%work_chat_statistic}}", "owner_id", "{{%work_user}}", "id", "CASCADE", "CASCADE");

			/**work_contact_way_line*/
			$this->dropForeignKey("KEY_WORK_CONTACT_WAY_LINE_USER_ID", "{{%work_contact_way_line}}");
			$this->addForeignKey("KEY_WORK_CONTACT_WAY_LINE_USER_ID", "{{%work_contact_way_line}}", "user_id", "{{%work_user}}", "id", "CASCADE", "CASCADE");

			/**work_contact_way_redpacket_send*/
			$this->dropForeignKey("KEY_WORK_CONTACT_WAY_REDPACKET_USER_ID", "{{%work_contact_way_redpacket_send}}");
			$this->addForeignKey("KEY_WORK_CONTACT_WAY_REDPACKET_USER_ID", "{{%work_contact_way_redpacket_send}}", "user_id", "{{%work_user}}", "id", "CASCADE", "CASCADE");

			/**work_contact_way_redpacket_user*/
			$this->dropForeignKey("KEY_WORK_CONTACT_WAY_REDPACKET_USER_USERID", "{{%work_contact_way_redpacket_user}}");
			$this->addForeignKey("KEY_WORK_CONTACT_WAY_REDPACKET_USER_USERID", "{{%work_contact_way_redpacket_user}}", "user_id", "{{%work_user}}", "id", "CASCADE", "CASCADE");

			/**work_contact_way_redpacket_user_limit*/
			$this->dropForeignKey("KEY_WORK_CONTACT_WAY_REDPACKET_USER_LIMIT_USER_ID", "{{%work_contact_way_redpacket_user_limit}}");
			$this->addForeignKey("KEY_WORK_CONTACT_WAY_REDPACKET_USER_LIMIT_USER_ID", "{{%work_contact_way_redpacket_user_limit}}", "user_id", "{{%work_user}}", "id", "CASCADE", "CASCADE");

			/**work_contact_way_user*/
			$this->dropForeignKey("KEY_WORK_CONTACT_WAY_USER_USERID", "{{%work_contact_way_user}}");
			$this->addForeignKey("KEY_WORK_CONTACT_WAY_USER_USERID", "{{%work_contact_way_user}}", "user_id", "{{%work_user}}", "id", "CASCADE", "CASCADE");

			/**work_contact_way_user_limit*/
			$this->dropForeignKey("KEY_WORK_CONTACT_WAY_USER_LIMIT_USER_ID", "{{%work_contact_way_user_limit}}");
			$this->addForeignKey("KEY_WORK_CONTACT_WAY_USER_LIMIT_USER_ID", "{{%work_contact_way_user_limit}}", "user_id", "{{%work_user}}", "id", "CASCADE", "CASCADE");

			/**work_dismiss_user_detail*/
			$this->dropForeignKey("KEY_WORK_DISMISS_USER_DETAIL_USER_ID", "{{%work_dismiss_user_detail}}");
			$this->addForeignKey("KEY_WORK_DISMISS_USER_DETAIL_USER_ID", "{{%work_dismiss_user_detail}}", "user_id", "{{%work_user}}", "id", "CASCADE", "CASCADE");

			/**work_external_contact_follow_statistic*/
			$this->dropForeignKey("KEY_EXTERNAL_CONTACT_FOLLOW_STATISTIC_USER_ID", "{{%work_external_contact_follow_statistic}}");
			$this->addForeignKey("KEY_EXTERNAL_CONTACT_FOLLOW_STATISTIC_USER_ID", "{{%work_external_contact_follow_statistic}}", "user_id", "{{%work_user}}", "id", "CASCADE", "CASCADE");

			/**work_external_contact_follow_user*/
			$this->dropForeignKey("KEY_WORK_EXTERNAL_CONTACT_FOLLOW_USER_USERID", "{{%work_external_contact_follow_user}}");
			$this->addForeignKey("KEY_WORK_EXTERNAL_CONTACT_FOLLOW_USER_USERID", "{{%work_external_contact_follow_user}}", "user_id", "{{%work_user}}", "id", "CASCADE", "CASCADE");

			/**work_external_contact_user_way_detail*/
			$this->dropForeignKey("KEY_WORK_TAG_CONTACT_USER_ID", "{{%work_external_contact_user_way_detail}}");
			$this->addForeignKey("KEY_WORK_TAG_CONTACT_USER_ID", "{{%work_external_contact_user_way_detail}}", "user_id", "{{%work_user}}", "id", "CASCADE", "CASCADE");

			/**work_follow_user*/
			$this->dropForeignKey("KEY_WORK_FOLLOW_USER_USERID", "{{%work_follow_user}}");
			$this->addForeignKey("KEY_WORK_FOLLOW_USER_USERID", "{{%work_follow_user}}", "user_id", "{{%work_user}}", "id", "CASCADE", "CASCADE");

			/**work_group_sending_redpacket_send*/
			$this->dropForeignKey("KEY_WORK_GROUP_SENDING_REDPACKET_SEND_USER_ID", "{{%work_group_sending_redpacket_send}}");
			$this->addForeignKey("KEY_WORK_GROUP_SENDING_REDPACKET_SEND_USER_ID", "{{%work_group_sending_redpacket_send}}", "user_id", "{{%work_user}}", "id", "CASCADE", "CASCADE");

			/**work_group_sending_user*/
			$this->dropForeignKey("KEY_WORK_GROUP_SENDING_USER_USER_ID", "{{%work_group_sending_user}}");
			$this->addForeignKey("KEY_WORK_GROUP_SENDING_USER_USER_ID", "{{%work_group_sending_user}}", "user_id", "{{%work_user}}", "id", "CASCADE", "CASCADE");

			/**work_moment_edit*/
			$this->dropForeignKey("WORK_MOMENT_EDIT_USER_ID", "{{%work_moment_edit}}");
			$this->addForeignKey("WORK_MOMENT_EDIT_USER_ID", "{{%work_moment_edit}}", "user_id", "{{%work_user}}", "id", "CASCADE", "CASCADE");

			/**work_moment_goods*/
			$this->dropForeignKey("pig_fk-work_moment_goods-user_id", "{{%work_moment_goods}}");
			$this->addForeignKey("pig_fk-work_moment_goods-user_id", "{{%work_moment_goods}}", "user_id", "{{%work_user}}", "id", "CASCADE", "CASCADE");

			/**work_moment_reply*/
			$this->dropForeignKey("pig_fk-work_moment_reply-user_id", "{{%work_moment_reply}}");
			$this->addForeignKey("pig_fk-work_moment_reply-user_id", "{{%work_moment_reply}}", "user_id", "{{%work_user}}", "id", "CASCADE", "CASCADE");

			/**work_moment_user_config*/
			$this->dropForeignKey("pig_fk-work_moment_user_config-user_id", "{{%work_moment_user_config}}");
			$this->addForeignKey("pig_fk-work_moment_user_config-user_id", "{{%work_moment_user_config}}", "user_id", "{{%work_user}}", "id", "CASCADE", "CASCADE");

			/**work_moment_user_config*/
			$this->dropForeignKey("pig_fk-work_moments-user_id", "{{%work_moments}}");
			$this->addForeignKey("pig_fk-work_moments-user_id", "{{%work_moments}}", "user_id", "{{%work_user}}", "id", "CASCADE", "CASCADE");

			/**work_moments_base*/
			$this->dropForeignKey("WORK_MOMENT_BASE_WORK_USER_ID", "{{%work_moments_base}}");
			$this->addForeignKey("WORK_MOMENT_BASE_WORK_USER_ID", "{{%work_moments_base}}", "user_id", "{{%work_user}}", "id", "CASCADE", "CASCADE");

			/**work_public_activity_fans_user*/
			$this->dropForeignKey("KEY_WORK_PUBLIC_ACTIVITY_USER_DETAIL_USER_ID", "{{%work_public_activity_fans_user}}");
			$this->addForeignKey("KEY_WORK_PUBLIC_ACTIVITY_USER_DETAIL_USER_ID", "{{%work_public_activity_fans_user}}", "user_id", "{{%work_user}}", "id", "CASCADE", "CASCADE");

			/**work_tag_group_statistic*/
			$this->dropForeignKey("KEY_WORK_TAG_GROUP_STATISTIC_USER_ID", "{{%work_tag_group_statistic}}");
			$this->addForeignKey("KEY_WORK_TAG_GROUP_STATISTIC_USER_ID", "{{%work_tag_group_statistic}}", "user_id", "{{%work_user}}", "id", "CASCADE", "CASCADE");

			/**work_tag_group_user_statistic*/
			$this->dropForeignKey("KEY_WORK_TAG_GROUP_USER_STATISTIC_USER_ID", "{{%work_tag_group_user_statistic}}");
			$this->addForeignKey("KEY_WORK_TAG_GROUP_USER_STATISTIC_USER_ID", "{{%work_tag_group_user_statistic}}", "user_id", "{{%work_user}}", "id", "CASCADE", "CASCADE");

			/**work_tag_user*/
			$this->dropForeignKey("KEY_WORK_TAG_USER_USERID", "{{%work_tag_user}}");
			$this->addForeignKey("KEY_WORK_TAG_USER_USERID", "{{%work_tag_user}}", "user_id", "{{%work_user}}", "id", "CASCADE", "CASCADE");

			/**work_user_author_relation*/
			$this->dropForeignKey("KEY_WORK_USER_AUTHOR_RELATION_USERID", "{{%work_user_author_relation}}");
			$this->addForeignKey("KEY_WORK_USER_AUTHOR_RELATION_USERID", "{{%work_user_author_relation}}", "user_id", "{{%work_user}}", "id", "CASCADE", "CASCADE");

			/**work_user_commission_remind*/
			$this->dropForeignKey("KEY_WORK_USER_COMMISSION_REMIND_USER_ID", "{{%work_user_commission_remind}}");
			$this->addForeignKey("KEY_WORK_USER_COMMISSION_REMIND_USER_ID", "{{%work_user_commission_remind}}", "user_id", "{{%work_user}}", "id", "CASCADE", "CASCADE");

			/**work_user_commission_remind_time*/
			$this->dropForeignKey("KEY_WORK_USER_COMMISSION_REMIND_TIME_REMIND_ID", "{{%work_user_commission_remind_time}}");
			$this->addForeignKey("KEY_WORK_USER_COMMISSION_REMIND_TIME_REMIND_ID", "{{%work_user_commission_remind_time}}", "remind_id", "{{%work_user_commission_remind}}", "id", "CASCADE", "CASCADE");

			/**work_user_del_follow_user*/
			$this->dropForeignKey("KEY_WORK_USER_DEL_FOLLOW_USER_USER_ID", "{{%work_user_del_follow_user}}");
			$this->addForeignKey("KEY_WORK_USER_DEL_FOLLOW_USER_USER_ID", "{{%work_user_del_follow_user}}", "user_id", "{{%work_user}}", "id", "CASCADE", "CASCADE");

			/**work_user_external_profile*/
			$this->dropForeignKey("KEY_WORK_USER_EXTERNAL_PROFILE_USERID", "{{%work_user_external_profile}}");
			$this->addForeignKey("KEY_WORK_USER_EXTERNAL_PROFILE_USERID", "{{%work_user_external_profile}}", "user_id", "{{%work_user}}", "id", "CASCADE", "CASCADE");

			/**work_user_tag_external*/
			$this->dropForeignKey("KEY_WORK_USER_TAG_EXTERNAL_USERID", "{{%work_user_tag_external}}");
			$this->addForeignKey("KEY_WORK_USER_TAG_EXTERNAL_USERID", "{{%work_user_tag_external}}", "user_id", "{{%work_user}}", "id", "CASCADE", "CASCADE");

			/**work_user_tag_rule*/
			$this->dropForeignKey("KEY_WORK_USER_TAG_RULE_USERID", "{{%work_user_tag_rule}}");
			$this->addForeignKey("KEY_WORK_USER_TAG_RULE_USERID", "{{%work_user_tag_rule}}", "user_id", "{{%work_user}}", "id", "CASCADE", "CASCADE");

			/**work_welcome*/
			$this->dropForeignKey("KEY_WORK_WELCOME_USERID", "{{%work_welcome}}");
			$this->addForeignKey("KEY_WORK_WELCOME_USERID", "{{%work_welcome}}", "user_id", "{{%work_user}}", "id", "CASCADE", "CASCADE");

			/**public_sea_tag*/
			$this->dropForeignKey("KEY_PUBLIC_SEA_TAG_FOLLOW_USER_ID", "{{%public_sea_tag}}");
			$this->addForeignKey("KEY_PUBLIC_SEA_TAG_FOLLOW_USER_ID", "{{%public_sea_tag}}", "follow_user_id", "{{%public_sea_contact_follow_user}}", "id", "CASCADE", "CASCADE");

			/**wait_project_remind*/
			$this->dropForeignKey("KEY_WAIT_PROJECT_REMIND_PROJECT_ID", "{{%wait_project_remind}}");
			$this->addForeignKey("KEY_WAIT_PROJECT_REMIND_PROJECT_ID", "{{%wait_project_remind}}", "project_id", "{{%wait_project}}", "id", "CASCADE", "CASCADE");

			/**wait_task*/
			$this->dropForeignKey("KEY_WAIT_TASK_PROJECT_ID", "{{%wait_task}}");
			$this->addForeignKey("KEY_WAIT_TASK_PROJECT_ID", "{{%wait_task}}", "project_id", "{{%wait_project}}", "id", "CASCADE", "CASCADE");

			/**wait_customer_task*/
			$this->dropForeignKey("KEY_WAIT_CUSTOMER_TASK_TASK_ID", "{{%wait_customer_task}}");
			$this->addForeignKey("KEY_WAIT_CUSTOMER_TASK_TASK_ID", "{{%wait_customer_task}}", "task_id", "{{%wait_task}}", "id", "CASCADE", "CASCADE");

			/**attachment_statistic*/
			$this->dropForeignKey("KEY_ATTACHMENT_STATISTIC_CHATID", "{{%attachment_statistic}}");
			$this->addForeignKey("KEY_ATTACHMENT_STATISTIC_CHATID", "{{%attachment_statistic}}", "chat_id", "{{%work_chat}}", "id", "CASCADE", "CASCADE");

			/**red_pack_chat_send_rule*/
			$this->dropForeignKey("KEY_RED_PACK_CHAT_SEND_RULE_CHAT_ID", "{{%red_pack_chat_send_rule}}");
			$this->addForeignKey("KEY_RED_PACK_CHAT_SEND_RULE_CHAT_ID", "{{%red_pack_chat_send_rule}}", "chat_id", "{{%work_chat}}", "id", "CASCADE", "CASCADE");

			/**work_chat_info*/
			$this->dropForeignKey("KEY_WORK_CHAT_info_chatid", "{{%work_chat_info}}");
			$this->addForeignKey("KEY_WORK_CHAT_info_chatid", "{{%work_chat_info}}", "chat_id", "{{%work_chat}}", "id", "CASCADE", "CASCADE");

			/**work_dismiss_user_detail*/
			$this->dropForeignKey("KEY_WORK_DISMISS_USER_DETAIL_CHAT_ID", "{{%work_dismiss_user_detail}}");
			$this->addForeignKey("KEY_WORK_DISMISS_USER_DETAIL_CHAT_ID", "{{%work_dismiss_user_detail}}", "chat_id", "{{%work_chat}}", "id", "CASCADE", "CASCADE");

			/**work_external_contact_user_way_detail*/
			$this->dropForeignKey("KEY_WORK_TAG_CONTACT_CHAT_ID", "{{%work_external_contact_user_way_detail}}");
			$this->addForeignKey("KEY_WORK_TAG_CONTACT_CHAT_ID", "{{%work_external_contact_user_way_detail}}", "chat_id", "{{%work_chat}}", "id", "CASCADE", "CASCADE");

			/**work_tag_chat*/
			$this->dropForeignKey("KEY_WORK_TAG_CHAT_CHATID", "{{%work_tag_chat}}");
			$this->addForeignKey("KEY_WORK_TAG_CHAT_CHATID", "{{%work_tag_chat}}", "chat_id", "{{%work_chat}}", "id", "CASCADE", "CASCADE");

			/**work_user_del_follow_user_detail*/
			$this->dropForeignKey("KEY_WORK_USER_DEL_FOLLOW_DETAIL_USER_USER_ID", "{{%work_user_del_follow_user_detail}}");
			$this->addForeignKey("KEY_WORK_USER_DEL_FOLLOW_DETAIL_USER_USER_ID", "{{%work_user_del_follow_user_detail}}", "user_id", "{{%work_user}}", "id", "CASCADE", "CASCADE");


		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m201219_074916_change_work_user_key cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m201219_074916_change_work_user_key cannot be reverted.\n";

			return false;
		}
		*/
	}
