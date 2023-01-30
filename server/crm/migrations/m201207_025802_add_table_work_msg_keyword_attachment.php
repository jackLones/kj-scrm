<?php

use yii\db\Migration;

/**
 * Class m201207_025802_add_table_work_msg_keyword_attachment
 */
class m201207_025802_add_table_work_msg_keyword_attachment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%work_msg_keyword_attachment}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `corp_id` int(11) unsigned DEFAULT NULL COMMENT '企业ID',
  `keywords` VARCHAR(5000) DEFAULT NULL COMMENT '关键词（逗号分隔）',
  `attachment_ids` VARCHAR(2000) DEFAULT NULL COMMENT '内容引擎id集合',
  `is_del` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '是否删除1是0否',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `KEY_WORK_MSG_KEYWORD_ATTACHMENT_CORPID` (`corp_id`),
  CONSTRAINT `KEY_WORK_MSG_KEYWORD_ATTACHMENT_CORPID` FOREIGN KEY (`corp_id`) REFERENCES {{%work_corp}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='智能推荐关键词表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201207_025802_add_table_work_msg_keyword_attachment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201207_025802_add_table_work_msg_keyword_attachment cannot be reverted.\n";

        return false;
    }
    */
}
