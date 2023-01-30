<?php

	use yii\db\Migration;

	/**
	 * Class m200831_050537_add_table_work_public_activity_statistic
	 */
	class m200831_050537_add_table_work_public_activity_statistic extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable("{{%work_public_activity_statistic}}", [
				"id"            => $this->primaryKey(11)->unsigned(),
				"activity_id"   => $this->integer(11)->unsigned()->comment("活动id"),
				"time"          => $this->char(60)->unsigned()->comment("时间"),
				"new_fans"      => $this->integer(11)->unsigned()->comment("新增"),
				"participation" => $this->integer(11)->unsigned()->comment("净增粉丝"),
				"now_not_day"   => $this->integer(11)->unsigned()->comment("取关粉丝"),
				"net_fans"      => $this->integer(11)->unsigned()->comment("参与粉丝"),
				"success"       => $this->integer(11)->unsigned()->comment("完成任务"),
				"new_add"       => $this->integer(11)->unsigned()->comment("新添加好友"),
				"lose_fans"     => $this->integer(11)->unsigned()->comment("流失好友"),
				"keep"          => $this->integer(11)->unsigned()->comment("好友留存率"),
				"type"          => $this->integer(1)->unsigned()->comment("1天，2周，3月"),
			], "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='任务宝活动奖品用户领取奖品明细'");
			$this->addForeignKey('KEY_WORK_PUBLIC_ACTIVITY_STATISTIC', '{{%work_public_activity_statistic}}', 'activity_id', '{{%work_public_activity}}', 'id');

		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200831_050537_add_table_work_public_activity_statistic cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200831_050537_add_table_work_public_activity_statistic cannot be reverted.\n";

			return false;
		}
		*/
	}
