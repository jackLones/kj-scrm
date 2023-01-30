<?php

use yii\db\Migration;

/**
 * Class m200924_091550_add_table_work_contact_way_redpacket_date_user
 */
class m200924_091550_add_table_work_contact_way_redpacket_date_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%work_contact_way_redpacket_date_user}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date_id` int(11) unsigned NOT NULL COMMENT '红包活动日期表ID',
  `time` char(32) DEFAULT NULL COMMENT '具体时间',
  `user_key` text COMMENT '用户选择的key值',
  `department` varchar(255) DEFAULT NULL COMMENT '部门id',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_WORK_CONTACT_WAY_REDPACKET_DATE_USER_DATE_ID` (`date_id`),
  KEY `KEY_WORK_CONTACT_WAY_REDPACKET_DATE_USER_TIME` (`time`),
  CONSTRAINT `KEY_WORK_CONTACT_WAY_REDPACKET_DATE_USER_DATE_ID` FOREIGN KEY (`date_id`) REFERENCES {{%work_contact_way_redpacket_date}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='渠道活码红包活动日期成员表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200924_091550_add_table_work_contact_way_redpacket_date_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200924_091550_add_table_work_contact_way_redpacket_date_user cannot be reverted.\n";

        return false;
    }
    */
}
