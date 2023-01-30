<?php

	use yii\db\Migration;

	/**
	 * Class m200804_035650_add_table_work_tag_keyword_rule
	 */
	class m200804_035650_add_table_work_tag_keyword_rule extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			//标签关键词表
			$sql = <<<SQL
CREATE TABLE {{%work_tag_keyword_rule}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `corp_id` int(11) unsigned NOT NULL COMMENT '企业微信id',
  `tags_id` text NOT NULL COMMENT '标签id',
  `keyword` text NOT NULL COMMENT '关键词',
  `status` tinyint(1) DEFAULT '2' COMMENT '0删除、1关闭、2开启',
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `add_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_WORK_TAG_KEYWORD_RULE_CORPID` (`corp_id`),
  CONSTRAINT `KEY_WORK_TAG_KEYWORD_RULE_CORPID` FOREIGN KEY (`corp_id`) REFERENCES {{%work_corp}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='标签关键词表';
SQL;
			$this->execute($sql);

			//生效员工表
			$sql = <<<SQL
CREATE TABLE {{%work_user_tag_rule}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `corp_id` int(11) unsigned NOT NULL COMMENT '企业微信id',
  `user_id` int(11) unsigned NOT NULL COMMENT '授权的企业的成员ID',
  `tags_id` text NOT NULL COMMENT '标签id',
  `status` tinyint(1) DEFAULT '2' COMMENT '0删除、1关闭、2开启',
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `add_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_WORK_USER_TAG_RULE_CORPID` (`corp_id`),
  KEY `KEY_WORK_USER_TAG_RULE_USERID` (`user_id`),
  CONSTRAINT `KEY_WORK_USER_TAG_RULE_CORPID` FOREIGN KEY (`corp_id`) REFERENCES {{%work_corp}} (`id`),
  CONSTRAINT `KEY_WORK_USER_TAG_RULE_USERID` FOREIGN KEY (`user_id`) REFERENCES {{%work_user}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='生效员工表';
SQL;
			$this->execute($sql);

			//客户打标签记录表
			$sql = <<<SQL
CREATE TABLE {{%work_user_tag_external}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `corp_id` int(11) unsigned NOT NULL COMMENT '企业微信id',
  `user_id` int(11) unsigned NOT NULL COMMENT '授权的企业的成员ID',
  `external_id` int(11) unsigned NOT NULL COMMENT '外部联系人ID',
  `tag_id` int(11) unsigned NOT NULL COMMENT '标签id',
  `follow_user_id` int(11) unsigned NOT NULL COMMENT '外部联系人对应的ID',
  `keyword` varchar(250) DEFAULT NULL COMMENT '关键词',
  `audit_info_id` int(11) unsigned NOT NULL COMMENT '会话内容ID',
  `status` tinyint(1) DEFAULT '1' COMMENT '0删除、1可用',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '打标签时间',
  PRIMARY KEY (`id`),
  KEY `KEY_WORK_USER_TAG_EXTERNAL_CORPID` (`corp_id`),
  KEY `KEY_WORK_USER_TAG_EXTERNAL_USERID` (`user_id`),
  KEY `KEY_WORK_USER_TAG_EXTERNAL_EXTERNALID` (`external_id`),
  KEY `KEY_WORK_USER_TAG_EXTERNAL_TAGID` (`tag_id`),
  CONSTRAINT `KEY_WORK_USER_TAG_EXTERNAL_CORPID` FOREIGN KEY (`corp_id`) REFERENCES {{%work_corp}} (`id`),
  CONSTRAINT `KEY_WORK_USER_TAG_EXTERNAL_USERID` FOREIGN KEY (`user_id`) REFERENCES {{%work_user}} (`id`),
  CONSTRAINT `KEY_WORK_USER_TAG_EXTERNAL_EXTERNALID` FOREIGN KEY (`external_id`) REFERENCES {{%work_external_contact}} (`id`),
  CONSTRAINT `KEY_WORK_USER_TAG_EXTERNAL_TAGID` FOREIGN KEY (`tag_id`) REFERENCES {{%work_tag}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='客户打标签记录表';
SQL;
			$this->execute($sql);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200804_035650_add_table_work_tag_keyword_rule cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200804_035650_add_table_work_tag_keyword_rule cannot be reverted.\n";

			return false;
		}
		*/
	}
