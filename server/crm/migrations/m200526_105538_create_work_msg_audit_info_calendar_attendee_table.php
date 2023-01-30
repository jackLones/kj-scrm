<?php

	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%work_msg_audit_info_calendar_attendee}}`.
	 * Has foreign keys to the tables:
	 *
	 * - `{{%work_msg_audit_info_calendar}}`
	 * - `{{%work_user}}`
	 * - `{{%work_external_contact}}`
	 */
	class m200526_105538_create_work_msg_audit_info_calendar_attendee_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_msg_audit_info_calendar_attendee}}', [
				'id'           => $this->primaryKey(11)->unsigned(),
				'calendar_id'  => $this->integer(11)->unsigned()->comment('日程ID'),
				'user_id'      => $this->integer(11)->unsigned()->comment('成员ID'),
				'external_id'  => $this->integer(11)->unsigned()->comment('外部联系人ID'),
				'attendeename' => $this->char(64)->comment('日程参与人'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'日程类型会话消息参与人表\'');

			// creates index for column `calendar_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_calendar_attendee-calendar_id}}',
				'{{%work_msg_audit_info_calendar_attendee}}',
				'calendar_id'
			);

			// add foreign key for table `{{%work_msg_audit_info_calendar}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_calendar_attendee-calendar_id}}',
				'{{%work_msg_audit_info_calendar_attendee}}',
				'calendar_id',
				'{{%work_msg_audit_info_calendar}}',
				'id',
				'CASCADE'
			);

			// creates index for column `user_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_calendar_attendee-user_id}}',
				'{{%work_msg_audit_info_calendar_attendee}}',
				'user_id'
			);

			// add foreign key for table `{{%work_user}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_calendar_attendee-user_id}}',
				'{{%work_msg_audit_info_calendar_attendee}}',
				'user_id',
				'{{%work_user}}',
				'id',
				'CASCADE'
			);

			// creates index for column `external_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_calendar_attendee-external_id}}',
				'{{%work_msg_audit_info_calendar_attendee}}',
				'external_id'
			);

			// add foreign key for table `{{%work_external_contact}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_calendar_attendee-external_id}}',
				'{{%work_msg_audit_info_calendar_attendee}}',
				'external_id',
				'{{%work_external_contact}}',
				'id',
				'CASCADE'
			);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			// drops foreign key for table `{{%work_msg_audit_info_calendar}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_calendar_attendee-calendar_id}}',
				'{{%work_msg_audit_info_calendar_attendee}}'
			);

			// drops index for column `calendar_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_calendar_attendee-calendar_id}}',
				'{{%work_msg_audit_info_calendar_attendee}}'
			);

			// drops foreign key for table `{{%work_user}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_calendar_attendee-user_id}}',
				'{{%work_msg_audit_info_calendar_attendee}}'
			);

			// drops index for column `user_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_calendar_attendee-user_id}}',
				'{{%work_msg_audit_info_calendar_attendee}}'
			);

			// drops foreign key for table `{{%work_external_contact}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_calendar_attendee-external_id}}',
				'{{%work_msg_audit_info_calendar_attendee}}'
			);

			// drops index for column `external_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_calendar_attendee-external_id}}',
				'{{%work_msg_audit_info_calendar_attendee}}'
			);

			$this->dropTable('{{%work_msg_audit_info_calendar_attendee}}');
		}
	}
