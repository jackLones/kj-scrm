<?php

	use yii\db\Migration;

	/**
	 * Handles adding columns to table `{{%work_msg_audit_info_agree}}`.
	 */
	class m200529_084734_add_agree_type_columns_to_work_msg_audit_info_agree_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%work_msg_audit_info_agree}}', 'agree_type', $this->tinyInteger(1)->after('userid')->comment('是否同意：0、不同意；1、同意'));
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			$this->dropColumn('{{%work_msg_audit_info_agree}}', 'agree_type');
		}
	}
