<?php

	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%work_msg_audit_info_weapp}}`.
	 * Has foreign keys to the tables:
	 *
	 * - `{{%work_msg_audit_info}}`
	 */
	class m200526_091350_create_work_msg_audit_info_weapp_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_msg_audit_info_weapp}}', [
				'id'            => $this->primaryKey(11)->unsigned(),
				'audit_info_id' => $this->integer(11)->unsigned()->comment('会话内容ID'),
				'title'         => $this->char(64)->comment('消息标题'),
				'description'   => $this->string(255)->comment('消息描述'),
				'username'      => $this->char(64)->comment('用户名称'),
				'displayname'   => $this->char(64)->comment('小程序名称'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'小程序类型会话消息表\'');

			// creates index for column `audit_info_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_weapp-audit_info_id}}',
				'{{%work_msg_audit_info_weapp}}',
				'audit_info_id'
			);

			// add foreign key for table `{{%work_msg_audit_info}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_weapp-audit_info_id}}',
				'{{%work_msg_audit_info_weapp}}',
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
				'{{%fk-work_msg_audit_info_weapp-audit_info_id}}',
				'{{%work_msg_audit_info_weapp}}'
			);

			// drops index for column `audit_info_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_weapp-audit_info_id}}',
				'{{%work_msg_audit_info_weapp}}'
			);

			$this->dropTable('{{%work_msg_audit_info_weapp}}');
		}
	}
