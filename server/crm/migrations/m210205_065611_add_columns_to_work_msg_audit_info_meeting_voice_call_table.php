<?php

	use yii\db\Migration;

	/**
	 * Handles adding columns to table `{{%work_msg_audit_info_meeting_voice_call}}`.
	 */
	class m210205_065611_add_columns_to_work_msg_audit_info_meeting_voice_call_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%work_msg_audit_info_meeting_voice_call}}', 'is_finish', $this->tinyInteger(1)->unsigned()->defaultValue(0)->comment('是否已结束：0未结束、1已结束')->after('file_path'));
			$this->addColumn('{{%work_msg_audit_info_meeting_voice_call}}', 'indexbuf', $this->string(255)->comment('索引缓冲')->after('is_finish'));
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			$this->dropColumn('{{%work_msg_audit_info_meeting_voice_call}}', 'is_finish');
			$this->dropColumn('{{%work_msg_audit_info_meeting_voice_call}}', 'indexbuf');
		}
	}
