<?php

	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%follow_lose_msg}}`.
	 */
	class m210115_053105_create_follow_loser_msg_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%follow_lose_msg}}', [
				'id'          => $this->primaryKey(11)->unsigned(),
				"corp_id"     => $this->integer(11)->unsigned()->comment("企业id"),
				"uid"         => $this->integer(11)->unsigned()->comment("主账号id"),
				"sub_id"      => $this->integer(11)->unsigned()->comment("子账号id"),
				"user_id"     => $this->integer(11)->unsigned()->comment("成员id"),
				"context"     => $this->text()->comment("原因"),
				"status"      => $this->tinyInteger(1)->comment("状态"),
				"sort"        => $this->tinyInteger(3)->comment("排序"),
				"create_time" => $this->integer(11)->comment("创建时间"),
				"update_time" => $this->integer(11)->comment("修改时间"),
			]);
			$this->createIndex("WORK_FOLLOW_LOSER_MSG_UID", "{{%follow_lose_msg}}", "uid");
			$this->addForeignKey("WORK_FOLLOW_LOSER_MSG_SUB_ID", "{{%follow_lose_msg}}", "sub_id", "{{%sub_user}}", "sub_id", "CASCADE");
			$this->addForeignKey("WORK_FOLLOW_LOSER_MSG_USER_ID", "{{%follow_lose_msg}}", "user_id", "{{%work_user}}", "id", "CASCADE");
			$this->addForeignKey("WORK_FOLLOW_LOSER_MSG_CORP_ID", "{{%follow_lose_msg}}", "corp_id", "{{%work_corp}}", "id", "CASCADE");

		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			$this->dropIndex("WORK_FOLLOW_LOSER_MSG_UID", "{{%follow_lose_msg}}");
			$this->dropForeignKey("WORK_FOLLOW_LOSER_MSG_SUB_ID", "{{%follow_lose_msg}}");
			$this->dropForeignKey("WORK_FOLLOW_LOSER_MSG_USER_ID", "{{%follow_lose_msg}}");
			$this->dropForeignKey("WORK_FOLLOW_LOSER_MSG_CORP_ID", "{{%follow_lose_msg}}");
			$this->dropTable('{{%follow_lose_msg}}');

		}
	}
