<?php

	use yii\db\Migration;

	/**
	 * Class m200813_025901_add_table_work_public_activity
	 */
	class m200813_025901_add_table_work_public_activity extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable("{{%work_public_activity}}", [
				"id"               => $this->primaryKey(11)->unsigned(),
				"type"             => $this->integer(11)->unsigned()->comment("1公众号，2企业微信，3企业+公众号"),
				"uid"              => $this->integer(11)->unsigned()->comment("企业uid"),
				"sub_id"           => $this->integer(11)->unsigned()->comment("子账户id"),
				"corp_agent"       => $this->integer(11)->unsigned()->comment("应用id"),
				"corp_id"          => $this->integer(11)->unsigned()->comment("企业id"),
				"public_id"        => $this->integer(11)->unsigned()->comment("公众号id"),
				"is_over"          => $this->integer(1)->defaultValue(1)->unsigned()->comment("1未结束，2时间结束，3阶段结束"),
				"activity_name"    => $this->string(255)->unsigned()->comment("活动名称"),
				"activity_rule"    => $this->text()->unsigned()->comment("活动规则"),
				"describe"         => $this->text()->unsigned()->comment("描述"),
				"poster_open"      => $this->integer(1)->defaultValue(1)->unsigned()->comment("活动海报描述1发送2不发送"),
				"poster_describe"  => $this->text()->unsigned()->comment("海报描述"),
				"sex_type"         => $this->integer(1)->unsigned()->comment("性别参与 1男，2女，3未知，4不限制"),
				"region_type"      => $this->integer(1)->unsigned()->comment("1不限，2指定地区 "),
				"region"           => $this->text()->unsigned()->comment("地区"),
				"label_id"         => $this->string(255)->unsigned()->comment("标签id"),
				"mutual"           => $this->integer(1)->unsigned()->comment("是否允许互助"),
				"number"           => $this->integer(3)->defaultValue(0)->unsigned()->comment("活动主力次数默认0不限制(单人)"),
				"keyword"          => $this->char(255)->unsigned()->comment("关键词触发"),
				"not_attention"    => $this->integer(1)->defaultValue(1)->unsigned()->comment("1不扣除,2取关扣除人数，3取关删除扣除（企业），4删除（企业）"),
				"action_type"      => $this->integer(1)->defaultValue(1)->unsigned()->comment("2客服领取1H5领取"),
				"channel_user"     => $this->text()->unsigned()->comment("企业微信员工id生成渠道码"),
				"channel_user_id"  => $this->text()->unsigned()->comment("企业员工id"),
				"user_key"         => $this->text()->unsigned()->comment("客服员工id"),
				"hfive_url"        => $this->string(255)->unsigned()->comment("H5地址"),
				"hfive_config"     => $this->string(255)->unsigned()->comment("H5配置"),
				"qc_url"           => $this->string(255)->unsigned()->comment("渠道活码"),
				"user_url"         => $this->string(255)->unsigned()->comment("客服活码"),
				"code_url"         => $this->string(255)->unsigned()->comment("活动二维码"),
				"qr_scene_str"     => $this->string(255)->unsigned()->comment("活动二维码携带参数"),
				"welcome"          => $this->text()->notNull()->unsigned()->comment("企业欢迎语"),
				"welcome_url"      => $this->string(255)->notNull()->unsigned()->comment("企业图文"),
				"welcome_title"    => $this->string(255)->notNull()->unsigned()->comment("企业图片标题"),
				"welcome_describe" => $this->text()->notNull()->unsigned()->comment("企业描述"),
				"tickets_start"    => $this->integer(11)->unsigned()->comment("兑奖开始时间"),
				"tickets_end"      => $this->integer(11)->unsigned()->comment("兑奖结束时间"),
				"start_time"       => $this->integer(11)->unsigned()->comment("开始时间"),
				"end_time"         => $this->integer(11)->unsigned()->comment("结束时间"),
				"flow"             => $this->integer(1)->defaultValue(1)->unsigned()->comment("流程1直接发放资格，2选择奖品"),
				"level_end"        => $this->char(60)->unsigned()->comment("阶段结束"),
				"create_time"      => $this->integer(11)->unsigned()->comment("创建时间"),
				"update_time"      => $this->integer(11)->unsigned()->comment("修改时间"),
			], "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='任务宝活动名称表'");
			$this->addForeignKey('KEY_WORK_PUBLIC_ACTIVITY_SUB_ID', '{{%work_public_activity}}', 'sub_id', '{{%sub_user}}', 'sub_id');
			$this->addForeignKey('KEY_WORK_PUBLIC_ACTIVITY_UID', '{{%work_public_activity}}', 'uid', '{{%user}}', 'uid');
			$this->addForeignKey('KEY_WORK_PUBLIC_ACTIVITY_CORP_ID', '{{%work_public_activity}}', 'corp_id', '{{%work_corp}}', 'id');

		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200813_025901_add_table_work_public_activity cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200813_025901_add_table_work_public_activity cannot be reverted.\n";

			return false;
		}
		*/
	}
