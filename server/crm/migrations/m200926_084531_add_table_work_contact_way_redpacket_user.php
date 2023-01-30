<?php

use yii\db\Migration;

/**
 * Class m200926_084531_add_table_work_contact_way_redpacket_user
 */
class m200926_084531_add_table_work_contact_way_redpacket_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%work_contact_way_redpacket_user}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `config_id` int(11) unsigned DEFAULT NULL COMMENT '红包活动渠道活码表id',
  `user_id` int(11) unsigned DEFAULT NULL COMMENT '成员ID',
  PRIMARY KEY (`id`),
  KEY `KEY_WORK_CONTACT_WAY_REDPACKET_USER_CONFIGID` (`config_id`),
  KEY `KEY_WORK_CONTACT_WAY_REDPACKET_USER_USERID` (`user_id`),
  CONSTRAINT `KEY_WORK_CONTACT_WAY_REDPACKET_USER_CONFIGID` FOREIGN KEY (`config_id`) REFERENCES {{%work_contact_way_redpacket}} (`id`),
  CONSTRAINT `KEY_WORK_CONTACT_WAY_REDPACKET_USER_USERID` FOREIGN KEY (`user_id`) REFERENCES {{%work_user}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='红包活动渠道活码成员表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200926_084531_add_table_work_contact_way_redpacket_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200926_084531_add_table_work_contact_way_redpacket_user cannot be reverted.\n";

        return false;
    }
    */
}
