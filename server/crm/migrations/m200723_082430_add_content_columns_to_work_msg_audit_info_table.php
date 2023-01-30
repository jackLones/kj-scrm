<?php

	use yii\db\Migration;

	/**
	 * Handles adding columns to table `{{%work_msg_audit_info}}`.
	 */
	class m200723_082430_add_content_columns_to_work_msg_audit_info_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%work_msg_audit_info}}', 'content', $this->text()->comment('缩略消息')->after('roomid'));
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			$this->dropColumn('{{%work_msg_audit_info}}', 'content');
		}
	}
