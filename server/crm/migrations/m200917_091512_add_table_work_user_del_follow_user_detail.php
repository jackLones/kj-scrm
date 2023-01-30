<?php

	use yii\db\Migration;

	/**
	 * Class m200917_091512_add_table_work_user_del_follow_user_detail
	 */
	class m200917_091512_add_table_work_user_del_follow_user_detail extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable("{{%work_user_del_follow_user_detail}}", [
				"id"              => $this->primaryKey(11)->unsigned(),
				"corp_id"         => $this->integer(11)->unsigned()->comment("企业应用id"),
				"user_id"         => $this->integer(11)->unsigned()->comment("员工id"),
				"external_userid" => $this->integer(11)->unsigned()->comment("外部联系人id"),
				"repetition"      => $this->integer(11)->unsigned()->comment("是否重复删除"),
				"create_time"     => $this->integer(11)->unsigned(),
				"update_time"     => $this->integer(11)->unsigned(),
			], "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='员工删除外部联系人明細'");
			$this->addForeignKey('KEY_WORK_USER_DEL_FOLLOW_DETAIL_USER_CORP_ID', '{{%work_user_del_follow_user_detail}}', 'corp_id', '{{%work_corp}}', 'id');
			$this->addForeignKey('KEY_WORK_USER_DEL_FOLLOW_DETAIL_USER_USER_ID', '{{%work_user_del_follow_user_detail}}', 'user_id', '{{%work_user}}', 'id');
			$this->addForeignKey('KEY_WORK_USER_DEL_FOLLOW_DETAIL_external_userid', '{{%work_user_del_follow_user_detail}}', 'external_userid', '{{%work_external_contact}}', 'id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200917_091512_add_table_work_user_del_follow_user_detail cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200917_091512_add_table_work_user_del_follow_user_detail cannot be reverted.\n";

			return false;
		}
		*/
	}
