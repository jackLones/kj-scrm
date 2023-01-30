<?php

	use yii\db\Migration;

	/**
	 * Class m210129_031028_add_table_update_batch
	 */
	class m210129_031028_add_table_update_batch extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			\app\models\PublicSeaCustomer::updateBatch();
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m210129_031028_add_table_update_batch cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m210129_031028_add_table_update_batch cannot be reverted.\n";

			return false;
		}
		*/
	}
