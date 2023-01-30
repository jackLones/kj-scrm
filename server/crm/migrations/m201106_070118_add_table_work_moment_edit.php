<?php

	use yii\db\Migration;

	/**
	 * Class m201106_070117_add_table_work_moment_edit
	 */
	class m201106_070118_add_table_work_moment_edit extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable("{{%work_moment_edit}}", [
				"id"          => $this->primaryKey()->unsigned(),
				"corp_id"     => $this->integer(11)->unsigned()->comment("企业id"),
				"user_id"     => $this->integer(11)->unsigned()->comment("员工id"),
				"status"      => $this->tinyInteger(1)->defaultValue(2)->comment("1完成2正在编辑"),
				"info"        => $this->text()->comment("保存编辑内容"),
				"create_time" => $this->timestamp()->defaultExpression("CURRENT_TIMESTAMP")
			],"ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='朋友圈保存编辑'");
			$this->addForeignKey("WORK_MOMENT_EDIT_CORP_ID", "{{%work_moment_edit}}", "corp_id", "{{%work_corp}}", "id");
			$this->addForeignKey("WORK_MOMENT_EDIT_USER_ID", "{{%work_moment_edit}}", "user_id", "{{%work_user}}", "id");
			$this->createIndex("WORK_MOMENT_EDIT_STATUS", "{{%work_moment_edit}}", "status");
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m201106_070117_add_table_work_moment_edit cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m201106_070117_add_table_work_moment_edit cannot be reverted.\n";

			return false;
		}
		*/
	}
