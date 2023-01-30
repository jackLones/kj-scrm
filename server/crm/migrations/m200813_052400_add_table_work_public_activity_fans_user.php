<?php

	use yii\db\Migration;

	/**
	 * Class m200813_052400_add_table_work_public_activity_fans_user_
	 */
	class m200813_052400_add_table_work_public_activity_fans_user extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable("{{%work_public_activity_fans_user}}", [
				"id"           => $this->primaryKey(11)->unsigned(),
				"corp_id"      => $this->integer(11)->unsigned()->comment("企业id"),
				"public_id"    => $this->integer(11)->unsigned()->comment("公众号id"),
				"activity_id"  => $this->integer(11)->unsigned()->comment("活动id"),
				"activity_num" => $this->integer(11)->defaultValue(0)->unsigned()->comment("活动完成人数"),
				"level"        => $this->integer(11)->unsigned()->comment("是否发送完成海报"),
				"tier"         => $this->integer(11)->unsigned()->comment("层级明细"),
				"parent_id"    => $this->integer(11)->unsigned()->comment("上级id"),
				"user_id"      => $this->integer(11)->unsigned()->comment("企业成员id"),
				"fans_id"      => $this->integer(11)->unsigned()->comment("粉丝id"),
				"is_tags"      => $this->integer(1)->unsigned()->comment("是否打标签"),
				"tags"         => $this->string(255)->unsigned()->comment("标签"),
				"prize"        => $this->integer(11)->unsigned()->comment("奖品id"),
				"is_form"      => $this->integer(1)->unsigned()->comment("是否填写表单"),
				"poster_path"  => $this->integer(11)->unsigned()->comment("生成海报素材地址"),
				"success_time" => $this->integer(11)->unsigned()->comment("完成时间"),
				"create_time"  => $this->integer(11)->unsigned()->comment("创建时间"),
				"update_time"  => $this->integer(11)->unsigned()->comment("修改时间"),
			], "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='任务宝活动关注粉丝明细'");
			$this->addForeignKey('KEY_WORK_PUBLIC_ACTIVITY_FANS_USER_DETAIL', '{{%work_public_activity_fans_user}}', 'activity_id', '{{%work_public_activity}}', 'id');
			$this->addForeignKey('KEY_WORK_PUBLIC_ACTIVITY_USER_DETAIL_USER_ID', '{{%work_public_activity_fans_user}}', 'user_id', '{{%work_user}}', 'id');
			$this->addForeignKey('KEY_WORK_PUBLIC_ACTIVITY_USER_DETAIL_CORP_ID', '{{%work_public_activity_fans_user}}', 'corp_id', '{{%work_corp}}', 'id');

		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200813_052400_add_table_work_public_activity_fans_user_ cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200813_052400_add_table_work_public_activity_fans_user_ cannot be reverted.\n";

			return false;
		}
		*/
	}
