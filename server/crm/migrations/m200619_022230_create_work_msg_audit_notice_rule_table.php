<?php

	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%work_msg_audit_notice_rule}}`.
	 * Has foreign keys to the tables:
	 *
	 * - `{{%work_msg_audit}}`
	 */
	class m200619_022230_create_work_msg_audit_notice_rule_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_msg_audit_notice_rule}}', [
				'id'          => $this->primaryKey(11)->unsigned(),
				'audit_id'    => $this->integer(11)->unsigned()->comment('会话存档ID'),
				'notice_name' => $this->char(16)->comment('规则名称'),
				'status'      => $this->tinyInteger(1)->comment('状态：0、关闭；1、开启')->defaultValue(1),
				'create_time' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'会话存档提醒规则表\'');

			// creates index for column `audit_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_notice_rule-audit_id}}',
				'{{%work_msg_audit_notice_rule}}',
				'audit_id'
			);

			// add foreign key for table `{{%work_msg_audit}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_notice_rule-audit_id}}',
				'{{%work_msg_audit_notice_rule}}',
				'audit_id',
				'{{%work_msg_audit}}',
				'id',
				'CASCADE'
			);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			// drops foreign key for table `{{%work_msg_audit}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_notice_rule-audit_id}}',
				'{{%work_msg_audit_notice_rule}}'
			);

			// drops index for column `audit_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_notice_rule-audit_id}}',
				'{{%work_msg_audit_notice_rule}}'
			);

			$this->dropTable('{{%work_msg_audit_notice_rule}}');
		}
	}
