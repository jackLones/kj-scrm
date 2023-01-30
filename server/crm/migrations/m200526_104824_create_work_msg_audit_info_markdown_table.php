<?php

	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%work_msg_audit_info_markdown}}`.
	 * Has foreign keys to the tables:
	 *
	 * - `{{%work_msg_audit_info}}`
	 */
	class m200526_104824_create_work_msg_audit_info_markdown_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_msg_audit_info_markdown}}', [
				'id'            => $this->primaryKey(11)->unsigned(),
				'audit_info_id' => $this->integer(11)->unsigned()->comment('会话内容ID'),
				'content'       => $this->text()->comment('markdown消息内容，目前为机器人发出的消息'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'MarkDown类型会话消息表\'');

			// creates index for column `audit_info_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_markdown-audit_info_id}}',
				'{{%work_msg_audit_info_markdown}}',
				'audit_info_id'
			);

			// add foreign key for table `{{%work_msg_audit_info}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_markdown-audit_info_id}}',
				'{{%work_msg_audit_info_markdown}}',
				'audit_info_id',
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
				'{{%fk-work_msg_audit_info_markdown-audit_info_id}}',
				'{{%work_msg_audit_info_markdown}}'
			);

			// drops index for column `audit_info_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_markdown-audit_info_id}}',
				'{{%work_msg_audit_info_markdown}}'
			);

			$this->dropTable('{{%work_msg_audit_info_markdown}}');
		}
	}
