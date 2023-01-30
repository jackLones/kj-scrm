<?php

use yii\db\Migration;

/**
 * Class m201002_094211_add_table_red_pack_chat_send_rule
 */
class m201002_094211_add_table_red_pack_chat_send_rule extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%red_pack_chat_send_rule}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `corp_id` int(11) unsigned DEFAULT NULL COMMENT '企业微信ID',
  `user_id` int(11) unsigned DEFAULT NULL COMMENT '员工ID',
  `chat_id` int(11) unsigned DEFAULT NULL COMMENT '群ID',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '单个红包金额类型：1、固定金额，2、随机金额',
  `redpacket_amount` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '红包金额',
  `redpacket_num` int(10) NOT NULL DEFAULT 0 COMMENT '红包个数',
  `get_amount` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '领取金额',
  `get_num` int(10) NOT NULL DEFAULT 0 COMMENT '领取个数',
  `amount_allot` text COMMENT '红包分配',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '红包备注',
  `des` varchar(500) NOT NULL DEFAULT '' COMMENT '描述（祝福语）',
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `KEY_RED_PACK_CHAT_SEND_RULE_CORPID` (`corp_id`),
  KEY `KEY_RED_PACK_CHAT_SEND_RULE_USER_ID` (`user_id`),
  KEY `KEY_RED_PACK_CHAT_SEND_RULE_CHAT_ID` (`chat_id`),
  CONSTRAINT `KEY_RED_PACK_CHAT_SEND_RULE_CORPID` FOREIGN KEY (`corp_id`) REFERENCES {{%work_corp}} (`id`),
  CONSTRAINT `KEY_RED_PACK_CHAT_SEND_RULE_USER_ID` FOREIGN KEY (`user_id`) REFERENCES {{%work_user}} (`id`),
  CONSTRAINT `KEY_RED_PACK_CHAT_SEND_RULE_CHAT_ID` FOREIGN KEY (`chat_id`) REFERENCES {{%work_chat}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='群红包发放规则表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201002_094211_add_table_red_pack_chat_send_rule cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201002_094211_add_table_red_pack_chat_send_rule cannot be reverted.\n";

        return false;
    }
    */
}
