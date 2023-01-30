<?php

	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%work_msg_audit_notice_rule_info}}`.
	 * Has foreign keys to the tables:
	 *
	 * - `{{%work_msg_audit_notice_rule}}`
	 * - `{{%work_msg_audit_category}}`
	 * - `{{%work_corp_agent}}`
	 * - `{{%work_user}}`
	 */
	class m200619_022926_create_work_msg_audit_notice_rule_info_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_msg_audit_notice_rule_info}}', [
				'id'          => $this->primaryKey(11)->unsigned(),
				'rule_id'     => $this->integer(11)->unsigned()->comment('规则ID'),
				'category_id' => $this->integer(11)->unsigned()->comment('类别ID'),
				'agent_id'    => $this->integer(11)->unsigned()->comment('应用ID'),
				'user_id'     => $this->integer(11)->unsigned()->comment('成员ID'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'会话存档提醒规则详情表\'');

			// creates index for column `rule_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_notice_rule_info-rule_id}}',
				'{{%work_msg_audit_notice_rule_info}}',
				'rule_id'
			);

			// add foreign key for table `{{%work_msg_audit_notice_rule}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_notice_rule_info-rule_id}}',
				'{{%work_msg_audit_notice_rule_info}}',
				'rule_id',
				'{{%work_msg_audit_notice_rule}}',
				'id',
				'CASCADE'
			);

			// creates index for column `category_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_notice_rule_info-category_id}}',
				'{{%work_msg_audit_notice_rule_info}}',
				'category_id'
			);

			// add foreign key for table `{{%work_msg_audit_category}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_notice_rule_info-category_id}}',
				'{{%work_msg_audit_notice_rule_info}}',
				'category_id',
				'{{%work_msg_audit_category}}',
				'id',
				'CASCADE'
			);

			// creates index for column `agent_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_notice_rule_info-agent_id}}',
				'{{%work_msg_audit_notice_rule_info}}',
				'agent_id'
			);

			// add foreign key for table `{{%work_corp_agent}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_notice_rule_info-agent_id}}',
				'{{%work_msg_audit_notice_rule_info}}',
				'agent_id',
				'{{%work_corp_agent}}',
				'id',
				'CASCADE'
			);

			// creates index for column `user_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_notice_rule_info-user_id}}',
				'{{%work_msg_audit_notice_rule_info}}',
				'user_id'
			);

			// add foreign key for table `{{%work_user}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_notice_rule_info-user_id}}',
				'{{%work_msg_audit_notice_rule_info}}',
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
			// drops foreign key for table `{{%work_msg_audit_notice_rule}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_notice_rule_info-rule_id}}',
				'{{%work_msg_audit_notice_rule_info}}'
			);

			// drops index for column `rule_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_notice_rule_info-rule_id}}',
				'{{%work_msg_audit_notice_rule_info}}'
			);

			// drops foreign key for table `{{%work_msg_audit_category}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_notice_rule_info-category_id}}',
				'{{%work_msg_audit_notice_rule_info}}'
			);

			// drops index for column `category_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_notice_rule_info-category_id}}',
				'{{%work_msg_audit_notice_rule_info}}'
			);

			// drops foreign key for table `{{%work_corp_agent}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_notice_rule_info-agent_id}}',
				'{{%work_msg_audit_notice_rule_info}}'
			);

			// drops index for column `agent_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_notice_rule_info-agent_id}}',
				'{{%work_msg_audit_notice_rule_info}}'
			);

			// drops foreign key for table `{{%work_user}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_notice_rule_info-user_id}}',
				'{{%work_msg_audit_notice_rule_info}}'
			);

			// drops index for column `user_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_notice_rule_info-user_id}}',
				'{{%work_msg_audit_notice_rule_info}}'
			);

			$this->dropTable('{{%work_msg_audit_notice_rule_info}}');
		}
	}
