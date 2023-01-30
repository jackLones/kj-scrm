<?php

	use yii\db\Migration;

	/**
	 * Class m201124_080410_add_index_into_custom_filed_table
	 */
	class m201124_080410_add_index_into_custom_filed_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createIndex('KEY_CUSTOM_FILED_ISDEFINE', '{{%custom_field}}', 'is_define');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			$this->dropIndex('KEY_CUSTOM_FILED_ISDEFINE', '{{%custom_field}}');
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m201124_080410_add_index_into_custom_filed_table cannot be reverted.\n";

			return false;
		}
		*/
	}
