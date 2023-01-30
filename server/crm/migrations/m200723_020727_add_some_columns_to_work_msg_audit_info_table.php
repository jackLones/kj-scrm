<?php

	use yii\db\Migration;

	/**
	 * Handles adding columns to table `{{%work_msg_audit_info}}`.
	 * Has foreign keys to the tables:
	 *
	 * - `{{%work_user}}`
	 * - `{{%work_external_contact}}`
	 */
	class m200723_020727_add_some_columns_to_work_msg_audit_info_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%work_msg_audit_info}}', 'to_user_id', $this->integer(11)->unsigned()->comment('接收者成员ID')->after('external_id'));
			$this->addColumn('{{%work_msg_audit_info}}', 'to_external_id', $this->integer(11)->unsigned()->comment('接收者外部联系人ID')->after('to_user_id'));

			// creates index for column `to_user_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info-to_user_id}}',
				'{{%work_msg_audit_info}}',
				'to_user_id'
			);

			// add foreign key for table `{{%work_user}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info-to_user_id}}',
				'{{%work_msg_audit_info}}',
				'to_user_id',
				'{{%work_user}}',
				'id',
				'CASCADE'
			);

			// creates index for column `to_external_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info-to_external_id}}',
				'{{%work_msg_audit_info}}',
				'to_external_id'
			);

			// add foreign key for table `{{%work_external_contact}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info-to_external_id}}',
				'{{%work_msg_audit_info}}',
				'to_external_id',
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
			// drops foreign key for table `{{%work_user}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info-to_user_id}}',
				'{{%work_msg_audit_info}}'
			);

			// drops index for column `to_user_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info-to_user_id}}',
				'{{%work_msg_audit_info}}'
			);

			// drops foreign key for table `{{%work_external_contact}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info-to_external_id}}',
				'{{%work_msg_audit_info}}'
			);

			// drops index for column `to_external_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info-to_external_id}}',
				'{{%work_msg_audit_info}}'
			);

			$this->dropColumn('{{%work_msg_audit_info}}', 'to_user_id');
			$this->dropColumn('{{%work_msg_audit_info}}', 'to_external_id');
		}
	}
