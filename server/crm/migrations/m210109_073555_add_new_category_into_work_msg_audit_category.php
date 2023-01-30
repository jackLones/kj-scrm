<?php

	use yii\db\Migration;

	/**
	 * Class m210109_073555_add_new_category_into_work_msg_audit_category
	 */
	class m210109_073555_add_new_category_into_work_msg_audit_category extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->batchInsert('{{%work_msg_audit_category}}', ['category_type', 'category_name'], [
				['meeting_voice_call', '音频存档'],
				['voip_doc_share', '音频共享文档'],
				['external_redpacket', '互通红包'],
			]);

		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m210109_073555_add_new_category_into_work_msg_audit_category cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m210109_073555_add_new_category_into_work_msg_audit_category cannot be reverted.\n";

			return false;
		}
		*/
	}
