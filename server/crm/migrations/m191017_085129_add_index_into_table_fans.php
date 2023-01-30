<?php

	use yii\db\Migration;

	/**
	 * Class m191017_085129_add_index_into_table_fans
	 */
	class m191017_085129_add_index_into_table_fans extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createIndex('KEY_FANS_MSG_CREATETIME', '{{%fans_msg}}', 'create_time');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m191017_085129_add_index_into_table_fans cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m191017_085129_add_index_into_table_fans cannot be reverted.\n";

			return false;
		}
		*/
	}
