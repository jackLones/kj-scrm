<?php

	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%work_msg_audit_info_collect}}`.
	 * Has foreign keys to the tables:
	 *
	 * - `{{%work_msg_audit_info}}`
	 */
	class m200526_093206_create_work_msg_audit_info_collect_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_msg_audit_info_collect}}', [
				'id'            => $this->primaryKey(11)->unsigned(),
				'audit_info_id' => $this->integer(11)->unsigned()->comment('会话内容ID'),
				'room_name'     => $this->char(64)->comment('填表消息所在的群名称'),
				'creator'       => $this->char(64)->comment('创建者在群中的名字'),
				'create_time'   => $this->char(16)->comment('创建的时间'),
				'title'         => $this->char(64)->comment('表名'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'填表类型会话消息表\'');

			// creates index for column `audit_info_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_collect-audit_info_id}}',
				'{{%work_msg_audit_info_collect}}',
				'audit_info_id'
			);

			// add foreign key for table `{{%work_msg_audit_info}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_collect-audit_info_id}}',
				'{{%work_msg_audit_info_collect}}',
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
				'{{%fk-work_msg_audit_info_collect-audit_info_id}}',
				'{{%work_msg_audit_info_collect}}'
			);

			// drops index for column `audit_info_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_collect-audit_info_id}}',
				'{{%work_msg_audit_info_collect}}'
			);

			$this->dropTable('{{%work_msg_audit_info_collect}}');
		}
	}
