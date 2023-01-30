<?php

	use yii\db\Migration;

	/**
	 * Handles adding columns to table `{{%work_msg_audit}}`.
	 */
	class m200529_054156_add_seq_columns_to_work_msg_audit_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%work_msg_audit}}', 'seq', $this->integer(64)->unsigned()->defaultValue(0)->after('status')->comment('从指定的seq开始拉取消息，注意的是返回的消息从seq+1开始返回，seq为之前接口返回的最大seq值。首次使用请使用seq：0'));
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			$this->dropColumn('{{%work_msg_audit}}', 'seq');
		}
	}
