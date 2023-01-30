<?php

	use yii\db\Migration;

	/**
	 * Class m200822_060118_add_some_indexs_to_external_time_line_table
	 */
	class m200822_060118_add_some_indexs_to_external_time_line_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createIndex('KEY_EXTERNAL_TIME_LINE_EVENT_RELATEDID_OPENID', '{{%external_time_line}}', ['event', 'related_id', 'openid(8)']);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			$this->dropIndex('KEY_EXTERNAL_TIME_LINE_EVENT_RELATEDID_OPENID', '{{%external_time_line}}');
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200822_060118_add_some_indexs_to_external_time_line_table cannot be reverted.\n";

			return false;
		}
		*/
	}
