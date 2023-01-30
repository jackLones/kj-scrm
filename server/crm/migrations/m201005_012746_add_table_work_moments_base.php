<?php

	use yii\db\Migration;

	/**
	 * Class m201005_012746_add_table_work_moments
	 */
	class m201005_012746_add_table_work_moments_base extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable("{{%work_moments_base}}", [
				"id"               => $this->primaryKey(),
				"corp_id"          => $this->integer()->unsigned()->comment("企业id"),
				"sub_id"           => $this->integer()->unsigned()->comment("子账户id"),
				"title"            => $this->string(255)->notNull()->comment("标题"),
				"ownership"        => $this->text()->comment("归属成员"),
				"condition"        => $this->text()->comment("条件[]代表全部"),
				"advanced_setting" => $this->integer(1)->unsigned()->defaultValue(0)->comment("同步设置,1开，0关"),
				"send_time"        => $this->string(30)->comment("发送时间null立即"),
				"type"             => $this->integer(1)->unsigned()->comment("类型：1、仅文本；2、图片；3、视频；4、链接"),
				"context"          => $this->text()->comment("朋友圈内容"),
				"status"           => $this->integer(1)->unsigned()->comment("审核状态,主账户无需审核，子账户需审核"),
				"info"             => $this->text()->unsigned()->comment("info"),
				"is_del"           => $this->integer(1)->defaultValue(0)->unsigned()->comment("是否删除"),
				"send_success"     => $this->integer(1)->defaultValue(0)->unsigned()->comment("0失败,1成功,2待发送"),
				"create_time"      => $this->integer(11)->unsigned(),
				"update_time"      => $this->integer(11)->unsigned(),
			]);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m201005_012746_add_table_work_moments cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m201005_012746_add_table_work_moments cannot be reverted.\n";

			return false;
		}
		*/
	}
