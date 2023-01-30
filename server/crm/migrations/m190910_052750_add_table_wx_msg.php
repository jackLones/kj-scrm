<?php

use yii\db\Migration;

/**
 * Class m190910_052750_add_table_wx_msg
 */
class m190910_052750_add_table_wx_msg extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    	$sql = <<<SQL
CREATE TABLE {{%wx_msg}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(256) NOT NULL COMMENT '消息去重标识',
  `msg_type` varchar(64) DEFAULT NULL COMMENT '消息类别',
  `msg_type_value` varchar(128) DEFAULT NULL COMMENT '消息类别',
  `data` longtext COMMENT '事件解密后数据',
  `status` tinyint(1) DEFAULT '0' COMMENT '消息类别名称',
  `update_time` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_WX_MSG_TYPE` (`msg_type`),
  KEY `KEY_WX_MSG_TYPE_VALUE` (`msg_type_value`),
  KEY `KEY_WX_MSG_STATUS` (`status`),
  KEY `KEY_WX_MSG_KEY` (`key`(35))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='微信消息表';
SQL;

    	$this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190910_052750_add_table_wx_msg cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190910_052750_add_table_wx_msg cannot be reverted.\n";

        return false;
    }
    */
}
