<?php

	use yii\db\Migration;

	/**
	 * Class m200510_024950_add_key_into_fans
	 */
	class m200510_024950_add_key_into_fans extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createIndex('KEY_FANS_FOLLOWSTATUS', '{{%fans}}', 'follow_status');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200510_024950_add_key_into_fans cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200510_024950_add_key_into_fans cannot be reverted.\n";

			return false;
		}
		*/
	}
