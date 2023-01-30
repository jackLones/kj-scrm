<?php

	use yii\db\Migration;

	/**
	 * Handles adding columns to table `{{%work_msg_audit_info}}`.
	 */
	class m200723_014327_add_to_type_columns_to_work_msg_audit_info_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%work_msg_audit_info}}', 'to_type', $this->tinyInteger(1)->unsigned()->defaultValue(0)->comment('接收者身份：1、企业成员；2、外部联系人；3、群机器人')->after('from_type'));

			// creates index for column `to_list`
			$this->createIndex(
				'{{%idx-work_msg_audit_info-tolist}}',
				'{{%work_msg_audit_info}}',
				'tolist(16)'
			);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			// drops index for column `to_list`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info-tolist}}',
				'{{%work_msg_audit_info}}'
			);

			$this->dropColumn('{{%work_msg_audit_info}}', 'tolist');
		}
	}
