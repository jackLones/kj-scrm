<?php

use yii\db\Migration;

/**
 * Class m200414_114211_add_table_work_external_contact_follow_record
 */
class m200414_114211_add_table_work_external_contact_follow_record extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%work_external_contact_follow_record}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL DEFAULT '0' COMMENT '商户id',
  `external_id` int(10) NOT NULL COMMENT '外部联系人ID',
  `sub_id` int(10) NOT NULL DEFAULT '0' COMMENT '子账户ID',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '成员ID',
  `record` text NOT NULL DEFAULT '' COMMENT '跟进记录',
  `time` int(10) NOT NULL DEFAULT '0' COMMENT '时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否有效1是0否',
  PRIMARY KEY (`id`),
  KEY `KEY_FOLLOW_RECORD_UID` (`uid`),
  KEY `KEY_FOLLOW_RECORD_EXTERNAL_ID` (`external_id`),
  KEY `KEY_FOLLOW_RECORD_SUB_ID` (`sub_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='客户跟进记录表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200414_114211_add_table_work_external_contact_follow_record cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200414_114211_add_table_work_external_contact_follow_record cannot be reverted.\n";

        return false;
    }
    */
}
