<?php

	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%auth_store_user}}`.
	 */
	class m210119_021611_create_auth_store_user_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%auth_store_user}}', [
				'id'          => $this->primaryKey()->unsigned(),
				"store_id"    => $this->integer(11)->unsigned()->comment("店铺id"),
				"user_id"     => $this->integer(11)->unsigned()->comment("员工id"),
				"status"      => $this->tinyInteger(1)->defaultValue(1)->comment("1正常0取消"),
				"qc_url"      => $this->char(255)->defaultValue(NULL)->comment("渠道码"),
				"config_id"   => $this->char(80)->defaultValue(NULL)->comment("渠道码config"),
				"create_time" => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'店铺成员\'');
			$this->addForeignKey("AUTH_STORE_USER_STORE_ID", "{{%auth_store_user}}", "store_id", "{{%auth_store}}", "id", "CASCADE");
			$this->addForeignKey("AUTH_STORE_USER_USER_ID", "{{%auth_store_user}}", "user_id", "{{%work_user}}", "id", "CASCADE");
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			$this->dropTable('{{%auth_store_user}}');
		}
	}
