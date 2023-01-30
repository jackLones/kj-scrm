<?php

	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%work_msg_audit_info_link}}`.
	 * Has foreign keys to the tables:
	 *
	 * - `{{%work_msg_audit_info}}`
	 */
	class m200526_084652_create_work_msg_audit_info_link_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_msg_audit_info_link}}', [
				'id'            => $this->primaryKey(11)->unsigned(),
				'audit_info_id' => $this->integer(11)->unsigned()->comment('会话内容ID'),
				'title'         => $this->char(64)->comment('消息标题'),
				'description'   => $this->string(255)->comment('消息描述'),
				'link_url'      => $this->text()->comment('链接url地址'),
				'image_url'     => $this->text()->comment('链接图片url'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'链接类型会话消息表\'');

			// creates index for column `audit_info_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_link-audit_info_id}}',
				'{{%work_msg_audit_info_link}}',
				'audit_info_id'
			);

			// add foreign key for table `{{%work_msg_audit_info}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_link-audit_info_id}}',
				'{{%work_msg_audit_info_link}}',
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
				'{{%fk-work_msg_audit_info_link-audit_info_id}}',
				'{{%work_msg_audit_info_link}}'
			);

			// drops index for column `audit_info_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_link-audit_info_id}}',
				'{{%work_msg_audit_info_link}}'
			);

			$this->dropTable('{{%work_msg_audit_info_link}}');
		}
	}
