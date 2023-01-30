<?php

use yii\db\Migration;

/**
 * Class m200924_092748_add_table_work_contact_way_redpacket_verify_date
 */
class m200924_092748_add_table_work_contact_way_redpacket_verify_date extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%work_contact_way_redpacket_verify_date}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `way_id` int(11) unsigned DEFAULT NULL COMMENT '红包活动渠道活码ID',
  `start_time` char(30) DEFAULT NULL COMMENT '开始时间',
  `end_time` char(30) DEFAULT NULL COMMENT '结束时间',
  `create_time` int(11) unsigned DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_WORK_CONTACT_WAY_REDPACKET_VERIFY_DATE_WAY_ID` (`way_id`),
  CONSTRAINT `KEY_WORK_CONTACT_WAY_REDPACKET_VERIFY_DATE_WAY_ID` FOREIGN KEY (`way_id`) REFERENCES {{%work_contact_way_redpacket}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='红包活动渠道活码验证时间表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200924_092748_add_table_work_contact_way_redpacket_verify_date cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200924_092748_add_table_work_contact_way_redpacket_verify_date cannot be reverted.\n";

        return false;
    }
    */
}
