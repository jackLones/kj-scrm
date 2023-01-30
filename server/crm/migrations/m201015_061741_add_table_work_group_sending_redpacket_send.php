<?php

use yii\db\Migration;

/**
 * Class m201015_061741_add_table_work_group_sending_redpacket_send
 */
class m201015_061741_add_table_work_group_sending_redpacket_send extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%work_group_sending_redpacket_send}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `corp_id` int(11) unsigned DEFAULT NULL COMMENT '授权的企业ID',
  `send_id` int(11) unsigned DEFAULT NULL COMMENT '群发活动ID',
  `group_send_id` int(11) unsigned DEFAULT NULL COMMENT '群发明细ID(work_tag_group_statistic)',
  `rule_id` int(11) unsigned DEFAULT '0' COMMENT '红包规则ID',
  `user_id` int(11) unsigned DEFAULT NULL COMMENT '成员ID',
  `external_userid` int(11) unsigned DEFAULT NULL COMMENT '外部联系人ID',
  `send_money` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '发放金额',
  `is_send` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否发送红包（图文）1是0否',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '领取状态0待领取1已领取2已过期3已领完（活动金额）4发放失败',
  `msg` varchar(255) DEFAULT '' COMMENT '发放失败描述',
  `create_time` int(11) unsigned DEFAULT '0' COMMENT '创建时间',
  `send_time` int(11) unsigned DEFAULT '0' COMMENT '领取时间',
  `update_time` int(11) unsigned DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `KEY_WORK_GROUP_SENDING_REDPACKET_SEND_CORP_ID` (`corp_id`),
  KEY `KEY_WORK_GROUP_SENDING_REDPACKET_SEND_SEND_ID` (`send_id`),
  KEY `KEY_WORK_GROUP_SENDING_REDPACKET_SEND_GROUP_SEND_ID` (`group_send_id`),
  KEY `KEY_WORK_GROUP_SENDING_REDPACKET_SEND_EXTERNAL_USERID` (`external_userid`),
  KEY `KEY_WORK_GROUP_SENDING_REDPACKET_SEND_USER_ID` (`user_id`),
  CONSTRAINT `KEY_WORK_GROUP_SENDING_REDPACKET_SEND_CORP_ID` FOREIGN KEY (`corp_id`) REFERENCES {{%work_corp}} (`id`),
  CONSTRAINT `KEY_WORK_GROUP_SENDING_REDPACKET_SEND_EXTERNAL_USERID` FOREIGN KEY (`external_userid`) REFERENCES {{%work_external_contact}} (`id`),
  CONSTRAINT `KEY_WORK_GROUP_SENDING_REDPACKET_SEND_USER_ID` FOREIGN KEY (`user_id`) REFERENCES {{%work_user}} (`id`),
  CONSTRAINT `KEY_WORK_GROUP_SENDING_REDPACKET_SEND_SEND_ID` FOREIGN KEY (`send_id`) REFERENCES {{%work_group_sending}} (`id`),
  CONSTRAINT `KEY_WORK_GROUP_SENDING_REDPACKET_SEND_GROUP_SEND_ID` FOREIGN KEY (`group_send_id`) REFERENCES {{%work_tag_group_statistic}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='群发活动红包发放表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201015_061741_add_table_work_group_sending_redpacket_send cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201015_061741_add_table_work_group_sending_redpacket_send cannot be reverted.\n";

        return false;
    }
    */
}
