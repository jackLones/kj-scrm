<?php

use yii\db\Migration;

/**
 * Class m200815_062025_add_table_work_tag_chat
 */
class m200815_062025_add_table_work_tag_chat extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		//群标签表
	    $sql = <<<SQL
CREATE TABLE {{%work_tag_chat}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `corp_id` int(11) unsigned DEFAULT NULL COMMENT '授权的企业ID',
  `tag_id` int(11) unsigned DEFAULT NULL COMMENT '授权的企业的标签ID',
  `chat_id` int(11) unsigned DEFAULT NULL COMMENT '群ID',
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '0不显示1显示',
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `add_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_WORK_TAG_CHAT_CORPID` (`corp_id`),
  KEY `KEY_WORK_TAG_CHAT_TAGID` (`tag_id`),
  KEY `KEY_WORK_TAG_CHAT_CHATID` (`chat_id`),
  CONSTRAINT `KEY_WORK_TAG_CHAT_CORPID` FOREIGN KEY (`corp_id`) REFERENCES {{%work_corp}} (`id`),
  CONSTRAINT `KEY_WORK_TAG_CHAT_TAGID` FOREIGN KEY (`tag_id`) REFERENCES {{%work_tag}} (`id`),
  CONSTRAINT `KEY_WORK_TAG_CHAT_CHATID` FOREIGN KEY (`chat_id`) REFERENCES {{%work_chat}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='群标签表';
SQL;
	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200815_062025_add_table_work_tag_chat cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200815_062025_add_table_work_tag_chat cannot be reverted.\n";

        return false;
    }
    */
}
