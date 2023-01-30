<?php

	use yii\db\Migration;

	/**
	 * Class m201112_053056_add_table_attachment_auth
	 */
	class m201112_053056_add_table_attachment_auth extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%attachment}}', 'auth_type', 'tinyint(1) unsigned DEFAULT 1 COMMENT \'权限类型：1：通用、2：指定成员部门、3：仅自己\' ');

			$sql = <<<SQL
CREATE TABLE {{%attachment_auth}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `corp_id` int(11) unsigned DEFAULT NULL COMMENT '授权的企业ID',
  `attachment_id` int(11) unsigned DEFAULT NULL COMMENT '附件ID',
  `user_key` text COMMENT '生效成员',
  `user` text COMMENT '用户userID列表',
  `party` text COMMENT '生效部门',
  `add_time` int(11) NOT NULL DEFAULT 0 COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `KEY_ATTACHMENT_AUTH_CORPID` (`corp_id`),
  KEY `KEY_ATTACHMENT_AUTH_ATTACHMENTID` (`attachment_id`),
  CONSTRAINT `KEY_ATTACHMENT_AUTH_CORPID` FOREIGN KEY (`corp_id`) REFERENCES {{%work_corp}} (`id`),
  CONSTRAINT `KEY_ATTACHMENT_AUTH_ATTACHMENTID` FOREIGN KEY (`attachment_id`) REFERENCES {{%attachment}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='附件权限表';
SQL;

			$this->execute($sql);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m201112_053056_add_table_attachment_auth cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m201112_053056_add_table_attachment_auth cannot be reverted.\n";

			return false;
		}
		*/
	}
