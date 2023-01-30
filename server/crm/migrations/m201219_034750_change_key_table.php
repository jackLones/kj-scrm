<?php

	use yii\db\Migration;

	/**
	 * Class m201219_034750_change_key
	 */
	class m201219_034750_change_key_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			/**attachment_statistic**/
			$this->dropForeignKey("KEY_ATTACHMENT_STATISTIC_EXTERNALID", "{{%attachment_statistic}}");
			$this->addForeignKey("KEY_ATTACHMENT_STATISTIC_EXTERNALID", "{{%attachment_statistic}}", "external_id", "{{%work_external_contact}}", "id", "CASCADE", "CASCADE");

			/**fans**/
			$this->dropForeignKey("KEY_FANS_EXTERNAL_USERID", "{{%fans}}");
			$this->addForeignKey("KEY_FANS_EXTERNAL_USERID", "{{%fans}}", "external_userid", "{{%work_external_contact}}", "id", "SET NULL");

			/**public_sea_transfer_detail**/
			$this->dropForeignKey("KEY_PUBLIC_SEA_TRANSFER_DETAIL_EXTERNAL_USERID", "{{%public_sea_transfer_detail}}");
			$this->addForeignKey("KEY_PUBLIC_SEA_TRANSFER_DETAIL_EXTERNAL_USERID", "{{%public_sea_transfer_detail}}", "external_userid", "{{%work_external_contact}}", "id", "CASCADE", "CASCADE");

			/**wait_customer_task**/
			$this->dropForeignKey("KEY_WAIT_CUSTOMER_TASK_EXTERNAL_USERID", "{{%wait_customer_task}}");
			$this->addForeignKey("KEY_WAIT_CUSTOMER_TASK_EXTERNAL_USERID", "{{%wait_customer_task}}", "external_userid", "{{%work_external_contact}}", "id", "CASCADE", "CASCADE");

			/**work_chat_info**/
			$this->dropForeignKey("KEY_WORK_CHAT_info_externalid", "{{%work_chat_info}}");
			$this->addForeignKey("KEY_WORK_CHAT_info_externalid", "{{%work_chat_info}}", "external_id", "{{%work_external_contact}}", "id", "CASCADE", "CASCADE");

			/**work_contact_way_line**/
			$this->dropForeignKey("KEY_WORK_CONTACT_WAY_LINE_EXTERNAL_USERID", "{{%work_contact_way_line}}");
			$this->addForeignKey("KEY_WORK_CONTACT_WAY_LINE_EXTERNAL_USERID", "{{%work_contact_way_line}}", "external_userid", "{{%work_external_contact}}", "id", "CASCADE", "CASCADE");

			/**work_contact_way_redpacket_send**/
			$this->dropForeignKey("KEY_WORK_CONTACT_WAY_REDPACKET_EXTERNAL_USERID", "{{%work_contact_way_redpacket_send}}");
			$this->addForeignKey("KEY_WORK_CONTACT_WAY_REDPACKET_EXTERNAL_USERID", "{{%work_contact_way_redpacket_send}}", "external_userid", "{{%work_external_contact}}", "id", "CASCADE", "CASCADE");

			/**work_dismiss_user_detail**/
			$this->dropForeignKey("KEY_WORK_DISMISS_USER_DETAIL_EXTERNAL_USERID", "{{%work_dismiss_user_detail}}");
			$this->addForeignKey("KEY_WORK_DISMISS_USER_DETAIL_EXTERNAL_USERID", "{{%work_dismiss_user_detail}}", "external_userid", "{{%work_external_contact}}", "id", "CASCADE", "CASCADE");

			/**work_external_contact_external_profile**/
			$this->dropForeignKey("KEY_WORK_EXTERNAL_CONTACT_EXTERNAL_PROFILE_EXTERNALUSERID", "{{%work_external_contact_external_profile}}");
			$this->addForeignKey("KEY_WORK_EXTERNAL_CONTACT_EXTERNAL_PROFILE_EXTERNALUSERID", "{{%work_external_contact_external_profile}}", "external_userid", "{{%work_external_contact}}", "id", "CASCADE", "CASCADE");

			/**work_external_contact_follow_statistic**/
			$this->dropForeignKey("KEY_EXTERNAL_CONTACT_FOLLOW_STATISTIC_EXTERNAL_USERID", "{{%work_external_contact_follow_statistic}}");
			$this->addForeignKey("KEY_EXTERNAL_CONTACT_FOLLOW_STATISTIC_EXTERNAL_USERID", "{{%work_external_contact_follow_statistic}}", "external_userid", "{{%work_external_contact}}", "id", "CASCADE", "CASCADE");

			/**work_external_contact_follow_user**/
			$this->dropForeignKey("KEY_WORK_EXTERNAL_CONTACT_FOLLOW_USER_EXTERNALUSERID", "{{%work_external_contact_follow_user}}");
			$this->addForeignKey("KEY_WORK_EXTERNAL_CONTACT_FOLLOW_USER_EXTERNALUSERID", "{{%work_external_contact_follow_user}}", "external_userid", "{{%work_external_contact}}", "id", "CASCADE", "CASCADE");

			/**work_external_contact_member**/
			$this->dropForeignKey("KEY_EXTERNAL_USERID", "{{%work_external_contact_member}}");
			$this->addForeignKey("KEY_EXTERNAL_USERID", "{{%work_external_contact_member}}", "external_userid", "{{%work_external_contact}}", "id", "CASCADE", "CASCADE");

			/**work_external_contact_user_way_detail**/
			$this->dropForeignKey("KEY_WORK_TAG_CONTACT_EX_ID", "{{%work_external_contact_user_way_detail}}");
			$this->addForeignKey("KEY_WORK_TAG_CONTACT_EX_ID", "{{%work_external_contact_user_way_detail}}", "external_id", "{{%work_external_contact}}", "id", "CASCADE", "CASCADE");

			/**work_group_clock_join**/
			$this->dropForeignKey("KEY_WORK_GROUP_CLOCK_JOIN_EXTERNAL_ID", "{{%work_group_clock_join}}");
			$this->addForeignKey("KEY_WORK_GROUP_CLOCK_JOIN_EXTERNAL_ID", "{{%work_group_clock_join}}", "external_id", "{{%work_external_contact}}", "id", "CASCADE", "CASCADE");

			/**work_group_sending_redpacket_send**/
			$this->dropForeignKey("KEY_WORK_GROUP_SENDING_REDPACKET_SEND_EXTERNAL_USERID", "{{%work_group_sending_redpacket_send}}");
			$this->addForeignKey("KEY_WORK_GROUP_SENDING_REDPACKET_SEND_EXTERNAL_USERID", "{{%work_group_sending_redpacket_send}}", "external_userid", "{{%work_external_contact}}", "id", "CASCADE", "CASCADE");

			/**work_moment_goods**/
			$this->dropForeignKey("pig_fk-work_moment_goods-external_id", "{{%work_moment_goods}}");
			$this->addForeignKey("pig_fk-work_moment_goods-external_id", "{{%work_moment_goods}}", "external_id", "{{%work_external_contact}}", "id", "CASCADE", "CASCADE");

			/**work_moment_reply**/
			$this->dropForeignKey("pig_fk-work_moment_reply-external_id", "{{%work_moment_reply}}");
			$this->addForeignKey("pig_fk-work_moment_reply-external_id", "{{%work_moment_reply}}", "external_id", "{{%work_external_contact}}", "id", "CASCADE", "CASCADE");

			/**work_msg_audit_agree**/
			$this->dropForeignKey("pig_fk-work_msg_audit_agree-external_id", "{{%work_msg_audit_agree}}");
			$this->addForeignKey("pig_fk-work_msg_audit_agree-external_id", "{{%work_msg_audit_agree}}", "external_id", "{{%work_external_contact}}", "id", "CASCADE", "CASCADE");

			/**work_msg_audit_info**/
			$this->dropForeignKey("pig_fk-work_msg_audit_info-external_id", "{{%work_msg_audit_info}}");
			$this->addForeignKey("pig_fk-work_msg_audit_info-external_id", "{{%work_msg_audit_info}}", "external_id", "{{%work_external_contact}}", "id", "CASCADE", "CASCADE");

			/**work_msg_audit_info**/
			$this->dropForeignKey("pig_fk-work_msg_audit_info-to_external_id", "{{%work_msg_audit_info}}");
			$this->addForeignKey("pig_fk-work_msg_audit_info-to_external_id", "{{%work_msg_audit_info}}", "to_external_id", "{{%work_external_contact}}", "id", "CASCADE", "CASCADE");

			/**work_msg_audit_info_agree**/
			$this->dropForeignKey("pig_fk-work_msg_audit_info_agree-external_id", "{{%work_msg_audit_info_agree}}");
			$this->addForeignKey("pig_fk-work_msg_audit_info_agree-external_id", "{{%work_msg_audit_info_agree}}", "external_id", "{{%work_external_contact}}", "id", "CASCADE", "CASCADE");

			/**work_msg_audit_info_calendar**/
			$this->dropForeignKey("pig_fk-work_msg_audit_info_calendar-external_id", "{{%work_msg_audit_info_calendar}}");
			$this->addForeignKey("pig_fk-work_msg_audit_info_calendar-external_id", "{{%work_msg_audit_info_calendar}}", "external_id", "{{%work_external_contact}}", "id", "CASCADE", "CASCADE");

			/**work_msg_audit_info_calendar_attendee**/
			$this->dropForeignKey("pig_fk-work_msg_audit_info_calendar_attendee-external_id", "{{%work_msg_audit_info_calendar_attendee}}");
			$this->addForeignKey("pig_fk-work_msg_audit_info_calendar_attendee-external_id", "{{%work_msg_audit_info_calendar_attendee}}", "external_id", "{{%work_external_contact}}", "id", "CASCADE", "CASCADE");

			/**work_msg_audit_info_card**/
			$this->dropForeignKey("pig_fk-work_msg_audit_info_card-external_id", "{{%work_msg_audit_info_card}}");
			$this->addForeignKey("pig_fk-work_msg_audit_info_card-external_id", "{{%work_msg_audit_info_card}}", "external_id", "{{%work_external_contact}}", "id", "CASCADE", "CASCADE");

			/**work_msg_audit_info_docmsg**/
			$this->dropForeignKey("pig_fk-work_msg_audit_info_docmsg-external_id", "{{%work_msg_audit_info_docmsg}}");
			$this->addForeignKey("pig_fk-work_msg_audit_info_docmsg-external_id", "{{%work_msg_audit_info_docmsg}}", "external_id", "{{%work_external_contact}}", "id", "CASCADE", "CASCADE");

			/**work_msg_audit_info_to_info**/
			$this->dropForeignKey("pig_fk-work_msg_audit_to_info-external_id", "{{%work_msg_audit_info_to_info}}");
			$this->addForeignKey("pig_fk-work_msg_audit_to_info-external_id", "{{%work_msg_audit_info_to_info}}", "external_id", "{{%work_external_contact}}", "id", "CASCADE", "CASCADE");

			/**work_public_activity_fans_user**/
			$this->dropForeignKey("KEY_WORK_PUBLIC_ACTIVITY_USER_FANS_EXT_USERID", "{{%work_public_activity_fans_user}}");
			$this->addForeignKey("KEY_WORK_PUBLIC_ACTIVITY_USER_FANS_EXT_USERID", "{{%work_public_activity_fans_user}}", "external_userid", "{{%work_external_contact}}", "id", "CASCADE", "CASCADE");

			/**work_tag_contact**/
			$this->dropForeignKey("KEY_WORK_TAG_CONTACT_ID", "{{%work_tag_contact}}");
			$this->addForeignKey("KEY_WORK_TAG_CONTACT_ID", "{{%work_tag_contact}}", "contact_id", "{{%work_external_contact}}", "id", "CASCADE", "CASCADE");

			/**work_tag_group_statistic**/
			$this->dropForeignKey("KEY_WORK_TAG_GROUP_STATISTIC_EXTERNAL_ID", "{{%work_tag_group_statistic}}");
			$this->addForeignKey("KEY_WORK_TAG_GROUP_STATISTIC_EXTERNAL_ID", "{{%work_tag_group_statistic}}", "external_id", "{{%work_external_contact}}", "id", "CASCADE", "CASCADE");

			/**work_user_tag_external**/
			$this->dropForeignKey("KEY_WORK_USER_TAG_EXTERNAL_EXTERNALID", "{{%work_user_tag_external}}");
			$this->addForeignKey("KEY_WORK_USER_TAG_EXTERNAL_EXTERNALID", "{{%work_user_tag_external}}", "external_id", "{{%work_external_contact}}", "id", "CASCADE", "CASCADE");

			//其他表相关wait_customer_task
			/**wait_user_remind**/
			$this->dropForeignKey("KEY_WAIT_USER_REMIND_CUSTOM_ID", "{{%wait_user_remind}}");
			$this->addForeignKey("KEY_WAIT_USER_REMIND_CUSTOM_ID", "{{%wait_user_remind}}", "custom_id", "{{%wait_customer_task}}", "id", "CASCADE", "CASCADE");
			//其他表相关work_external_contact_follow_user
			/**work_external_contact_member**/
			$this->dropForeignKey("KEY_WORK_EXTERNAL_CONTACT_MEMBER_USER_ID", "{{%work_external_contact_member}}");
			$this->addForeignKey("KEY_WORK_EXTERNAL_CONTACT_MEMBER_USER_ID", "{{%work_external_contact_member}}", "follow_user_id", "{{%work_external_contact_follow_user}}", "id", "CASCADE", "CASCADE");

			/**work_per_tag_follow_user**/
			$this->dropForeignKey("KEY_WORK_PER_TAG_FOLLOW_USER_FOLLOW_USER_ID", "{{%work_per_tag_follow_user}}");
			$this->addForeignKey("KEY_WORK_PER_TAG_FOLLOW_USER_FOLLOW_USER_ID", "{{%work_per_tag_follow_user}}", "follow_user_id", "{{%work_external_contact_follow_user}}", "id", "CASCADE", "CASCADE");

			/**work_tag_follow_user**/
			$this->dropForeignKey("KEY_WORK_TAG_FOLLOW_USER_FOLLOW_USER_ID", "{{%work_tag_follow_user}}");
			$this->addForeignKey("KEY_WORK_TAG_FOLLOW_USER_FOLLOW_USER_ID", "{{%work_tag_follow_user}}", "follow_user_id", "{{%work_external_contact_follow_user}}", "id", "CASCADE", "CASCADE");

			//其他表相关work_group_clock_join
			/**work_group_clock_detail**/
			$this->dropForeignKey("KEY_WORK_GROUP_CLOCK_DETAIL_JOIN_ID", "{{%work_group_clock_detail}}");
			$this->addForeignKey("KEY_WORK_GROUP_CLOCK_DETAIL_JOIN_ID", "{{%work_group_clock_detail}}", "join_id", "{{%work_group_clock_join}}", "id", "CASCADE", "CASCADE");

			/**work_group_clock_prize**/
			$this->dropForeignKey("KEY_WORK_GROUP_CLOCK_PRIZE_JOIN_ID", "{{%work_group_clock_prize}}");
			$this->addForeignKey("KEY_WORK_GROUP_CLOCK_PRIZE_JOIN_ID", "{{%work_group_clock_prize}}", "join_id", "{{%work_group_clock_join}}", "id", "CASCADE", "CASCADE");

			/**work_group_clock_prize**/
			$this->dropForeignKey("KEY_WORK_PUBLIC_ACTIVITY_TIER_FANS", "{{%work_public_activity_tier}}");
			$this->addForeignKey("KEY_WORK_PUBLIC_ACTIVITY_TIER_FANS", "{{%work_public_activity_tier}}", "fans_id", "{{%work_public_activity_fans_user}}", "id", "CASCADE", "CASCADE");

			/**work_group_sending_redpacket_send**/
			$this->dropForeignKey("KEY_WORK_GROUP_SENDING_REDPACKET_SEND_GROUP_SEND_ID", "{{%work_group_sending_redpacket_send}}");
			$this->addForeignKey("KEY_WORK_GROUP_SENDING_REDPACKET_SEND_GROUP_SEND_ID", "{{%work_group_sending_redpacket_send}}", "group_send_id", "{{%work_tag_group_statistic}}", "id", "CASCADE", "CASCADE");


		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m201219_034750_change_key cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m201219_034750_change_key cannot be reverted.\n";

			return false;
		}
		*/
	}
