<?php

	use yii\db\Migration;

	/**
	 * Class m200327_065319_change_work_user_and_work_external_table
	 */
	class m200327_065319_change_work_user_and_work_external_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%work_user}}', 'openid', 'char(64) NULL COMMENT \'成员openid\' AFTER `avg_reply_time`');
			$this->createIndex('KEY_WORK_USER_OPENID', '{{%work_user}}', 'openid');

			$this->addColumn('{{%work_external_contact}}', 'openid', 'char(64) NULL COMMENT \'外部联系人openid\' AFTER `unionid`');
			$this->createIndex('KEY_WORK_EXTERNAL_CONTACT_OPENID', '{{%work_external_contact}}', 'openid');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200327_065319_change_work_user_and_work_external_table cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200327_065319_change_work_user_and_work_external_table cannot be reverted.\n";

			return false;
		}
		*/
	}
