<?php

	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%work_msg_audit_info_chatrecord_item}}`.
	 * Has foreign keys to the tables:
	 *
	 * - `{{%work_msg_audit_info_chatrecord}}`
	 * - `{{%work_msg_audit_info}}`
	 */
	class m200526_092031_create_work_msg_audit_info_chatrecord_item_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_msg_audit_info_chatrecord_item}}', [
				'id'            => $this->primaryKey(11)->unsigned(),
				'record_id'     => $this->integer(11)->unsigned()->comment('会话记录ID'),
				'audit_info_id' => $this->integer(11)->unsigned()->comment('会话内容ID'),
				'type'          => $this->char(64)->comment('每条聊天记录的具体消息类型：ChatRecordText、ChatRecordFile、ChatRecordImage、ChatRecordVideo、ChatRecordLink、ChatRecordLocation等'),
				'from_chatroom' => $this->tinyInteger(1)->comment('是否来自群聊：0、否；1、是'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'会话记录类型会话消息详情表\'');

			// creates index for column `record_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_chatrecord_item-record_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}',
				'record_id'
			);

			// add foreign key for table `{{%work_msg_audit_info_chatrecord}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_chatrecord_item-record_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}',
				'record_id',
				'{{%work_msg_audit_info_chatrecord}}',
				'id',
				'CASCADE'
			);

			// creates index for column `audit_info_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_chatrecord_item-audit_info_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}',
				'audit_info_id'
			);

			// add foreign key for table `{{%work_msg_audit_info}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_chatrecord_item-audit_info_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}',
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
			// drops foreign key for table `{{%work_msg_audit_info_chatrecord}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_chatrecord_item-record_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}'
			);

			// drops index for column `record_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_chatrecord_item-record_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}'
			);

			// drops foreign key for table `{{%work_msg_audit_info}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_chatrecord_item-audit_info_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}'
			);

			// drops index for column `audit_info_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_chatrecord_item-audit_info_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}'
			);

			$this->dropTable('{{%work_msg_audit_info_chatrecord_item}}');
		}
	}
