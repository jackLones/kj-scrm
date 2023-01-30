<?php

use yii\db\Migration;

/**
 * Class m210222_012006_add_table_attachment_tag_group
 */
class m210222_012006_add_table_attachment_tag_group extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%attachment_tag_group}} (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) unsigned DEFAULT NULL COMMENT '上级分组id',
  `uid` int(11) unsigned DEFAULT NULL COMMENT '账户id',
  `corp_id` int(11) unsigned DEFAULT NULL COMMENT '企业微信id',
  `name` char(60) DEFAULT NULL COMMENT '分组名称',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态',
  `sort` int(11) DEFAULT NULL COMMENT '排序',
  `parent_ids` text COMMENT '上级分组',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `ATTACHMENT_TAG_GROUP_UID` (`uid`),
  KEY `ATTACHMENT_TAG_GROUP_PID` (`pid`),
  KEY `ATTACHMENT_TAG_GROUP_CORP_ID` (`corp_id`),
  CONSTRAINT `ATTACHMENT_TAG_GROUP_CORP_ID` FOREIGN KEY (`corp_id`) REFERENCES {{%work_corp}} (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='内容标签分组表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210222_012006_add_table_attachment_tag_group cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210222_012006_add_table_attachment_tag_group cannot be reverted.\n";

        return false;
    }
    */
}
