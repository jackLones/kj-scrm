<?php

	use yii\db\Migration;

	/**
	 * Class m201014_054129_add_table_work_public_activity_url
	 */
	class m201014_054129_add_table_work_public_activity_url extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable("{{%work_public_activity_url}}", [
				"id"          => $this->primaryKey(),
				"short_url"   => $this->string(60)->notNull()->comment("短连接"),
				"url"         => $this->string(255)->notNull()->comment("原始连接"),
				"create_time" => $this->integer(11)->unsigned()->notNull(),
			]);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m201014_054129_add_table_work_public_activity_url cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m201014_054129_add_table_work_public_activity_url cannot be reverted.\n";

			return false;
		}
		*/
	}
