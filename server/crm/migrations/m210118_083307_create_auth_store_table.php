<?php

	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%auth_store}}`.
	 */
	class m210118_083307_create_auth_store_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%auth_store}}', [
				'id'          => $this->primaryKey()->unsigned(),
				"group_id"    => $this->integer(11)->unsigned()->comment("分组id"),
				"pid"         => $this->integer(11)->unsigned()->comment("上级店铺"),
				"uid"         => $this->integer(11)->unsigned()->comment("主账户id"),
				"sub_id"      => $this->integer(11)->unsigned()->comment("子账户id"),
				"corp_id"     => $this->integer(11)->unsigned()->comment("企业微信id"),
				"manger_id"   => $this->integer(11)->unsigned()->comment("店长id"),
				"shop_name"   => $this->char(80)->comment("店铺名称"),
				"describe"    => $this->text()->comment("店铺描述"),
				"status"      => $this->integer(1)->comment("店铺状态"),
				"auth_id"     => $this->text()->comment("店铺权限"),
				"province"    => $this->text()->comment("省"),
				"city"        => $this->text()->comment("市"),
				"district"    => $this->text()->comment("区|县"),
				"address"     => $this->char(255)->comment("地址"),
				"lat"         => $this->char(20)->comment("纬度"),
				"lng"         => $this->char(20)->comment("经度"),
				"qc_url"      => $this->char(255)->defaultValue(NULL)->comment("渠道码"),
				"config_id"   => $this->char(80)->defaultValue(NULL)->comment("渠道码config"),
				"is_del"      => $this->integer(1)->defaultValue(0)->comment("0未删除1删除"),
				"create_time" => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
				"update_time" => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'店铺明细\'');

			$this->createIndex("AUTH_STORE_UID", "{{%auth_store}}", "uid");
			$this->createIndex("AUTH_STORE_PID", "{{%auth_store}}", "pid");
			$this->addForeignKey("AUTH_STORE_SUB_ID", "{{%auth_store}}", "sub_id", "{{%sub_user}}", "sub_id", "SET NULL");
			$this->addForeignKey("AUTH_STORE_CORP_ID", "{{%auth_store}}", "corp_id", "{{%work_corp}}", "id", "CASCADE");
			$this->addForeignKey("AUTH_STORE_MANGER_ID", "{{%auth_store}}", "manger_id", "{{%work_user}}", "id", "SET NULL");
			$this->addForeignKey("AUTH_STORE_GROUP_ID", "{{%auth_store}}", "group_id", "{{%auth_store_group}}", "id", "CASCADE");
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			$this->dropTable('{{%auth_store}}');
		}
	}
