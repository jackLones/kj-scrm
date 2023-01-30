<?php

	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%work_msg_audit_info_docmsg}}`.
	 * Has foreign keys to the tables:
	 *
	 * - `{{%work_msg_audit_info}}`
	 * - `{{%work_user}}`
	 * - `{{%work_external_contact}}`
	 */
	class m200526_100805_create_work_msg_audit_info_docmsg_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_msg_audit_info_docmsg}}', [
				'id'            => $this->primaryKey(11)->unsigned(),
				'audit_info_id' => $this->integer(11)->unsigned()->comment('会话内容ID'),
				'title'         => $this->char(64)->comment('在线文档名称'),
				'link_url'      => $this->text()->comment('在线文档链接'),
				'user_id'       => $this->integer(11)->unsigned()->comment('成员ID'),
				'external_id'   => $this->integer(11)->unsigned()->comment('外部联系人ID'),
				'doc_creator'   => $this->char(64)->comment('在线文档创建者。本企业成员创建为userid；外部企业成员创建为external_userid'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'在线文档类型会话消息表\'');

			// creates index for column `audit_info_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_docmsg-audit_info_id}}',
				'{{%work_msg_audit_info_docmsg}}',
				'audit_info_id'
			);

			// add foreign key for table `{{%work_msg_audit_info}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_docmsg-audit_info_id}}',
				'{{%work_msg_audit_info_docmsg}}',
				'audit_info_id',
				'{{%work_msg_audit_info}}',
				'id',
				'CASCADE'
			);

			// creates index for column `user_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_docmsg-user_id}}',
				'{{%work_msg_audit_info_docmsg}}',
				'user_id'
			);

			// add foreign key for table `{{%work_user}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_docmsg-user_id}}',
				'{{%work_msg_audit_info_docmsg}}',
				'user_id',
				'{{%work_user}}',
				'id',
				'CASCADE'
			);

			// creates index for column `external_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_docmsg-external_id}}',
				'{{%work_msg_audit_info_docmsg}}',
				'external_id'
			);

			// add foreign key for table `{{%work_external_contact}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_docmsg-external_id}}',
				'{{%work_msg_audit_info_docmsg}}',
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
				'{{%fk-work_msg_audit_info_docmsg-audit_info_id}}',
				'{{%work_msg_audit_info_docmsg}}'
			);

			// drops index for column `audit_info_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_docmsg-audit_info_id}}',
				'{{%work_msg_audit_info_docmsg}}'
			);

			// drops foreign key for table `{{%work_user}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_docmsg-user_id}}',
				'{{%work_msg_audit_info_docmsg}}'
			);

			// drops index for column `user_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_docmsg-user_id}}',
				'{{%work_msg_audit_info_docmsg}}'
			);

			// drops foreign key for table `{{%work_external_contact}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_docmsg-external_id}}',
				'{{%work_msg_audit_info_docmsg}}'
			);

			// drops index for column `external_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_docmsg-external_id}}',
				'{{%work_msg_audit_info_docmsg}}'
			);

			$this->dropTable('{{%work_msg_audit_info_docmsg}}');
		}
	}
