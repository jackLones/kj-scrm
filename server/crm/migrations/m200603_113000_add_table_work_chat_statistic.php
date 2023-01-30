<?php

use yii\db\Migration;

/**
 * Class m200603_113000_add_table_work_chat_statistic
 */
class m200603_113000_add_table_work_chat_statistic extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%work_chat_statistic}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `corp_id` int(11) unsigned DEFAULT NULL COMMENT '授权的企业ID',
  `owner_id` int(11) unsigned DEFAULT NULL COMMENT '群主用户ID',
  `owner` char(64) DEFAULT NULL COMMENT '群主ID',
  `new_chat_cnt` int(11) unsigned DEFAULT '0' COMMENT '新增客户群数量',
  `chat_total` int(11) unsigned DEFAULT '0' COMMENT '截至当天客户群总数量',
  `chat_has_msg` int(11) unsigned DEFAULT '0' COMMENT '截至当天有发过消息的客户群数量',
  `new_member_cnt` int(11) unsigned DEFAULT '0' COMMENT '客户群新增群人数',
  `member_total` int(11) unsigned DEFAULT '0' COMMENT '截至当天客户群总人数',
  `member_has_msg` int(11) unsigned DEFAULT '0' COMMENT '截至当天有发过消息的群成员数',
  `msg_total` int(11) unsigned DEFAULT '0' COMMENT '截至当天客户群消息总数',
  `time` int(11) unsigned DEFAULT '0' COMMENT '数据当日0点的时间戳',
  `create_time` int(11) unsigned DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_WORK_CHAT_STATISTIC_OWNERID` (`owner_id`),
  KEY `KEY_WORK_CHAT_STATISTIC_CORPID` (`corp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='客户群日统计数据';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200603_113000_add_table_work_chat_statistic cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200603_113000_add_table_work_chat_statistic cannot be reverted.\n";

        return false;
    }
    */
}
