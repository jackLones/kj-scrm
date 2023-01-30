<?php

	use yii\db\Migration;

	/**
	 * Handles adding columns to table `{{%work_msg_audit_info_image}}`.
	 */
	class m210205_064238_add_columns_to_work_msg_audit_info_image_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%work_msg_audit_info_image}}', 'indexbuf', $this->string(255)->comment('索引缓冲')->after('is_finish'));
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			$this->dropColumn('{{%work_msg_audit_info_image}}', 'indexbuf');
		}
	}
