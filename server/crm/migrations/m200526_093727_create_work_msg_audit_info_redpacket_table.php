<?php

	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%work_msg_audit_info_redpacket}}`.
	 * Has foreign keys to the tables:
	 *
	 * - `{{%work_msg_audit_info}}`
	 */
	class m200526_093727_create_work_msg_audit_info_redpacket_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_msg_audit_info_redpacket}}', [
				'id'            => $this->primaryKey(11)->unsigned(),
				'audit_info_id' => $this->integer(11)->unsigned()->comment('会话内容ID'),
				'type'          => $this->tinyinteger(1)->comment('红包消息类型。1 普通红包、2 拼手气群红包、3 激励群红包'),
				'wish'          => $this->char(64)->comment('红包祝福语'),
				'totalcnt'      => $this->integer(11)->unsigned()->comment('红包总个数'),
				'totalamount'   => $this->integer(11)->unsigned()->comment('红包总金额。单位为分'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'红包类型会话消息表\'');

			// creates index for column `audit_info_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_redpacket-audit_info_id}}',
				'{{%work_msg_audit_info_redpacket}}',
				'audit_info_id'
			);

			// add foreign key for table `{{%work_msg_audit_info}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_redpacket-audit_info_id}}',
				'{{%work_msg_audit_info_redpacket}}',
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
				'{{%fk-work_msg_audit_info_redpacket-audit_info_id}}',
				'{{%work_msg_audit_info_redpacket}}'
			);

			// drops index for column `audit_info_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_redpacket-audit_info_id}}',
				'{{%work_msg_audit_info_redpacket}}'
			);

			$this->dropTable('{{%work_msg_audit_info_redpacket}}');
		}
	}
