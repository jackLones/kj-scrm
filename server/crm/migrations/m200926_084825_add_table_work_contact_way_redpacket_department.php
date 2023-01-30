<?php

use yii\db\Migration;

/**
 * Class m200926_084825_add_table_work_contact_way_redpacket_department
 */
class m200926_084825_add_table_work_contact_way_redpacket_department extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%work_contact_way_redpacket_department}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `config_id` int(11) unsigned DEFAULT NULL COMMENT '红包活动渠道活码表id',
  `department_id` int(11) unsigned DEFAULT NULL COMMENT '部门ID',
  PRIMARY KEY (`id`),
  KEY `KEY_WORK_CONTACT_WAY_REDPACKET_DEPARTMENT_CONFIGID` (`config_id`),
  KEY `KEY_WORK_CONTACT_WAY_REDPACKET_DEPARTMENT_DEPARTMENTID` (`department_id`),
  CONSTRAINT `KEY_WORK_CONTACT_WAY_REDPACKET_DEPARTMENT_CONFIGID` FOREIGN KEY (`config_id`) REFERENCES {{%work_contact_way_redpacket}} (`id`),
  CONSTRAINT `KEY_WORK_CONTACT_WAY_REDPACKET_DEPARTMENT_DEPARTMENTID` FOREIGN KEY (`department_id`) REFERENCES {{%work_department}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='红包活动渠道活码部门表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200926_084825_add_table_work_contact_way_redpacket_department cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200926_084825_add_table_work_contact_way_redpacket_department cannot be reverted.\n";

        return false;
    }
    */
}
