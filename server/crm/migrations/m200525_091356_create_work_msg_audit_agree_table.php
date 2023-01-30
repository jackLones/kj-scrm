<?php

	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%work_msg_audit_agree}}`.
	 * Has foreign keys to the tables:
	 *
	 * - `{{%work_msg_audit}}`
	 * - `{{%work_user}}`
	 * - `{{%work_external_contact}}`
	 * - `{{%work_chat}}`
	 * - `{{%agree_status}}`
	 */
	class m200525_091356_create_work_msg_audit_agree_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_msg_audit_agree}}', [
				'id'                 => $this->primaryKey(11)->unsigned(),
				'audit_id'           => $this->integer(11)->unsigned()->comment('会话存档ID'),
				'user_id'            => $this->integer(11)->unsigned()->comment('成员ID'),
				'userid'             => $this->char(64)->comment('内部成员的userid'),
				'external_id'        => $this->integer(11)->unsigned()->comment('外部联系人ID'),
				'exteranalopenid'    => $this->char(64)->comment('外部成员的externalopenid'),
				'chat_id'            => $this->integer(11)->unsigned()->comment('企业群ID'),
				'roomid'             => $this->char(64)->comment('企业外部群ID'),
				'agree_status'       => $this->char(16)->comment('同意："Agree"，不同意："Disagree"，默认同意："Default_Agree"'),
				'status_change_time' => $this->char(16)->comment('同意状态改变的具体时间，utc时间'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'会话同意情况\'');

			// creates index for column `audit_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_agree-audit_id}}',
				'{{%work_msg_audit_agree}}',
				'audit_id'
			);

			// add foreign key for table `{{%work_msg_audit}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_agree-audit_id}}',
				'{{%work_msg_audit_agree}}',
				'audit_id',
				'{{%work_msg_audit}}',
				'id',
				'CASCADE'
			);

			// creates index for column `user_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_agree-user_id}}',
				'{{%work_msg_audit_agree}}',
				'user_id'
			);

			// add foreign key for table `{{%work_user}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_agree-user_id}}',
				'{{%work_msg_audit_agree}}',
				'user_id',
				'{{%work_user}}',
				'id',
				'CASCADE'
			);

			// creates index for column `external_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_agree-external_id}}',
				'{{%work_msg_audit_agree}}',
				'external_id'
			);

			// add foreign key for table `{{%work_external_contact}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_agree-external_id}}',
				'{{%work_msg_audit_agree}}',
				'external_id',
				'{{%work_external_contact}}',
				'id',
				'CASCADE'
			);

			// creates index for column `chat_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_agree-chat_id}}',
				'{{%work_msg_audit_agree}}',
				'chat_id'
			);

			// add foreign key for table `{{%work_chat}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_agree-chat_id}}',
				'{{%work_msg_audit_agree}}',
				'chat_id',
				'{{%work_chat}}',
				'id',
				'CASCADE'
			);

			// creates index for column `agree_status`
			$this->createIndex(
				'{{%idx-work_msg_audit_agree-agree_status}}',
				'{{%work_msg_audit_agree}}',
				'agree_status'
			);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			// drops foreign key for table `{{%work_msg_audit}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_agree-audit_id}}',
				'{{%work_msg_audit_agree}}'
			);

			// drops index for column `audit_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_agree-audit_id}}',
				'{{%work_msg_audit_agree}}'
			);

			// drops foreign key for table `{{%work_user}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_agree-user_id}}',
				'{{%work_msg_audit_agree}}'
			);

			// drops index for column `user_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_agree-user_id}}',
				'{{%work_msg_audit_agree}}'
			);

			// drops foreign key for table `{{%work_external_contact}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_agree-external_id}}',
				'{{%work_msg_audit_agree}}'
			);

			// drops index for column `external_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_agree-external_id}}',
				'{{%work_msg_audit_agree}}'
			);

			// drops foreign key for table `{{%work_chat}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_agree-chat_id}}',
				'{{%work_msg_audit_agree}}'
			);

			// drops index for column `chat_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_agree-chat_id}}',
				'{{%work_msg_audit_agree}}'
			);

			// drops index for column `agree_status`
			$this->dropIndex(
				'{{%idx-work_msg_audit_agree-agree_status}}',
				'{{%work_msg_audit_agree}}'
			);

			$this->dropTable('{{%work_msg_audit_agree}}');
		}
	}
