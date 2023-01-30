<?php

	use yii\db\Migration;

	/**
	 * Class m210303_152116_change_auth_store_group_cloums
	 */
	class m210303_152116_change_auth_store_group_cloums extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->alterColumn("{{%auth_store_group}}", "name", $this->string(60)->comment("分组名称"));
			$this->createIndex("AUTH_STORE_GROUP_NAME", "{{%auth_store_group}}", "name(8)");
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m210303_152116_change_auth_store_group_cloums cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m210303_152116_change_auth_store_group_cloums cannot be reverted.\n";

			return false;
		}
		*/
	}
