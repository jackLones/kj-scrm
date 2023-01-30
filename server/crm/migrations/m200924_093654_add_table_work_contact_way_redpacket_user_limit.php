<?php

use yii\db\Migration;

/**
 * Class m200924_093654_add_table_work_contact_way_redpacket_user_limit
 */
class m200924_093654_add_table_work_contact_way_redpacket_user_limit extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%work_contact_way_redpacket_user_limit}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `way_id` int(11) unsigned DEFAULT NULL COMMENT '红包活动渠道活码ID',
  `user_id` int(11) unsigned DEFAULT NULL COMMENT '成员ID',
  `name` char(64) DEFAULT NULL COMMENT '员工名称',
  `limit` int(11) unsigned DEFAULT NULL COMMENT '每天添加的上限',
  `create_time` int(11) unsigned DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_WORK_CONTACT_WAY_REDPACKET_USER_LIMIT_WAY_ID` (`way_id`),
  KEY `KEY_WORK_CONTACT_WAY_REDPACKET_USER_LIMIT_USER_ID` (`user_id`),
  CONSTRAINT `KEY_WORK_CONTACT_WAY_REDPACKET_USER_LIMIT_USER_ID` FOREIGN KEY (`user_id`) REFERENCES {{%work_user}} (`id`),
  CONSTRAINT `KEY_WORK_CONTACT_WAY_REDPACKET_USER_LIMIT_WAY_ID` FOREIGN KEY (`way_id`) REFERENCES {{%work_contact_way_redpacket}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='红包活动渠道活码成员添加客户上限表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200924_093654_add_table_work_contact_way_redpacket_user_limit cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200924_093654_add_table_work_contact_way_redpacket_user_limit cannot be reverted.\n";

        return false;
    }
    */
}
