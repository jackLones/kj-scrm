<?php

	use yii\db\Migration;

	/**
	 * Class m200521_112247_add_work_chat
	 */
	class m200521_112247_add_work_chat extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			// 客户群列表
			$this->createTable('{{%work_chat}}', [
				'id'          => $this->primaryKey(11)->unsigned(),
				'corp_id'     => $this->integer(11)->unsigned()->comment('企业ID'),
				'chat_id'     => $this->char(64)->notNull()->comment('客户群ID'),
				'name'        => $this->char(64)->comment('群名'),
				'owner_id'    => $this->integer(11)->unsigned()->comment('群主用户ID'),
				'owner'       => $this->char(64)->comment('群主ID'),
				'create_time' => $this->char(64)->comment('群的创建时间'),
				'notice'      => $this->string(255)->comment('群公告')
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'客户群列表\'');

			$this->createIndex('KEY_WORK_CHAT_CHATID', '{{%work_chat}}', 'chat_id');
			$this->createIndex('KEY_WORK_CHAT_NAME', '{{%work_chat}}', 'name');
			$this->createIndex('KEY_WORK_CHAT_CREATETIME', '{{%work_chat}}', 'create_time');

			$this->addForeignKey('KEY_WORK_CHAT_CORPID', '{{%work_chat}}', 'corp_id', '{{%work_corp}}', 'id');
			$this->addForeignKey('KEY_WORK_CHAT_OWNERID', '{{%work_chat}}', 'owner_id', '{{%work_user}}', 'id');

			// 客户群详细信息
			$this->createTable('{{%work_chat_info}}', [
				'id'          => $this->primaryKey(11)->unsigned(),
				'chat_id'     => $this->integer(11)->unsigned()->comment('客户群列表ID'),
				'user_id'     => $this->integer(11)->unsigned()->comment('成员用户ID'),
				'external_id' => $this->integer(11)->unsigned()->comment('外部联系人ID'),
				'userid'      => $this->char(64)->comment('群成员id（可能是内部成员，也可能是外部联系人）'),
				'type'        => $this->tinyInteger(1)->comment('成员类型。1 - 企业成员2 - 外部联系人'),
				'join_time'   => $this->char(64)->comment('入群时间'),
				'join_scene'  => $this->tinyInteger(1)->comment('入群方式。1 - 由成员邀请入群（直接邀请入群）2 - 由成员邀请入群（通过邀请链接入群）3 - 通过扫描群二维码入群'),
				'status'      => $this->tinyInteger(1)->comment('成员状态。1 - 正常；0 - 已离开'),
				'create_time' => $this->timestamp()->defaultExpression("CURRENT_TIMESTAMP")->comment('创建时间'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'客户群详细信息\'');

			$this->createIndex('KEY_WORK_CHAT_INFO_USERID', '{{%work_chat_info}}', 'userid');
			$this->createIndex('KEY_WORK_CHAT_INFO_TYPE', '{{%work_chat_info}}', 'type');
			$this->createIndex('KEY_WORK_CHAT_INFO_JOINTIME', '{{%work_chat_info}}', 'join_time');
			$this->createIndex('KEY_WORK_CHAT_INFO_JOINSCENE', '{{%work_chat_info}}', 'join_scene');
			$this->createIndex('KEY_WORK_CHAT_INFO_STATUS', '{{%work_chat_info}}', 'status');

			$this->addForeignKey('KEY_WORK_CHAT_info_chatid', '{{%work_chat_info}}', 'chat_id', '{{%work_chat}}', 'id');
			$this->addForeignKey('KEY_WORK_CHAT_info_work_userid', '{{%work_chat_info}}', 'user_id', '{{%work_user}}', 'id');
			$this->addForeignKey('KEY_WORK_CHAT_info_externalid', '{{%work_chat_info}}', 'external_id', '{{%work_external_contact}}', 'id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200521_112247_add_work_chat cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200521_112247_add_work_chat cannot be reverted.\n";

			return false;
		}
		*/
	}
