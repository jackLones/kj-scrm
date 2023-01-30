<?php

use yii\db\Migration;

/**
 * Class m200531_075705_add_table_work_chat_group
 */
class m200531_075705_add_table_work_chat_group extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%work_chat_group}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `corp_id` int(11) unsigned DEFAULT NULL COMMENT '授权的企业ID',
  `group_name` char(32) DEFAULT NULL COMMENT '群分组名称，长度限制为32个字以内（汉字或英文字母），分组名不可与其他组名重名',
  `sort` int(11) unsigned DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) unsigned DEFAULT 1 COMMENT '是否有效1是0否',
  PRIMARY KEY (`id`),
  KEY `KEY_WORK_CHAT_GROUP_CORPID` (`corp_id`),
  CONSTRAINT `KEY_WORK_CHAT_GROUP_CORPID` FOREIGN KEY (`corp_id`) REFERENCES {{%work_corp}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='客户群分组表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200531_075705_add_table_work_chat_group cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200531_075705_add_table_work_chat_group cannot be reverted.\n";

        return false;
    }
    */
}
