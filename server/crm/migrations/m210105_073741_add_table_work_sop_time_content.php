<?php

use yii\db\Migration;

/**
 * Class m210105_073741_add_table_work_sop_time_content
 */
class m210105_073741_add_table_work_sop_time_content extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%work_sop_time_content}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sop_time_id` int(11) unsigned DEFAULT NULL COMMENT 'SOP规则时间表ID',
  `type` tinyint(1) NOT NULL COMMENT '回复类型，1：文本（text）、2：图片（img）、3：语音（voice）、4：视频（video）、5：图文（news）',
  `content` text COMMENT '对于文本类型，content是文本内容，对于图文、图片、语音、视频类型，content是mediaID',
  `material_id` int(11) unsigned DEFAULT NULL COMMENT '素材库ID',
  `attachment_id` int(11) unsigned DEFAULT NULL COMMENT '附件id',
  `title` char(64) DEFAULT NULL COMMENT '图文消息的标题',
  `digest` varchar(255) DEFAULT NULL COMMENT '图文消息的摘要',
  `author` char(16) DEFAULT NULL COMMENT '图文消息的作者',
  `show_cover` tinyint(1) DEFAULT NULL COMMENT '是否显示封面，0为不显示，1为显示',
  `cover_url` text COMMENT '封面图片的URL',
  `content_url` text COMMENT '正文的URL',
  `source_url` text COMMENT '原文的URL，若置空则无查看原文入口',
  `is_use` tinyint(1) DEFAULT '0' COMMENT '是否是自定义',
  `is_sync` tinyint(1) DEFAULT '0' COMMENT '是否同步文件柜',
  `attach_id` int(11) unsigned DEFAULT NULL COMMENT '同步文件柜的id',
  `status` tinyint(1) unsigned DEFAULT NULL COMMENT '是否开启，0代表未开启，1代表开启',
  `create_time` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_WORK_SOP_TIME_CONTENT_SOP_TIMEID` (`sop_time_id`),
  CONSTRAINT `KEY_WORK_SOP_TIME_CONTENT_SOP_TIMEID` FOREIGN KEY (`sop_time_id`) REFERENCES {{%work_sop_time}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='SOP规则时间内容素材表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210105_073741_add_table_work_sop_time_content cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210105_073741_add_table_work_sop_time_content cannot be reverted.\n";

        return false;
    }
    */
}
