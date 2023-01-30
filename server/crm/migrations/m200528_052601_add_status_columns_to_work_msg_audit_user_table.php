<?php

	use yii\db\Migration;

	/**
	 * Handles adding columns to table `{{%work_msg_audit_user}}`.
	 */
	class m200528_052601_add_status_columns_to_work_msg_audit_user_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%work_msg_audit_user}}', 'status', $this->tinyInteger(1)->unsigned()->comment('状态：0、禁用；1、启用'));
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			$this->dropColumn('{{%work_msg_audit_user}}', 'status');
		}
	}
