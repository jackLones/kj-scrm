<?php

	use yii\db\Migration;

	/**
	 * Class m200813_055603_add_table_work_public_activity_fans_user_detail
	 */
	class m200813_055603_add_table_work_public_activity_fans_user_detail extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable("{{%work_public_activity_fans_user_detail}}", [
				"id"               => $this->primaryKey(11)->unsigned(),
				"activity_id"      => $this->integer(11)->unsigned()->comment("活动id"),
				"type"             => $this->integer(1)->unsigned()->comment("方式1老用户，2去关删除，3去关+删除，4删除"),
				"user_id"          => $this->integer(11)->unsigned()->comment("企业人员id"),
				"public_parent_id" => $this->integer(11)->unsigned()->comment("二维码归属人	"),
				"public_user_id"   => $this->integer(11)->unsigned()->comment("当前公众号人物"),
				"external_userid"  => $this->integer(11)->unsigned()->comment("外部联系人id"),
				"fans_id"          => $this->integer(11)->unsigned()->comment("公众号粉丝id"),
				"is_remind"        => $this->integer(11)->unsigned()->comment("是否提醒0未提醒，1已提醒"),
				"level_time"       => $this->integer(11)->unsigned()->comment("公众号取关||外部用户删除当前员工时间"),
				"create_time"      => $this->integer(11)->unsigned()->comment("创建时间"),
				"update_time"      => $this->integer(11)->unsigned()->comment("修改时间"),
			], "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='任务宝活动奖品用户领取奖品明细'");
			$this->addForeignKey('KEY_WORK_PUBLIC_ACTIVITY_PRIZE_FANS_USER_DETAIL', '{{%work_public_activity_fans_user_detail}}', 'activity_id', '{{%work_public_activity}}', 'id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200813_055603_add_table_work_public_activity_fans_user_detail cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200813_055603_add_table_work_public_activity_fans_user_detail cannot be reverted.\n";

			return false;
		}
		*/
	}
