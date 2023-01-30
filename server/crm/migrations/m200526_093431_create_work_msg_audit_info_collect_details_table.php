<?php

	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%work_msg_audit_info_collect_details}}`.
	 * Has foreign keys to the tables:
	 *
	 * - `{{%work_msg_audit_info_collect}}`
	 */
	class m200526_093431_create_work_msg_audit_info_collect_details_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_msg_audit_info_collect_details}}', [
				'id'         => $this->primaryKey(11)->unsigned(),
				'collect_id' => $this->integer(11)->unsigned()->comment('填表信息ID'),
				'detail_id'  => $this->integer(64)->unsigned()->comment('表项id'),
				'ques'       => $this->char(64)->comment('表项名称'),
				'type'       => $this->char(8)->comment('表项类型，有Text文本,Number数字,Date日期,Time时间'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'填表类型会话消息选项表\'');

			// creates index for column `collect_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_collect_details-collect_id}}',
				'{{%work_msg_audit_info_collect_details}}',
				'collect_id'
			);

			// add foreign key for table `{{%work_msg_audit_info_collect}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_collect_details-collect_id}}',
				'{{%work_msg_audit_info_collect_details}}',
				'collect_id',
				'{{%work_msg_audit_info_collect}}',
				'id',
				'CASCADE'
			);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			// drops foreign key for table `{{%work_msg_audit_info_collect}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_collect_details-collect_id}}',
				'{{%work_msg_audit_info_collect_details}}'
			);

			// drops index for column `collect_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_collect_details-collect_id}}',
				'{{%work_msg_audit_info_collect_details}}'
			);

			$this->dropTable('{{%work_msg_audit_info_collect_details}}');
		}
	}
