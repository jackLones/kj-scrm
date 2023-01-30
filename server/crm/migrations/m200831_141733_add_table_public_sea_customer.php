<?php

	use yii\db\Migration;

	/**
	 * Class m200831_141733_add_table_public_sea_customer
	 */
	class m200831_141733_add_table_public_sea_customer extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			//公海客户
			$sql = <<<SQL
CREATE TABLE {{%public_sea_customer}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned DEFAULT NULL COMMENT '账户ID',
  `corp_id` int(11) unsigned DEFAULT NULL COMMENT '授权的企业ID',
  `type` tinyint(1) unsigned DEFAULT '1' COMMENT '0非企微客户1企微客户',
  `name` varchar(64) DEFAULT '' COMMENT '姓名',
  `wx_num` varchar(64) DEFAULT '' COMMENT '微信号',
  `phone` text COMMENT '手机号',
  `qq` varchar(64) DEFAULT '' COMMENT 'QQ',
  `remark` varchar(64) DEFAULT '' COMMENT '备注',
  `field_option_id` int(11) unsigned DEFAULT 0 COMMENT '来源高级属性选项id',
  `external_userid` int(11) unsigned DEFAULT 0 COMMENT '绑定的外部联系人id',
  `bind_time` int(11) unsigned DEFAULT 0 COMMENT '绑定时间',
  `user_id` int(11) unsigned DEFAULT 0 COMMENT '上次认领成员',
  `follow_user_id` int(11) unsigned DEFAULT 0 COMMENT '外部联系人添加信息表id',
  `is_claim` tinyint(1) unsigned DEFAULT 0 COMMENT '是否已认领0否1是',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `add_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `reclaim_time` int(11) NOT NULL DEFAULT 0 COMMENT '最后回收时间',
  PRIMARY KEY (`id`),
  KEY `KEY_PUBLIC_SEA_CUSTOMER_UID` (`uid`),
  KEY `KEY_PUBLIC_SEA_CUSTOMER_CORPID` (`corp_id`),
  CONSTRAINT `KEY_PUBLIC_SEA_CUSTOMER_UID` FOREIGN KEY (`uid`) REFERENCES {{%user}} (`uid`),
  CONSTRAINT `KEY_PUBLIC_SEA_CUSTOMER_CORPID` FOREIGN KEY (`corp_id`) REFERENCES {{%work_corp}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='公海客户表';
SQL;
			$this->execute($sql);

			//认领回收表
			$sql = <<<SQL
CREATE TABLE {{%public_sea_claim}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned DEFAULT NULL COMMENT '账户ID',
  `corp_id` int(11) unsigned DEFAULT NULL COMMENT '授权的企业ID',
  `sea_id` int(11) unsigned DEFAULT NULL COMMENT '公海客户id',
  `type` tinyint(1) unsigned DEFAULT 1 COMMENT '0非企微客户、1企微客户',
  `claim_type` tinyint(1) unsigned DEFAULT 0 COMMENT '0回收、1认领',
  `user_id` int(11) unsigned DEFAULT 0 COMMENT '认领回收成员id',
  `external_userid` int(11) unsigned DEFAULT 0 COMMENT '企微外部联系人id',
  `follow_user_id` int(11) unsigned DEFAULT 0 COMMENT '回收关联表id',
  `claim_time` int(11) NOT NULL DEFAULT 0 COMMENT '认领时间',
  `reclaim_time` int(11) NOT NULL DEFAULT 0 COMMENT '回收时间',
  PRIMARY KEY (`id`),
  KEY `KEY_PUBLIC_SEA_CLAIM_CORPID` (`corp_id`),
  KEY `KEY_PUBLIC_SEA_CLAIM_SEAID` (`sea_id`),
  KEY `KEY_PUBLIC_SEA_CLAIM_USERID` (`user_id`),
  CONSTRAINT `KEY_PUBLIC_SEA_CLAIM_CORPID` FOREIGN KEY (`corp_id`) REFERENCES {{%work_corp}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='公海客户认领回收表';
SQL;
			$this->execute($sql);

			//非企微客户外部联系人添加信息表
			$sql = <<<SQL
CREATE TABLE {{%public_sea_contact_follow_user}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `corp_id` int(11) unsigned DEFAULT NULL COMMENT '授权的企业ID',
  `sea_id` int(11) unsigned DEFAULT NULL COMMENT '公海客户ID',
  `user_id` int(11) unsigned DEFAULT NULL COMMENT '成员ID',
  `follow_id` int(11) unsigned DEFAULT '0' COMMENT '状态id',
  `last_follow_time` int(11) NOT NULL DEFAULT '0' COMMENT '最后一次跟进状态时间',
  `is_chat` tinyint(1) unsigned DEFAULT '0' COMMENT '沟通状态：0一直未沟通、1已沟通',
  `follow_num` int(11) NOT NULL DEFAULT '0' COMMENT '跟进次数',
  `close_rate` int(11) unsigned DEFAULT NULL COMMENT '预计成交率',
  `description` varchar(255) DEFAULT '' COMMENT '设置的用户描述',
  `company_name` varchar(255) DEFAULT '' COMMENT '公司名称',
  `is_reclaim` tinyint(1) unsigned DEFAULT 0 COMMENT '是否已回收：0否、1是',
  `add_time` int(11) NOT NULL DEFAULT 0 COMMENT '添加时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `KEY_PUBLIC_SEA_CONTACT_FOLLOW_USER_CORPID` (`corp_id`),
  KEY `KEY_PUBLIC_SEA_CONTACT_FOLLOW_USER_SEAID` (`sea_id`),
  KEY `KEY_PUBLIC_SEA_CONTACT_FOLLOW_USER_USERID` (`user_id`),
  CONSTRAINT `KEY_PUBLIC_SEA_CONTACT_FOLLOW_USER_CORPID` FOREIGN KEY (`corp_id`) REFERENCES {{%work_corp}} (`id`),
  CONSTRAINT `KEY_PUBLIC_SEA_CONTACT_FOLLOW_USER_SEAID` FOREIGN KEY (`sea_id`) REFERENCES {{%public_sea_customer}} (`id`),
  CONSTRAINT `KEY_PUBLIC_SEA_CONTACT_FOLLOW_USER_USERID` FOREIGN KEY (`user_id`) REFERENCES {{%work_user}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='非企微客户外部联系人添加信息表';
SQL;
			$this->execute($sql);

			//非企微客户跟进记录表
			$sql = <<<SQL
CREATE TABLE {{%public_sea_contact_follow_record}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned DEFAULT NULL COMMENT '账户ID',
  `sub_id` int(11) unsigned DEFAULT 0 COMMENT '子账户ID',
  `sea_id` int(11) unsigned DEFAULT NULL COMMENT '公海客户ID',
  `user_id` int(11) unsigned DEFAULT 0 COMMENT '成员ID',
  `record` text NOT NULL COMMENT '跟进记录',
  `file` varchar(1000) DEFAULT '' COMMENT '图片附件',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否有效：1是0否',
  `follow_id` int(11) DEFAULT '0' COMMENT '跟进状态id',
  `is_master` tinyint(1) unsigned DEFAULT '0' COMMENT '状态：0主账户添加、1子账户添加 ',
  `is_sync` tinyint(1) unsigned DEFAULT '0' COMMENT '是否已同步过：0否、1是',
  PRIMARY KEY (`id`),
  KEY `KEY_PUBLIC_SEA_CONTACT_FOLLOW_RECORD_UID` (`uid`),
  KEY `KEY_PUBLIC_SEA_CONTACT_FOLLOW_RECORD_SEAID` (`sea_id`),
  CONSTRAINT `KEY_PUBLIC_SEA_CONTACT_FOLLOW_RECORD_UID` FOREIGN KEY (`uid`) REFERENCES {{%user}} (`uid`),
  CONSTRAINT `KEY_PUBLIC_SEA_CONTACT_FOLLOW_RECORD_SEAID` FOREIGN KEY (`sea_id`) REFERENCES {{%public_sea_customer}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='非企微客户跟进记录表';
SQL;
			$this->execute($sql);

		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200831_141733_add_table_public_sea_customer cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200831_141733_add_table_public_sea_customer cannot be reverted.\n";

			return false;
		}
		*/
	}
