<?php

	use yii\db\Migration;

	/**
	 * Handles adding columns to table `{{%work_msg_audit_notice_rule_info}}`.
	 */
	class m200619_090310_add_status_columns_to_work_msg_audit_notice_rule_info_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%work_msg_audit_notice_rule_info}}', 'status', $this->tinyInteger(1)->after('user_id')->comment('状态：0、关闭；1、开启'));
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			$this->dropColumn('{{%work_msg_audit_notice_rule_info}}', 'status');
		}
	}
