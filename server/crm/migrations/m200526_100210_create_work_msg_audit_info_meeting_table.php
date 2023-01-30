<?php

	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%work_msg_audit_info_meeting}}`.
	 * Has foreign keys to the tables:
	 *
	 * - `{{%work_msg_audit_info}}`
	 */
	class m200526_100210_create_work_msg_audit_info_meeting_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_msg_audit_info_meeting}}', [
				'id'            => $this->primaryKey(11)->unsigned(),
				'audit_info_id' => $this->integer(11)->unsigned()->comment('会话内容ID'),
				'topic'         => $this->char(64)->comment('会议主题'),
				'starttime'     => $this->char(16)->comment('会议开始时间'),
				'endtime'       => $this->char(16)->comment('会议结束时间'),
				'address'       => $this->string(255)->comment('会议地址'),
				'remarks'       => $this->text()->comment('会议备注'),
				'meetingtype'   => $this->integer(11)->unsigned()->comment('会议消息类型。101发起会议邀请消息、102处理会议邀请消息'),
				'meetingid'     => $this->char(64)->comment('会议ID'),
				'status'        => $this->tinyInteger(1)->comment('会议邀请处理状态。1 参加会议、2 拒绝会议、3 待定'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'会议邀请类型会话消息表\'');

			// creates index for column `audit_info_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_meeting-audit_info_id}}',
				'{{%work_msg_audit_info_meeting}}',
				'audit_info_id'
			);

			// add foreign key for table `{{%work_msg_audit_info}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_meeting-audit_info_id}}',
				'{{%work_msg_audit_info_meeting}}',
				'audit_info_id',
				'{{%work_msg_audit_info}}',
				'id',
				'CASCADE'
			);

			// creates index for column `meetingid`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_meeting-meetingid}}',
				'{{%work_msg_audit_info_meeting}}',
				'meetingid'
			);

			// creates index for column `status`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_meeting-status}}',
				'{{%work_msg_audit_info_meeting}}',
				'status'
			);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			// drops foreign key for table `{{%work_msg_audit_info}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_meeting-audit_info_id}}',
				'{{%work_msg_audit_info_meeting}}'
			);

			// drops index for column `audit_info_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_meeting-audit_info_id}}',
				'{{%work_msg_audit_info_meeting}}'
			);

			// drops index for column `meetingid`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_meeting-meetingid}}',
				'{{%work_msg_audit_info_meeting}}'
			);

			// drops index for column `status`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_meeting-status}}',
				'{{%work_msg_audit_info_meeting}}'
			);

			$this->dropTable('{{%work_msg_audit_info_meeting}}');
		}
	}
