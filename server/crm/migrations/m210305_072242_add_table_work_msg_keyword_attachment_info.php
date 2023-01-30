<?php

use yii\db\Migration;

/**
 * Class m210305_072242_add_table_work_msg_keyword_attachment_info
 */
class m210305_072242_add_table_work_msg_keyword_attachment_info extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%work_msg_keyword_attachment_info}} (
  `id` INT (11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`keyword_id` INT (11) UNSIGNED DEFAULT NULL COMMENT '推荐规则ID',
	`keyword_tag_id` INT (11) UNSIGNED DEFAULT 0 COMMENT '推荐规则关联标签表ID',
	`type` TINYINT (1) NOT NULL COMMENT '回复类型，1：文本（text）、2：图片（img）、3：语音（voice）、4：视频（video）、5：图文（news）',
	`content` text COMMENT '对于文本类型，content是文本内容，对于图文、图片、语音、视频类型，content是mediaID',
	`material_id` INT (11) UNSIGNED DEFAULT NULL COMMENT '素材库ID',
	`attachment_id` INT (11) UNSIGNED DEFAULT NULL COMMENT '附件id',
	`title` CHAR (64) DEFAULT NULL COMMENT '图文消息的标题',
	`digest` VARCHAR (255) DEFAULT NULL COMMENT '图文消息的摘要',
	`author` CHAR (16) DEFAULT NULL COMMENT '图文消息的作者',
	`show_cover` TINYINT (1) DEFAULT NULL COMMENT '是否显示封面，0为不显示，1为显示',
	`cover_url` text COMMENT '封面图片的URL',
	`content_url` text COMMENT '正文的URL',
	`source_url` text COMMENT '原文的URL，若置空则无查看原文入口',
	`status` TINYINT (1) UNSIGNED DEFAULT NULL COMMENT '是否开启，0代表未开启，1代表开启',
	`is_use` TINYINT (1) DEFAULT '0' COMMENT '是否是自定义',
	`is_sync` TINYINT (1) DEFAULT '0' COMMENT '是否同步文件柜',
	`attach_id` INT (11) UNSIGNED DEFAULT NULL COMMENT '同步文件柜的id',
	`create_time` TIMESTAMP NULL DEFAULT NULL COMMENT '创建时间',
	`appid` VARCHAR (255) DEFAULT '' COMMENT '小程序的appid',
	`pagepath` VARCHAR (255) DEFAULT '' COMMENT '小程序的页面路径',
	PRIMARY KEY (`id`),
	KEY `KEY_WORK_MSG_KEYWORD_ATTACHMENT_INFO_KEYWORDID` (`keyword_id`),
	KEY `KEY_WORK_MSG_KEYWORD_ATTACHMENT_INFO_KEYWORDTAGID` (`keyword_tag_id`),
	KEY `KEY_WORK_MSG_KEYWORD_ATTACHMENT_INFO_MATERIALID` (`material_id`),
	CONSTRAINT `KEY_WORK_MSG_KEYWORD_ATTACHMENT_INFO_KEYWORDID` FOREIGN KEY (`keyword_id`) REFERENCES {{%work_msg_keyword_attachment}} (`id`),
	CONSTRAINT `KEY_WORK_MSG_KEYWORD_ATTACHMENT_INFO_KEYWORDTAGID` FOREIGN KEY (`keyword_tag_id`) REFERENCES {{%work_msg_keyword_tag}} (`id`),
	CONSTRAINT `KEY_WORK_MSG_KEYWORD_ATTACHMENT_INFO_MATERIALID` FOREIGN KEY (`material_id`) REFERENCES {{%material}} (`id`)
) ENGINE = INNODB DEFAULT CHARSET = utf8mb4 COMMENT = '智能推荐关键词关联内容表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210305_072242_add_table_work_msg_keyword_attachment_info cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210305_072242_add_table_work_msg_keyword_attachment_info cannot be reverted.\n";

        return false;
    }
    */
}
