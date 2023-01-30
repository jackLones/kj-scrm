<?php

use yii\db\Migration;

/**
 * Class m210222_102519_add_table_work_tag_attachment
 */
class m210222_102519_add_table_work_tag_attachment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%work_tag_attachment}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `corp_id` int(11) unsigned DEFAULT NULL COMMENT '授权的企业ID',
  `tag_id` int(11) unsigned DEFAULT NULL COMMENT '内容标签ID',
  `attachment_id` int(11) unsigned DEFAULT NULL COMMENT '内容ID',
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '0不显示1显示',
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `add_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_WORK_TAG_ATTACHMENT_CORPID` (`corp_id`),
  KEY `KEY_WORK_TAG_ATTACHMENT_TAGID` (`tag_id`),
  KEY `KEY_WORK_TAG_ATTACHMENT_ATTACHMENTID` (`attachment_id`),
  CONSTRAINT `KEY_WORK_TAG_ATTACHMENT_ATTACHMENTID` FOREIGN KEY (`attachment_id`) REFERENCES {{%attachment}} (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `KEY_WORK_TAG_ATTACHMENT_CORPID` FOREIGN KEY (`corp_id`) REFERENCES {{%work_corp}} (`id`),
  CONSTRAINT `KEY_WORK_TAG_ATTACHMENT_TAGID` FOREIGN KEY (`tag_id`) REFERENCES {{%work_tag}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='内容引擎标签表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210222_102519_add_table_work_tag_attachment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210222_102519_add_table_work_tag_attachment cannot be reverted.\n";

        return false;
    }
    */
}
