<?php

	use yii\db\Migration;

	/**
	 * Class m200813_032209_add_table_work_public_activity_config_level
	 */
	class m200813_032209_add_table_work_public_activity_config_level extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable("{{%work_public_activity_config_level}}", [
				"id"              => $this->primaryKey(11)->unsigned(),
				"activity_id"     => $this->integer(11)->unsigned()->comment("活动id"),
				"is_open"         => $this->integer(1)->unsigned()->comment("是否开启"),
				"level"           => $this->integer(1)->unsigned()->comment("等级"),
				"prize_name"      => $this->char(60)->unsigned()->comment("奖品名称"),
				"money_amount"    => $this->decimal(10, 2)->unsigned()->comment("红包金额"),
				"money_count"     => $this->integer(11)->unsigned()->comment("红包数量"),
				"number"          => $this->integer(11)->unsigned()->comment("助力次数"),
				"num"             => $this->integer(11)->unsigned()->comment("奖品数量"),
				"money_count_old" => $this->integer(11)->unsigned()->comment("常量库存"),
				"num_old"         => $this->integer(11)->unsigned()->comment("常量库存"),
				"create_time"     => $this->integer(11)->unsigned()->comment("修改时间"),
				"update_time"     => $this->integer(11)->unsigned()->comment("修改时间"),
			], "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='任务宝阶段配置'");

			$this->addForeignKey('KEY_WORK_PUBLIC_ACTIVITY_CONFIG_LEVEL', '{{%work_public_activity_config_level}}', 'activity_id', '{{%work_public_activity}}', 'id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200813_032209_add_table_work_public_activity_config_level cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200813_032209_add_table_work_public_activity_config_level cannot be reverted.\n";

			return false;
		}
		*/
	}
