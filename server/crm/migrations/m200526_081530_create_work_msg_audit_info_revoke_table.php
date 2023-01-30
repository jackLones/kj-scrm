<?php

	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%work_msg_audit_info_revoke}}`.
	 * Has foreign keys to the tables:
	 *
	 * - `{{%work_msg_audit_info}}`
	 * - `{{%work_msg_audit_info}}`
	 */
	class m200526_081530_create_work_msg_audit_info_revoke_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_msg_audit_info_revoke}}', [
				'id'            => $this->primaryKey(11)->unsigned(),
				'audit_info_id' => $this->integer(11)->unsigned()->comment('会话内容ID'),
				'pre_info_id'   => $this->integer(11)->unsigned()->comment('会话内容ID'),
				'pre_msgid'     => $this->char(64)->comment('标识撤回的原消息的msgid'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'撤回类型会话消息表\'');

			// creates index for column `audit_info_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_revoke-audit_info_id}}',
				'{{%work_msg_audit_info_revoke}}',
				'audit_info_id'
			);

			// add foreign key for table `{{%work_msg_audit_info}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_revoke-audit_info_id}}',
				'{{%work_msg_audit_info_revoke}}',
				'audit_info_id',
				'{{%work_msg_audit_info}}',
				'id',
				'CASCADE'
			);

			// creates index for column `pre_info_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_revoke-pre_info_id}}',
				'{{%work_msg_audit_info_revoke}}',
				'pre_info_id'
			);

			// add foreign key for table `{{%work_msg_audit_info}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_revoke-pre_info_id}}',
				'{{%work_msg_audit_info_revoke}}',
				'pre_info_id',
				'{{%work_msg_audit_info}}',
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
				'{{%fk-work_msg_audit_info_revoke-audit_info_id}}',
				'{{%work_msg_audit_info_revoke}}'
			);

			// drops index for column `audit_info_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_revoke-audit_info_id}}',
				'{{%work_msg_audit_info_revoke}}'
			);

			// drops foreign key for table `{{%work_msg_audit_info}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_revoke-pre_info_id}}',
				'{{%work_msg_audit_info_revoke}}'
			);

			// drops index for column `pre_info_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_revoke-pre_info_id}}',
				'{{%work_msg_audit_info_revoke}}'
			);

			$this->dropTable('{{%work_msg_audit_info_revoke}}');
		}
	}
