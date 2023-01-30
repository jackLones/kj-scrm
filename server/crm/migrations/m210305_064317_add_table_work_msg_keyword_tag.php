<?php

use yii\db\Migration;

/**
 * Class m210305_064317_add_table_work_msg_keyword_tag
 */
class m210305_064317_add_table_work_msg_keyword_tag extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%work_msg_keyword_tag}} (
  `id` INT (11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`corp_id` INT (11) UNSIGNED DEFAULT NULL COMMENT '企业ID',
	`keyword_id` INT (11) UNSIGNED DEFAULT NULL COMMENT '推荐规则ID',
	`tags` VARCHAR (5000) DEFAULT NULL COMMENT '客户标签id（逗号分隔）',
	`attachment_ids` VARCHAR (5000) DEFAULT NULL COMMENT '内容引擎id集合',
	`is_del` TINYINT (1) NOT NULL DEFAULT '0' COMMENT '是否删除1是0否',
	`add_time` INT (11) NOT NULL DEFAULT '0' COMMENT '添加时间',
	PRIMARY KEY (`id`),
	KEY `KEY_WORK_MSG_KEYWORD_TAG_CORPID` (`corp_id`),
	KEY `KEY_WORK_MSG_KEYWORD_TAG_KEYWORDID` (`keyword_id`),
	CONSTRAINT `KEY_WORK_MSG_KEYWORD_TAG_CORPID` FOREIGN KEY (`corp_id`) REFERENCES {{%work_corp}} (`id`),
	CONSTRAINT `KEY_WORK_MSG_KEYWORD_TAG_KEYWORDID` FOREIGN KEY (`keyword_id`) REFERENCES {{%work_msg_keyword_attachment}} (`id`)
) ENGINE = INNODB DEFAULT CHARSET = utf8mb4 COMMENT = '智能推荐关键词关联标签表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210305_064317_add_table_work_msg_keyword_tag cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210305_064317_add_table_work_msg_keyword_tag cannot be reverted.\n";

        return false;
    }
    */
}
