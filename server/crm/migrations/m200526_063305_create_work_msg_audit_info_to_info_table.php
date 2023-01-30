<?php

	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%work_msg_audit_info_to_info}}`.
	 * Has foreign keys to the tables:
	 *
	 * - `{{%work_msg_audit_info}}`
	 * - `{{%work_user}}`
	 * - `{{%work_external_contact}}`
	 */
	class m200526_063305_create_work_msg_audit_info_to_info_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_msg_audit_info_to_info}}', [
				'id'            => $this->primaryKey(11)->unsigned(),
				'audit_info_id' => $this->integer(11)->unsigned()->comment('会话内容ID'),
				'to_type'       => $this->tinyInteger(1)->unsigned()->comment('接收者身份：1、企业成员；2、外部联系人；3、群机器人'),
				'user_id'       => $this->integer(11)->unsigned()->comment('成员ID'),
				'external_id'   => $this->integer(11)->unsigned()->comment('外部联系人ID'),
				'to'            => $this->char(64)->notNull()->comment('消息接收id。同一企业内容为userid，非相同企业为external_userid'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'会话消息接收方表\'');

			// creates index for column `audit_info_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_to_info-audit_info_id}}',
				'{{%work_msg_audit_info_to_info}}',
				'audit_info_id'
			);

			// add foreign key for table `{{%work_msg_audit_info}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_to_info-audit_info_id}}',
				'{{%work_msg_audit_info_to_info}}',
				'audit_info_id',
				'{{%work_msg_audit_info}}',
				'id',
				'CASCADE'
			);

			// creates index for column `to_type`
			$this->createIndex(
				'{{%idx-work_msg_audit_to_info-to_type}}',
				'{{%work_msg_audit_info_to_info}}',
				'to_type'
			);

			// creates index for column `user_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_to_info-user_id}}',
				'{{%work_msg_audit_info_to_info}}',
				'user_id'
			);

			// add foreign key for table `{{%work_user}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_to_info-user_id}}',
				'{{%work_msg_audit_info_to_info}}',
				'user_id',
				'{{%work_user}}',
				'id',
				'CASCADE'
			);

			// creates index for column `external_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_to_info-external_id}}',
				'{{%work_msg_audit_info_to_info}}',
				'external_id'
			);

			// add foreign key for table `{{%work_external_contact}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_to_info-external_id}}',
				'{{%work_msg_audit_info_to_info}}',
				'external_id',
				'{{%work_external_contact}}',
				'id',
				'CASCADE'
			);

			// creates index for column `to`
			$this->createIndex(
				'{{%idx-work_msg_audit_to_info-to}}',
				'{{%work_msg_audit_info_to_info}}',
				'to'
			);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			// drops foreign key for table `{{%work_msg_audit_info}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_to_info-audit_info_id}}',
				'{{%work_msg_audit_info_to_info}}'
			);

			// drops index for column `audit_info_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_to_info-audit_info_id}}',
				'{{%work_msg_audit_info_to_info}}'
			);

			// drops index for column `to_type`
			$this->dropIndex(
				'{{%idx-work_msg_audit_to_info-to_type}}',
				'{{%work_msg_audit_info_to_info}}'
			);

			// drops foreign key for table `{{%work_user}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_to_info-user_id}}',
				'{{%work_msg_audit_info_to_info}}'
			);

			// drops index for column `user_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_to_info-user_id}}',
				'{{%work_msg_audit_info_to_info}}'
			);

			// drops foreign key for table `{{%work_external_contact}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_to_info-external_id}}',
				'{{%work_msg_audit_info_to_info}}'
			);

			// drops index for column `external_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_to_info-external_id}}',
				'{{%work_msg_audit_info_to_info}}'
			);

			// drops index for column `to`
			$this->dropIndex(
				'{{%idx-work_msg_audit_to_info-to}}',
				'{{%work_msg_audit_info_to_info}}'
			);

			$this->dropTable('{{%work_msg_audit_info_to_info}}');
		}
	}
