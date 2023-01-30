<?php

	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%work_msg_audit_info_card}}`.
	 * Has foreign keys to the tables:
	 *
	 * - `{{%work_msg_audit_info}}`
	 * - `{{%work_user}}`
	 * - `{{%work_external_contact}}`
	 */
	class m200526_083332_create_work_msg_audit_info_card_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_msg_audit_info_card}}', [
				'id'            => $this->primaryKey(11)->unsigned(),
				'audit_info_id' => $this->integer(11)->unsigned()->comment('会话内容ID'),
				'corpname'      => $this->char(64)->comment('名片所有者所在的公司名称'),
				'user_id'       => $this->integer(11)->unsigned()->comment('成员ID'),
				'external_id'   => $this->integer(11)->unsigned()->comment('外部联系人ID'),
				'userid'        => $this->char(64)->comment('名片所有者的id，同一公司是userid，不同公司是external_userid'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'名片类型会话消息表\'');

			// creates index for column `audit_info_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_card-audit_info_id}}',
				'{{%work_msg_audit_info_card}}',
				'audit_info_id'
			);

			// add foreign key for table `{{%work_msg_audit_info}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_card-audit_info_id}}',
				'{{%work_msg_audit_info_card}}',
				'audit_info_id',
				'{{%work_msg_audit_info}}',
				'id',
				'CASCADE'
			);

			// creates index for column `user_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_card-user_id}}',
				'{{%work_msg_audit_info_card}}',
				'user_id'
			);

			// add foreign key for table `{{%work_user}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_card-user_id}}',
				'{{%work_msg_audit_info_card}}',
				'user_id',
				'{{%work_user}}',
				'id',
				'CASCADE'
			);

			// creates index for column `external_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_card-external_id}}',
				'{{%work_msg_audit_info_card}}',
				'external_id'
			);

			// add foreign key for table `{{%work_external_contact}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_card-external_id}}',
				'{{%work_msg_audit_info_card}}',
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
				'{{%fk-work_msg_audit_info_card-audit_info_id}}',
				'{{%work_msg_audit_info_card}}'
			);

			// drops index for column `audit_info_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_card-audit_info_id}}',
				'{{%work_msg_audit_info_card}}'
			);

			// drops foreign key for table `{{%work_user}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_card-user_id}}',
				'{{%work_msg_audit_info_card}}'
			);

			// drops index for column `user_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_card-user_id}}',
				'{{%work_msg_audit_info_card}}'
			);

			// drops foreign key for table `{{%work_external_contact}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_card-external_id}}',
				'{{%work_msg_audit_info_card}}'
			);

			// drops index for column `external_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_card-external_id}}',
				'{{%work_msg_audit_info_card}}'
			);

			$this->dropTable('{{%work_msg_audit_info_card}}');
		}
	}
