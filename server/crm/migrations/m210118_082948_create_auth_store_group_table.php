<?php

	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%auth_store_group}}`.
	 */
	class m210118_082948_create_auth_store_group_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%auth_store_group}}', [
				'id'          => $this->primaryKey()->unsigned(),
				'pid'         => $this->integer(11)->unsigned()->comment("上级分组id"),
				"uid"         => $this->integer(11)->unsigned()->comment("账户id"),
				"corp_id"     => $this->integer(11)->unsigned()->comment("企业微信id"),
				"name"        => $this->char(60)->comment("分组名称"),
				"status"      => $this->tinyInteger(1)->defaultValue(1)->comment("状态"),
				"sort"        => $this->integer(11)->comment("排序"),
				"parent_ids"  => $this->text()->comment("所有上级关系"),
				"create_time" => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
				"update_time" => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('修改时间'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'店铺分组表\'');
			$this->createIndex("AUTH_STORE_GROUP_UID", "{{%auth_store_group}}", "uid");
			$this->createIndex("AUTH_STORE_GROUP_PID", "{{%auth_store_group}}", "pid");
			$this->addForeignKey("AUTH_STORE_GROUP_CORP_ID", "{{%auth_store_group}}", "corp_id", "{{%work_corp}}", "id", "CASCADE");

		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			$this->dropTable('{{%auth_store_group}}');
		}
	}
