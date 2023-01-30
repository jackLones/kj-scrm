<?php

	use yii\db\Migration;

	/**
	 * Class m200117_094425_add_table_corp_bind
	 */
	class m200117_094425_add_table_corp_bind extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_corp_bind}}', [
				'id'                            => $this->primaryKey(11)->unsigned(),
				'corp_id'                       => $this->integer(11)->unsigned()->comment('企业ID'),
				'token'                         => $this->string(255)->comment('Token用于计算签名'),
				'encode_aes_key'                => $this->string(255)->comment('EncodingAESKey用于消息内容加密'),
				'book_secret'                   => $this->char(64)->comment('通讯录管理secret。在“管理工具”-“通讯录同步”里面查看（需开启“API接口同步”）'),
				'book_access_token'             => $this->string(255)->comment('通讯录管理access_token'),
				'book_access_token_expires'     => $this->char(16)->comment('book_access_token有效期'),
				'external_secret'               => $this->char(64)->comment('外部联系人管理secret。在“客户联系”栏，点开“API”小按钮，即可看到'),
				'external_access_token'         => $this->string(255)->comment('外部联系人管理saccess_token'),
				'external_access_token_expires' => $this->char(16)->comment('external_access_token有效期'),
				'create_time'                   => $this->timestamp()->comment('创建时间'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'企业微信相关绑定信息表\'');

			$this->addForeignKey('KEY_WORK_CORP_BIND_CORPID', '{{%work_corp_bind}}', 'corp_id', '{{%work_corp}}', 'id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200117_094425_add_table_corp_bind cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200117_094425_add_table_corp_bind cannot be reverted.\n";

			return false;
		}
		*/
	}
