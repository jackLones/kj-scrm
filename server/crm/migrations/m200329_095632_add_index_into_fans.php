<?php

	use yii\db\Migration;

	/**
	 * Class m200329_095632_add_index_into_fans
	 */
	class m200329_095632_add_index_into_fans extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createIndex('KEY_FANS_SUBCRIBE_AUTHORID_LASTTIME', '{{%fans}}', ['subscribe', 'author_id', 'last_time']);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200329_095632_add_index_into_fans cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200329_095632_add_index_into_fans cannot be reverted.\n";

			return false;
		}
		*/
	}
