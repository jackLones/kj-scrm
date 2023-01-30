<?php

	use yii\db\Migration;

	/**
	 * Class m200212_060603_add_table_work_corp_auth
	 */
	class m200212_060603_add_table_work_corp_auth extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_corp_auth}}', [
				'id'                   => $this->primaryKey(11)->unsigned(),
				'suite_id'             => $this->integer(11)->unsigned()->comment('应用ID'),
				'corp_id'              => $this->integer(11)->unsigned()->comment('企业ID'),
				'access_token'         => $this->string(255)->comment('授权方（企业）access_token'),
				'access_token_expires' => $this->char(16)->comment('授权方（企业）access_token超时时间'),
				'permanent_code'       => $this->string(255)->comment('企业微信永久授权码'),
				'auth_user_info'       => $this->string(255)->comment('授权管理员的信息'),
				'dealer_corp_info'     => $this->string(255)->comment('代理服务商企业信息'),
				'auth_type'            => $this->char(16)->comment('授权状态 cancel_auth是取消授权，change_auth是更新授权，create_auth是授权成功通知'),
				'sync_user_time'       => $this->integer(11)->unsigned()->comment('最后一次同步通讯录时间'),
				'last_tag_time'        => $this->integer(11)->unsigned()->comment('最后一次同步企业微信标签'),
				'create_time'          => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('添加时间'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'企业微信授权关系信息表\'');

			$this->createIndex('KEY_WORK_CORP_AUTH_AUTHTYPE', '{{%work_corp_auth}}', 'auth_type');

			$this->addForeignKey('KEY_WORK_CORP_AUTH_SUITEID', '{{%work_corp_auth}}', 'suite_id', '{{%work_suite_config}}', 'id');
			$this->addForeignKey('KEY_WORK_CORP_AUTH_CORPID', '{{%work_corp_auth}}', 'corp_id', '{{%work_corp}}', 'id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200212_060603_add_table_work_corp_auth cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200212_060603_add_table_work_corp_auth cannot be reverted.\n";

			return false;
		}
		*/
	}
