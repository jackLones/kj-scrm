<?php

	use yii\db\Migration;

	/**
	 * Handles adding columns to table `{{%work_msg_audit_info_meeting_voice_call}}`.
	 */
	class m210205_065639_add_columns_to_work_msg_audit_info_voip_doc_share_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%work_msg_audit_info_voip_doc_share}}', 'is_finish', $this->tinyInteger(1)->unsigned()->defaultValue(0)->comment('是否已结束：0未结束、1已结束')->after('sdkfileid'));
			$this->addColumn('{{%work_msg_audit_info_voip_doc_share}}', 'indexbuf', $this->string(255)->comment('索引缓冲')->after('is_finish'));
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			$this->dropColumn('{{%work_msg_audit_info_voip_doc_share}}', 'is_finish');
			$this->dropColumn('{{%work_msg_audit_info_voip_doc_share}}', 'indexbuf');
		}
	}
