<?php

	use yii\db\Migration;

	/**
	 * Class m210303_145759_change_auth_store_cloums
	 */
	class m210303_145759_change_auth_store_cloums extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->alterColumn("{{%auth_store}}", "shop_name", $this->string(80)->comment("店铺名称"));
			$this->alterColumn("{{%auth_store}}", "address", $this->string(255)->comment("地址"));
			$this->createIndex("AUTH_STORE_SHOP_NAME","{{%auth_store}}", "shop_name(8)");
			$this->createIndex("AUTH_STORE_ADDRESS","{{%auth_store}}", "address(10)");
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m210303_145759_change_auth_store_cloums cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{
	
		}
	
		public function down()
		{
			echo "m210303_145759_change_auth_store_cloums cannot be reverted.\n";
	
			return false;
		}
		*/
	}
