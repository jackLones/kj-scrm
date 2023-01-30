<?php

	use yii\db\Migration;

	/**
	 * Class m200113_062723_change_table_attachment
	 */
	class m200113_062723_change_table_attachment extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->alterColumn('{{%attachment}}', 'file_type', 'tinyint(1) DEFAULT \'0\' COMMENT \'附件类型，1：图片、2：音频、3：视频、4：图文、5：文件、6：文本、7：小程序\'');
			$this->alterColumn('{{%attachment}}', 'file_width', 'varchar(8) DEFAULT \'\' COMMENT \'图片附件宽度\'');
			$this->alterColumn('{{%attachment}}', 'file_height', 'varchar(8) DEFAULT \'\' COMMENT \'图片附件高度\'');
			$this->addColumn('{{%attachment}}', 'group_id', 'int(11) unsigned COMMENT \'分组id\'  AFTER `uid`');
			$this->addColumn('{{%attachment}}', 'file_duration', 'char(8) DEFAULT \'\' COMMENT \'素材时长秒\'  AFTER `file_height`');
			$this->addColumn('{{%attachment}}', 'jump_url', 'text COMMENT \'图文的跳转地址\' AFTER `wx_url`');
			$this->addColumn('{{%attachment}}', 'content', 'text COMMENT \'对于文本类型，content是文本内容，对于图文类型，content是图文描述\' AFTER `jump_url`');
			$this->addColumn('{{%attachment}}', 'appId', 'varchar(32) DEFAULT \'\' COMMENT \'图文的跳转地址\' AFTER `content`');
			$this->addColumn('{{%attachment}}', 'appPath', 'varchar(64) DEFAULT \'\' COMMENT \'图文的跳转地址\' AFTER `appId`');
			$this->addColumn('{{%attachment}}', 'status', 'tinyint(1) DEFAULT \'1\' COMMENT \'1可用 0不可用\' AFTER `appPath`');
			$this->addColumn('{{%attachment}}', 'source', 'tinyint(1) DEFAULT \'0\' COMMENT \'0平台 1公众号 2企业 \' AFTER `status`');
			$this->addColumn('{{%attachment}}', 'update_time', 'timestamp NULL DEFAULT NULL COMMENT \'修改时间\'');
			$this->addForeignKey('KEY_ATTACHMENT_GROUPID', '{{%attachment}}', 'group_id', '{{%attachment_group}}', 'id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200113_062723_change_table_attachment cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200113_062723_change_table_attachment cannot be reverted.\n";

			return false;
		}
		*/
	}
