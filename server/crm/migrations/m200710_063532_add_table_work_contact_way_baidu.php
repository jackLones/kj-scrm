<?php

	use yii\db\Migration;

	/**
	 * Class m200710_063532_add_table_work_contact_way_baidu
	 */
	class m200710_063532_add_table_work_contact_way_baidu extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			//企业微信联系我百度表
			$sql = <<<SQL
CREATE TABLE {{%work_contact_way_baidu_group}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned DEFAULT NULL COMMENT '用户ID',
  `corp_id` int(11) unsigned DEFAULT NULL COMMENT '企业ID',
  `parent_id` int(11) unsigned DEFAULT NULL COMMENT '分组父级ID',
  `title` varchar(32) DEFAULT NULL COMMENT '分组名称',
  `status` tinyint(1) DEFAULT '1' COMMENT '1可用 0不可用',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `sort` int(11) DEFAULT '0' COMMENT '分组排序',
  `is_not_group` tinyint(1) DEFAULT '0' COMMENT '0已分组、1未分组',
  PRIMARY KEY (`id`),
  KEY `KEY_WORK_CONTACT_WAY_BAIDU_GROUP_UID` (`uid`),
  KEY `KEY_WORK_CONTACT_WAY_BAIDU_GROUP_CORPID` (`corp_id`),
  KEY `KEY_WORK_CONTACT_WAY_BAIDU_GROUP_PARENTID` (`parent_id`),
  CONSTRAINT `KEY_WORK_CONTACT_WAY_BAIDU_GROUP_CORPID` FOREIGN KEY (`corp_id`) REFERENCES {{%work_corp}} (`id`),
  CONSTRAINT `KEY_WORK_CONTACT_WAY_BAIDU_GROUP_PARENTID` FOREIGN KEY (`parent_id`) REFERENCES {{%work_contact_way_baidu_group}} (`id`),
  CONSTRAINT `KEY_WORK_CONTACT_WAY_BAIDU_GROUP_UID` FOREIGN KEY (`uid`) REFERENCES {{%user}} (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='渠道活码分组百度表';
SQL;

			$this->execute($sql);

			//企业微信联系我百度表
			$sql = <<<SQL
CREATE TABLE {{%work_contact_way_baidu}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `corp_id` int(11) unsigned DEFAULT NULL COMMENT '授权的企业ID',
  `way_group_id` int(11) unsigned DEFAULT NULL COMMENT '渠道活码分组id',
  `title` varchar(200) DEFAULT NULL COMMENT '活码名称',
  `type` tinyint(1) unsigned DEFAULT NULL COMMENT '联系方式类型,1-单人, 2-多人',
  `skip_verify` tinyint(1) DEFAULT NULL COMMENT '外部客户添加时是否无需验证，默认为true',
  `is_del` tinyint(1) DEFAULT '0' COMMENT '0：未删除；1：已删除',
  `open_date` tinyint(1) DEFAULT '0' COMMENT '0关闭1开启',  
  `add_num` int(11) unsigned DEFAULT '0' COMMENT '添加人数',
  `tag_ids` text COMMENT '给客户打的标签',
  `content` text COMMENT '渠道活码的欢迎语内容',
  `status` tinyint(1) DEFAULT '0' COMMENT '渠道活码的欢迎语是否开启0关闭1开启',
  `sync_attachment_id` int(11) unsigned DEFAULT '0' COMMENT '同步后的素材id',
  `work_material_id` int(11) unsigned DEFAULT '0' COMMENT '企业微信素材id',
  `groupId` int(11) unsigned DEFAULT '0' COMMENT '分组id',
  `material_sync` tinyint(1) unsigned DEFAULT '0' COMMENT '不同步到内容库1同步',
  `attachment_id` int(11) unsigned DEFAULT '0' COMMENT '内容引擎id',
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_WORK_CONTACT_WAY_BAIDU_CORPID` (`corp_id`),
  CONSTRAINT `KEY_WORK_CONTACT_WAY_BAIDU_CORPID` FOREIGN KEY (`corp_id`) REFERENCES {{%work_corp}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='企业微信联系我百度表';
SQL;

			$this->execute($sql);

			//企业微信联系我二维码百度表
			$sql = <<<SQL
CREATE TABLE {{%work_contact_way_baidu_code}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `way_id` int(11) unsigned NOT NULL COMMENT '企业微信联系我表ID',
  `config_id` varchar(64) DEFAULT '' COMMENT '联系方式的配置id',
  `qr_code` varchar(255) DEFAULT '' COMMENT '联系二维码的URL',
  `state` varchar(64) DEFAULT '' COMMENT '企业自定义的state参数，用于区分不同的添加渠道，在调用获取外部联系人详情时会返回该参数值',
  `add_num` int(11) unsigned DEFAULT '0' COMMENT '添加人数',
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `config_status` tinyint(1) DEFAULT '1' COMMENT '活码状态：0删除、1可用',
  `expire_time` int(11) NOT NULL DEFAULT '0' COMMENT '活码过期时间',
  `user` text COMMENT '用户userID列表',
  `party` text COMMENT '部门partyID列表',
  `queue_id` int(11) unsigned DEFAULT '0' COMMENT '队列id',
  `bd_vid` text COMMENT '转化页bd_vid',
  `logidUrl` text COMMENT '转化页URL',
  `newType` int(11) NOT NULL DEFAULT '0' COMMENT '转化类型',
  PRIMARY KEY (`id`),
  KEY `KEY_WORK_CONTACT_WAY_BAIDU_CODE_WAYID` (`way_id`),
  CONSTRAINT `KEY_WORK_CONTACT_WAY_BAIDU_CODE_WAYID` FOREIGN KEY (`way_id`) REFERENCES {{%work_contact_way_baidu}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='企业微信联系我二维码百度表';
SQL;

			$this->execute($sql);

			//渠道活码日期百度表
			$sql = <<<SQL
CREATE TABLE {{%work_contact_way_baidu_date}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `way_id` int(11) unsigned NOT NULL COMMENT '企业微信联系我表ID',
  `type` tinyint(1) unsigned DEFAULT '0' COMMENT '0周1日期',
  `start_date` date DEFAULT NULL COMMENT '开始日期',
  `end_date` date DEFAULT NULL COMMENT '结束日期',
  `day` varchar(32) DEFAULT NULL COMMENT '周几',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_WORK_CONTACT_WAY_BAIDU_DATE_WAY_ID` (`way_id`),
  CONSTRAINT `KEY_WORK_CONTACT_WAY_BAIDU_DATE_WAY_ID` FOREIGN KEY (`way_id`) REFERENCES {{%work_contact_way_baidu}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='渠道活码日期百度表';
SQL;

			$this->execute($sql);

			//渠道活码日期成员百度表
			$sql = <<<SQL
CREATE TABLE {{%work_contact_way_baidu_date_user}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date_id` int(11) unsigned NOT NULL COMMENT '企业微信联系我表ID',
  `time` char(32) DEFAULT NULL COMMENT '具体时间',
  `user_key` text COMMENT '用户选择的key值',
  `department` varchar(255) DEFAULT NULL COMMENT '部门id',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_WORK_CONTACT_WAY_BAIDU_DATE_USER_DATE_ID` (`date_id`),
  KEY `KEY_WORK_CONTACT_WAY_BAIDU_DATE_USER_TIME` (`time`),
  CONSTRAINT `KEY_WORK_CONTACT_WAY_BAIDU_DATE_USER_DATE_ID` FOREIGN KEY (`date_id`) REFERENCES {{%work_contact_way_baidu_date}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='渠道活码日期成员百度表';
SQL;

			$this->execute($sql);

			//账户百度配置表
			$sql = <<<SQL
CREATE TABLE {{%user_baidu}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned DEFAULT NULL COMMENT '用户ID',
  `token` varchar(128) DEFAULT NULL COMMENT 'token',
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_USER_BAIDU_UID` (`uid`),
  CONSTRAINT `KEY_USER_BAIDU_UID` FOREIGN KEY (`uid`) REFERENCES {{%user}} (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='账户百度配置表';
SQL;
			$this->execute($sql);

			$this->addColumn('{{%work_external_contact_follow_user}}', 'baidu_way_id', 'int(11) unsigned DEFAULT NULL COMMENT \'百度联系我配置ID\' AFTER `way_id`');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200710_063532_add_table_work_contact_way_baidu cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200710_063532_add_table_work_contact_way_baidu cannot be reverted.\n";

			return false;
		}
		*/
	}
