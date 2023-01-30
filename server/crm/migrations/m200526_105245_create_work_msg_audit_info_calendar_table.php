<?php

	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%work_msg_audit_info_calendar}}`.
	 * Has foreign keys to the tables:
	 *
	 * - `{{%work_msg_audit_info}}`
	 * - `{{%work_user}}`
	 * - `{{%work_external_contact}}`
	 */
	class m200526_105245_create_work_msg_audit_info_calendar_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_msg_audit_info_calendar}}', [
				'id'            => $this->primaryKey(11)->unsigned(),
				'audit_info_id' => $this->integer(11)->unsigned()->comment('会话内容ID'),
				'title'         => $this->char(64)->comment('日程主题'),
				'user_id'       => $this->integer(11)->unsigned()->comment('成员ID'),
				'external_id'   => $this->integer(11)->unsigned()->comment('外部联系人ID'),
				'creatorname'   => $this->char(64)->comment('日程组织者'),
				'starttime'     => $this->char(16)->comment('日程开始时间 单位秒'),
				'endtime'       => $this->char(16)->comment('日程结束时间 单位秒'),
				'place'         => $this->string(255)->comment('日程地点'),
				'remarks'       => $this->text()->comment('日程备注'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'日程类型会话消息表\'');

			// creates index for column `audit_info_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_calendar-audit_info_id}}',
				'{{%work_msg_audit_info_calendar}}',
				'audit_info_id'
			);

			// add foreign key for table `{{%work_msg_audit_info}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_calendar-audit_info_id}}',
				'{{%work_msg_audit_info_calendar}}',
				'audit_info_id',
				'{{%work_msg_audit_info}}',
				'id',
				'CASCADE'
			);

			// creates index for column `user_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_calendar-user_id}}',
				'{{%work_msg_audit_info_calendar}}',
				'user_id'
			);

			// add foreign key for table `{{%work_user}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_calendar-user_id}}',
				'{{%work_msg_audit_info_calendar}}',
				'user_id',
				'{{%work_user}}',
				'id',
				'CASCADE'
			);

			// creates index for column `external_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_calendar-external_id}}',
				'{{%work_msg_audit_info_calendar}}',
				'external_id'
			);

			// add foreign key for table `{{%work_external_contact}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_calendar-external_id}}',
				'{{%work_msg_audit_info_calendar}}',
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
			// drops foreign key for table `{{%work_msg_audit_info}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_calendar-audit_info_id}}',
				'{{%work_msg_audit_info_calendar}}'
			);

			// drops index for column `audit_info_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_calendar-audit_info_id}}',
				'{{%work_msg_audit_info_calendar}}'
			);

			// drops foreign key for table `{{%work_user}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_calendar-user_id}}',
				'{{%work_msg_audit_info_calendar}}'
			);

			// drops index for column `user_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_calendar-user_id}}',
				'{{%work_msg_audit_info_calendar}}'
			);

			// drops foreign key for table `{{%work_external_contact}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_calendar-external_id}}',
				'{{%work_msg_audit_info_calendar}}'
			);

			// drops index for column `external_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_calendar-external_id}}',
				'{{%work_msg_audit_info_calendar}}'
			);

			$this->dropTable('{{%work_msg_audit_info_calendar}}');
		}
	}
