<?php

	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%work_msg_audit_info_agree}}`.
	 * Has foreign keys to the tables:
	 *
	 * - `{{%work_msg_audit_info}}`
	 * - `{{%work_user}}`
	 * - `{{%work_external_contact}}`
	 */
	class m200526_081803_create_work_msg_audit_info_agree_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_msg_audit_info_agree}}', [
				'id'            => $this->primaryKey(11)->unsigned(),
				'audit_info_id' => $this->integer(11)->unsigned()->comment('会话内容ID'),
				'user_id'       => $this->integer(11)->unsigned()->comment('成员ID'),
				'external_id'   => $this->integer(11)->unsigned()->comment('外部联系人ID'),
				'userid'        => $this->char(64)->comment('同意、不同意协议者的userid，外部企业默认为external_userid'),
				'agree_time'    => $this->char(16)->comment('同意、不同意协议的时间，utc时间，ms单位'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'同意会话类型会话消息表\'');

			// creates index for column `audit_info_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_agree-audit_info_id}}',
				'{{%work_msg_audit_info_agree}}',
				'audit_info_id'
			);

			// add foreign key for table `{{%work_msg_audit_info}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_agree-audit_info_id}}',
				'{{%work_msg_audit_info_agree}}',
				'audit_info_id',
				'{{%work_msg_audit_info}}',
				'id',
				'CASCADE'
			);

			// creates index for column `user_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_agree-user_id}}',
				'{{%work_msg_audit_info_agree}}',
				'user_id'
			);

			// add foreign key for table `{{%work_user}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_agree-user_id}}',
				'{{%work_msg_audit_info_agree}}',
				'user_id',
				'{{%work_user}}',
				'id',
				'CASCADE'
			);

			// creates index for column `external_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_agree-external_id}}',
				'{{%work_msg_audit_info_agree}}',
				'external_id'
			);

			// add foreign key for table `{{%work_external_contact}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_agree-external_id}}',
				'{{%work_msg_audit_info_agree}}',
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
				'{{%fk-work_msg_audit_info_agree-audit_info_id}}',
				'{{%work_msg_audit_info_agree}}'
			);

			// drops index for column `audit_info_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_agree-audit_info_id}}',
				'{{%work_msg_audit_info_agree}}'
			);

			// drops foreign key for table `{{%work_user}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_agree-user_id}}',
				'{{%work_msg_audit_info_agree}}'
			);

			// drops index for column `user_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_agree-user_id}}',
				'{{%work_msg_audit_info_agree}}'
			);

			// drops foreign key for table `{{%work_external_contact}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_agree-external_id}}',
				'{{%work_msg_audit_info_agree}}'
			);

			// drops index for column `external_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_agree-external_id}}',
				'{{%work_msg_audit_info_agree}}'
			);

			$this->dropTable('{{%work_msg_audit_info_agree}}');
		}
	}
