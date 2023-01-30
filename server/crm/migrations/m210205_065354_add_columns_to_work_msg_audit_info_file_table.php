<?php

	use yii\db\Migration;

	/**
	 * Handles adding columns to table `{{%work_msg_audit_info_file}}`.
	 */
	class m210205_065354_add_columns_to_work_msg_audit_info_file_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%work_msg_audit_info_file}}', 'indexbuf', $this->string(255)->comment('索引缓冲')->after('is_finish'));
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			$this->dropColumn('{{%work_msg_audit_info_file}}', 'indexbuf');
		}
	}
