<?php

	use yii\db\Migration;

	/**
	 * Handles adding columns to table `{{%work_msg_audit_info_chatrecord_item}}`.
	 * Has foreign keys to the tables:
	 *
	 * - `{{%work_msg_audit_info_mixed}}`
	 */
	class m200604_124816_add_mixed_id_columns_to_work_msg_audit_info_chatrecord_item_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%work_msg_audit_info_chatrecord_item}}', 'mixed_id', $this->integer(11)->unsigned()->after('news_id')->comment('混合消息ID'));

			// creates index for column `mixed_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_chatrecord_item-mixed_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}',
				'mixed_id'
			);

			// add foreign key for table `{{%work_msg_audit_info_mixed}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_chatrecord_item-mixed_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}',
				'mixed_id',
				'{{%work_msg_audit_info_mixed}}',
				'id',
				'CASCADE'
			);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			// drops foreign key for table `{{%work_msg_audit_info_mixed}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_chatrecord_item-mixed_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}'
			);

			// drops index for column `mixed_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_chatrecord_item-mixed_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}'
			);

			$this->dropColumn('{{%work_msg_audit_info_chatrecord_item}}', 'mixed_id');
		}
	}
