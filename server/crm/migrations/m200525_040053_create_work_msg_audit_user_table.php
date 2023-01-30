<?php

	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%work_msg_audit_user}}`.
	 * Has foreign keys to the tables:
	 *
	 * - `{{%work_msg_audit}}`
	 * - `{{%work_user}}`
	 */
	class m200525_040053_create_work_msg_audit_user_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_msg_audit_user}}', [
				'id'       => $this->primaryKey(11)->unsigned(),
				'audit_id' => $this->integer(11)->unsigned()->comment('会话存档ID'),
				'user_id'  => $this->integer(11)->unsigned()->comment('成员ID'),
				'userid'   => $this->char(64)->comment('成员UserID。对应管理端的帐号，企业内必须唯一。不区分大小写，长度为1~64个字节'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'会话内容存档开启成员列表\'');

			// creates index for column `audit_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_user-audit_id}}',
				'{{%work_msg_audit_user}}',
				'audit_id'
			);

			// add foreign key for table `{{%work_msg_audit}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_user-audit_id}}',
				'{{%work_msg_audit_user}}',
				'audit_id',
				'{{%work_msg_audit}}',
				'id',
				'CASCADE'
			);

			// creates index for column `user_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_user-user_id}}',
				'{{%work_msg_audit_user}}',
				'user_id'
			);

			// add foreign key for table `{{%work_user}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_user-user_id}}',
				'{{%work_msg_audit_user}}',
				'user_id',
				'{{%work_user}}',
				'id',
				'CASCADE'
			);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			// drops foreign key for table `{{%work_msg_audit}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_user-audit_id}}',
				'{{%work_msg_audit_user}}'
			);

			// drops index for column `audit_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_user-audit_id}}',
				'{{%work_msg_audit_user}}'
			);

			// drops foreign key for table `{{%work_user}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_user-user_id}}',
				'{{%work_msg_audit_user}}'
			);

			// drops index for column `user_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_user-user_id}}',
				'{{%work_msg_audit_user}}'
			);

			$this->dropTable('{{%work_msg_audit_user}}');
		}
	}
