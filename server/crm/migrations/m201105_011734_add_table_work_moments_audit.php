<?php

	use yii\db\Migration;

	/**
	 * Class m201105_011734_change_table_work_moments_base
	 */
	class m201105_011734_add_table_work_moments_audit extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->alterColumn("{{%work_moments_base}}", "status", $this->integer(1)->unsigned()->defaultValue(1)->comment("1已审核,2未审核,3审核失败"));
			$this->addColumn("{{%work_moments_base}}", "audit_id", $this->integer(11)->unsigned()->comment("审核表id"));
			$this->createTable("{{%work_moments_audit}}", [
				"id"           => $this->primaryKey()->unsigned(),
				"audit_people" => $this->integer()->unsigned()->comment("审核人id"),
				"type"         => $this->integer()->unsigned()->comment("1主账户，2子账户，3员工"),
				"reply"        => $this->text()->comment("回复"),
				"create_time"  => $this->timestamp()->comment("创建时间"),
			], "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='朋友圈审核明细表'");

		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m201105_011734_change_table_work_moments_base cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m201105_011734_change_table_work_moments_base cannot be reverted.\n";

			return false;
		}
		*/
	}
