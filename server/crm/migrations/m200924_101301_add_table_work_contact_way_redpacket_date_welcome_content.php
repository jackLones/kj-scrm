<?php

use yii\db\Migration;

/**
 * Class m200924_101301_add_table_work_contact_way_redpacket_date_welcome_content
 */
class m200924_101301_add_table_work_contact_way_redpacket_date_welcome_content extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%work_contact_way_redpacket_date_welcome_content}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date_id` int(11) unsigned DEFAULT NULL COMMENT '红包活动渠道活码欢迎语日期表ID',
  `content` text COMMENT '欢迎语内容',
  `welcome` text COMMENT '欢迎语给前端用的',
  `start_time` char(32) DEFAULT NULL COMMENT '开始时刻',
  `end_time` char(32) DEFAULT NULL COMMENT '结束时刻',
  `create_time` int(11) unsigned DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_WORK_CONTACT_WAY_REDPACKET_DATE_WELCOME_CONTENT_DATE_ID` (`date_id`),
  CONSTRAINT `KEY_WORK_CONTACT_WAY_REDPACKET_DATE_WELCOME_CONTENT_DATE_ID` FOREIGN KEY (`date_id`) REFERENCES {{%work_contact_way_redpacket_date_welcome}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='红包活动渠道活码欢迎语内容表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200924_101301_add_table_work_contact_way_redpacket_date_welcome_content cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200924_101301_add_table_work_contact_way_redpacket_date_welcome_content cannot be reverted.\n";

        return false;
    }
    */
}
