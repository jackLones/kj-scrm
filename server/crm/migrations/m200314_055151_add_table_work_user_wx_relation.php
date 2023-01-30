<?php

	use yii\db\Migration;

	/**
	 * Class m200314_055151_add_table_work_user_wx_relation
	 */
	class m200314_055151_add_table_work_user_wx_relation extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_user_author_relation}}', [
				'id'          => $this->primaryKey(11)->unsigned(),
				'corp_id'     => $this->integer(11)->unsigned()->comment('企业微信ID'),
				'user_id'     => $this->integer(11)->unsigned()->comment('企业微信成员ID'),
				'author_id'   => $this->integer(11)->unsigned()->comment('公众号ID'),
				'agent_id'    => $this->integer(11)->unsigned()->comment('企业微信应用ID'),
				'status'      => $this->tinyInteger(1)->unsigned()->defaultValue(1)->comment('状态：0、关闭；1：开启'),
				'create_time' => $this->timestamp()->defaultExpression("CURRENT_TIMESTAMP")->comment('创建时间'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'企业微信成员接收公众号粉丝消息绑定关系表\'');

			$this->addForeignKey('KEY_WORK_USER_AUTHOR_RELATION_CORPID', '{{%work_user_author_relation}}', 'corp_id', '{{%work_corp}}', 'id');
			$this->addForeignKey('KEY_WORK_USER_AUTHOR_RELATION_USERID', '{{%work_user_author_relation}}', 'user_id', '{{%work_user}}', 'id');
			$this->addForeignKey('KEY_WORK_USER_AUTHOR_RELATION_AUTHORID', '{{%work_user_author_relation}}', 'author_id', '{{%wx_authorize}}', 'author_id');
			$this->addForeignKey('KEY_WORK_USER_AUTHOR_RELATION_AGENTID', '{{%work_user_author_relation}}', 'agent_id', '{{%work_corp_agent}}', 'id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200314_055151_add_table_work_user_wx_relation cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200314_055151_add_table_work_user_wx_relation cannot be reverted.\n";

			return false;
		}
		*/
	}
