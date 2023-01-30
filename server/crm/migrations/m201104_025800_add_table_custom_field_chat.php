<?php

use yii\db\Migration;

/**
 * Class m201104_025800_add_table_custom_field_chat
 */
class m201104_025800_add_table_custom_field_chat extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%custom_field_chat}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned DEFAULT NULL COMMENT '商户的uid',
  `fieldid` int(10) unsigned DEFAULT NULL COMMENT '属性字段表id',
  `time` int(10) NOT NULL COMMENT '时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0关闭，1开启',
  PRIMARY KEY (`id`),
  KEY `KEY_CUSTOM_FIELD_CHAT_UID` (`uid`),
  KEY `KEY_CUSTOM_FIELD_CHAT_FIELDID` (`fieldid`),
  CONSTRAINT `KEY_CUSTOM_FIELD_CHAT_UID` FOREIGN KEY (`uid`) REFERENCES {{%user}} (`uid`),
  CONSTRAINT `KEY_CUSTOM_FIELD_CHAT_FIELDID` FOREIGN KEY (`fieldid`) REFERENCES {{%custom_field}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='默认高级属性字段客户群设置表';
SQL;
	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201104_025800_add_table_custom_field_chat cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201104_025800_add_table_custom_field_chat cannot be reverted.\n";

        return false;
    }
    */
}
