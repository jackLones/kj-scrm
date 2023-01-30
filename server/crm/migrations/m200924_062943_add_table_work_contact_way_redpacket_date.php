<?php

use yii\db\Migration;

/**
 * Class m200924_062943_add_table_work_contact_way_redpacket_date
 */
class m200924_062943_add_table_work_contact_way_redpacket_date extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%work_contact_way_redpacket_date}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `way_id` int(11) unsigned NOT NULL COMMENT '渠道活码红包活动表ID',
  `type` tinyint(1) unsigned DEFAULT '0' COMMENT '0周1日期',
  `start_date` date DEFAULT NULL COMMENT '开始日期',
  `end_date` date DEFAULT NULL COMMENT '结束日期',
  `day` varchar(32) DEFAULT NULL COMMENT '周几',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_WORK_CONTACT_WAY_REDPACKET_DATE_WAY_ID` (`way_id`),
  CONSTRAINT `KEY_WORK_CONTACT_WAY_REDPACKET_DATE_WAY_ID` FOREIGN KEY (`way_id`) REFERENCES {{%work_contact_way_redpacket}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='渠道活码红包活动日期表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200924_062943_add_table_work_contact_way_redpacket_date cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200924_062943_add_table_work_contact_way_redpacket_date cannot be reverted.\n";

        return false;
    }
    */
}
