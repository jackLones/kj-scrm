<?php

	use yii\db\Migration;

	/**
	 * Handles adding columns to table `{{%work_msg_audit}}`.
	 */
	class m200608_070045_add_some_columns_to_work_msg_audit_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%work_msg_audit}}', 'credit_code', $this->char(64)->after('corp_id')->comment('统一社会信用代码'));
			$this->addColumn('{{%work_msg_audit}}', 'contact_user', $this->char(32)->after('credit_code')->comment('接口联系人'));
			$this->addColumn('{{%work_msg_audit}}', 'contact_phone', $this->char(16)->after('contact_user')->comment('接口信息人联系电话'));
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			$this->dropColumn('{{%work_msg_audit}}', 'credit_code');
			$this->dropColumn('{{%work_msg_audit}}', 'contact_user');
			$this->dropColumn('{{%work_msg_audit}}', 'contact_phone');
		}
	}
