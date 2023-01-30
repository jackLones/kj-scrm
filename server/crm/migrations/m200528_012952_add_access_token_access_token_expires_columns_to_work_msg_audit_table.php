<?php

	use yii\db\Migration;

	/**
	 * Handles adding columns to table `{{%work_msg_audit}}`.
	 */
	class m200528_012952_add_access_token_access_token_expires_columns_to_work_msg_audit_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%work_msg_audit}}', 'access_token', $this->string(255)->after('secret')->comment('会话存档的access_token'));
			$this->addColumn('{{%work_msg_audit}}', 'access_token_expires', $this->char(16)->after('access_token')->comment('access_token有效期'));
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			$this->dropColumn('{{%work_msg_audit}}', 'access_token');
			$this->dropColumn('{{%work_msg_audit}}', 'access_token_expires');
		}
	}
