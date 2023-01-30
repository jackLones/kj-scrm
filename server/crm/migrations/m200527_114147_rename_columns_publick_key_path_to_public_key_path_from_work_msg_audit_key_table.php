<?php

	use yii\db\Migration;

	/**
	 * Class m200527_114147_rename_publick_key_path_to_public_key_path_from_work_msg_audit_key_table
	 */
	class m200527_114147_rename_columns_publick_key_path_to_public_key_path_from_work_msg_audit_key_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->renameColumn('{{%work_msg_audit_key}}', 'publick_key_path', 'public_key_path');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			$this->renameColumn('{{%work_msg_audit_key}}', 'public_key_path', 'publick_key_path');
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200527_114147_rename_publick_key_path_to_public_key_path_from_work_msg_audit_key_table cannot be reverted.\n";

			return false;
		}
		*/
	}
