<?php

	use yii\db\Migration;

	/**
	 * Class m210109_074717_change_work_msg_audit_category_category_type_columns
	 */
	class m210109_072717_change_work_msg_audit_category_category_type_columns extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->alterColumn('{{%work_msg_audit_category}}', 'category_type', $this->string(64)->comment('类别标识'));
			$this->alterColumn('{{%work_msg_audit_category}}', 'category_name', $this->string(64)->comment('类别名称'));
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			$this->alterColumn('{{%work_msg_audit_category}}', 'category_type', $this->char(16)->comment('类别标识'));
			$this->alterColumn('{{%work_msg_audit_category}}', 'category_name', $this->char(16)->comment('类别名称'));
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m210109_074717_change_work_msg_audit_category_category_type_columns cannot be reverted.\n";

			return false;
		}
		*/
	}
