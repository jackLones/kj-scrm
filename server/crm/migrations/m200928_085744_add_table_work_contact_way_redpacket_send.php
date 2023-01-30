<?php

use yii\db\Migration;

/**
 * Class m200928_085744_add_table_work_contact_way_redpacket_send
 */
class m200928_085744_add_table_work_contact_way_redpacket_send extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%work_contact_way_redpacket_send}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `corp_id` int(11) unsigned DEFAULT NULL COMMENT '授权的企业ID',
  `way_id` int(11) unsigned DEFAULT NULL COMMENT '红包活动活码ID',
  `rule_id` int(11) unsigned DEFAULT 0 COMMENT '红包规则ID',
  `user_id` int(11) unsigned DEFAULT NULL COMMENT '成员ID',
  `external_userid` int(11) unsigned DEFAULT NULL COMMENT '外部联系人ID',
  `send_money` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '发放金额',
  `is_send` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '是否发送红包（图文）1是0否',
  `status` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '领取状态0待领取1已领取2已过期3已领完（活动金额）4发放失败',
  `msg` VARCHAR(255) DEFAULT '' COMMENT '发放失败描述',
  `create_time` int(11) unsigned DEFAULT 0 COMMENT '创建时间',
  `send_time` int(11) unsigned DEFAULT 0 COMMENT '领取时间',
  `update_time` int(11) unsigned DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `KEY_WORK_CONTACT_WAY_REDPACKET_CORP_ID` (`corp_id`),
  KEY `KEY_WORK_CONTACT_WAY_REDPACKET_WAY_ID` (`way_id`),
  KEY `KEY_WORK_CONTACT_WAY_REDPACKET_EXTERNAL_USERID` (`external_userid`),
  KEY `KEY_WORK_CONTACT_WAY_REDPACKET_USER_ID` (`user_id`),
  CONSTRAINT `KEY_WORK_CONTACT_WAY_REDPACKET_EXTERNAL_USERID` FOREIGN KEY (`external_userid`) REFERENCES {{%work_external_contact}} (`id`),
  CONSTRAINT `KEY_WORK_CONTACT_WAY_REDPACKET_USER_ID` FOREIGN KEY (`user_id`) REFERENCES {{%work_user}} (`id`),
  CONSTRAINT `KEY_WORK_CONTACT_WAY_REDPACKET_CORP_ID` FOREIGN KEY (`corp_id`) REFERENCES {{%work_corp}} (`id`),
  CONSTRAINT `KEY_WORK_CONTACT_WAY_REDPACKET_WAY_ID` FOREIGN KEY (`way_id`) REFERENCES {{%work_contact_way_redpacket}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='红包活动发放表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200928_085744_add_table_work_contact_way_redpacket_send cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200928_085744_add_table_work_contact_way_redpacket_send cannot be reverted.\n";

        return false;
    }
    */
}
