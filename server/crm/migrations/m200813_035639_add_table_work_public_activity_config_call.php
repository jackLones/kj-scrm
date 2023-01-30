<?php

	use yii\db\Migration;

	/**
	 * Class m200813_035639_add_table_work_public_activity_config_call
	 */
	class m200813_035639_add_table_work_public_activity_config_call extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable("{{%work_public_activity_config_call}}", [
				"id"               => $this->primaryKey(11)->unsigned(),
				"activity_id"      => $this->integer(11)->unsigned()->comment("活动id"),
				"is_open"          => $this->integer(1)->defaultValue(1)->unsigned()->comment("是否开启默认1开启"),
				"type"             => $this->integer(2)->unsigned()->comment("1新成员加入提醒，2好友取关扣除人气提醒，3任务完成提醒，4任务完成推送图片，5重复参加提醒，6好友助力成功提醒，7好友重复助力提醒，8其他提醒，9免打扰任务提醒，10活动下线提醒，11活动完成后继续有新成员"),
				"is_url"           => $this->integer(1)->defaultValue(0)->unsigned()->comment("1排行榜2兑换链接（文本），1使用"),
				"is_template"      => $this->integer(2)->defaultValue(0)->unsigned()->comment("是否使用模板0不使用（文本），1使用"),
				"template_id"      => $this->string(255)->unsigned()->comment("模板id"),
				"template_context" => $this->text()->unsigned()->comment("模板内容"),
				"context"          => $this->text()->unsigned()->comment("回复内容"),
				"img_url"          => $this->string(255)->unsigned()->comment("推送图片"),
				"create_time"      => $this->integer(11)->unsigned()->comment("创建时间"),
				"update_time"      => $this->integer(11)->unsigned()->comment("修改时间"),
			], "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='任务宝活动回复'");

			$this->addForeignKey('KEY_WORK_PUBLIC_ACTIVITY_CONFIG_CALL', '{{%work_public_activity_config_call}}', 'activity_id', '{{%work_public_activity}}', 'id');

		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200813_035639_add_table_work_public_activity_config_call cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200813_035639_add_table_work_public_activity_config_call cannot be reverted.\n";

			return false;
		}
		*/
	}
