<?php

	use yii\db\Migration;

	/**
	 * Class m200416_123908_add_miniprogram_table
	 */
	class m200416_123908_add_miniprogram_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$sql = <<<SQL
DROP TABLE IF EXISTS {{%mini_user}};
CREATE TABLE {{%mini_user}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `author_id` int(11) unsigned NOT NULL COMMENT '小程序ID',
  `openid` char(80) DEFAULT NULL COMMENT '用户的标识，对当前小程序唯一',
  `unionid` char(80) DEFAULT NULL COMMENT '只有在用户将小程序绑定到微信开放平台帐号后，才会出现该字段。',
  `fans_id` int(11) unsigned DEFAULT NULL COMMENT '绑定的公众号的粉丝ID',
  `last_time` char(16) DEFAULT NULL COMMENT '最后活跃时间',
  `create_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `KEY_MINI_USER_AUTHORID` (`author_id`),
  KEY `KEY_MINI_USER_OPENID` (`openid`),
  CONSTRAINT `KEY_MINI_USER_AUTHORID` FOREIGN KEY (`author_id`) REFERENCES {{%wx_authorize}} (`author_id`),
  CONSTRAINT `KEY_MINI_USER_FANSID` FOREIGN KEY (`fans_id`) REFERENCES {{%fans}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='小程序用户表';

DROP TABLE IF EXISTS {{%mini_msg}};
CREATE TABLE {{%mini_msg}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mini_id` int(11) unsigned DEFAULT NULL COMMENT '小程序用户ID',
  `kf_id` int(11) unsigned DEFAULT NULL COMMENT '客服ID',
  `from` tinyint(1) DEFAULT NULL COMMENT '发送方，1：小程序用户、2：用户、3：客服',
  `to` tinyint(1) DEFAULT NULL COMMENT '接收方，1：小程序、2：用户、3：客服',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '消息内容',
  `isread` tinyint(1) unsigned DEFAULT '0' COMMENT '是否已读，0：未读、1：已读',
  `content_type` tinyint(1) unsigned DEFAULT NULL COMMENT '消息类型，1：文本（text）、2：图片（img）、3：小程序卡片（miniprogrampage）',
  `attachment_id` int(11) unsigned DEFAULT NULL COMMENT '附件id',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_MINI_MSG_FANSID` (`mini_id`),
  KEY `KEY_MINI_MSG_KFID` (`kf_id`),
  KEY `KEY_MINI_MSG_CREATETIME` (`create_time`),
  CONSTRAINT `KEY_MINI_MSG_MINIID` FOREIGN KEY (`mini_id`) REFERENCES {{%mini_user}} (`id`),
  CONSTRAINT `KEY_MINI_MSG_KFID` FOREIGN KEY (`kf_id`) REFERENCES {{%kf_user}} (`id`),
  CONSTRAINT `KEY_MINI_MSG_ATTACHMENTID` FOREIGN KEY (`attachment_id`) REFERENCES {{%attachment}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='小程序用户留言信息表';

DROP TABLE IF EXISTS {{%mini_msg_material}};
CREATE TABLE {{%mini_msg_material}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `author_id` int(11) unsigned DEFAULT NULL COMMENT '小程序ID',
  `mini_id` int(11) unsigned DEFAULT NULL COMMENT '小程序用户ID',
  `msg_id` int(11) unsigned DEFAULT NULL COMMENT '消息ID',
  `material_type` tinyint(1) NOT NULL COMMENT '素材类型：2、图片（image）',
  `file_name` char(32) DEFAULT NULL COMMENT '素材名称',
  `media_width` char(8) DEFAULT NULL COMMENT '素材宽度',
  `media_height` char(8) DEFAULT NULL COMMENT '素材高度',
  `media_duration` char(16) DEFAULT NULL COMMENT '多媒体素材时长',
  `file_length` char(16) DEFAULT NULL COMMENT '素材大小',
  `content_type` char(16) DEFAULT NULL COMMENT '素材类型',
  `local_path` text COMMENT '素材本地地址',
  `yun_url` text COMMENT '素材云端地址',
  `wx_url` text COMMENT '素材微信地址',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `IDX_MINI_MSG_MATERIAL_AUTHORID` (`author_id`),
  KEY `IDX_MINI_MSG_MATERIAL_MINIID` (`mini_id`),
  KEY `IDX_MINI_MSG_MATERIAL_MSGID` (`msg_id`),
  KEY `IDX_MINI_MSG_MATERIAL_MATERIALTYPE` (`material_type`),
  CONSTRAINT `KEY_MINI_MSG_MATERIAL_AUTHORID` FOREIGN KEY (`author_id`) REFERENCES {{%wx_authorize}} (`author_id`),
  CONSTRAINT `KEY_MINI_MSG_MATERIAL_MINIID` FOREIGN KEY (`mini_id`) REFERENCES {{%mini_user}} (`id`),
  CONSTRAINT `KEY_MINI_MSG_MATERIAL_MSGID` FOREIGN KEY (`msg_id`) REFERENCES {{%mini_msg}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='小程序用户消息素材表';
SQL;

			$this->execute($sql);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200416_123908_add_miniprogram_table cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200416_123908_add_miniprogram_table cannot be reverted.\n";

			return false;
		}
		*/
	}
