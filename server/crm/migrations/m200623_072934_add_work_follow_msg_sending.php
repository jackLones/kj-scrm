<?php

use yii\db\Migration;

/**
 * Class m200623_072934_add_work_follow_msg_sending
 */
class m200623_072934_add_work_follow_msg_sending extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%work_follow_msg_sending}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `corp_id` int(11) unsigned DEFAULT NULL COMMENT '企业ID',
  `agentid` int(11) unsigned DEFAULT NULL COMMENT '授权方应用id',
  `msg_id` int(11) unsigned DEFAULT NULL COMMENT '跟进提醒表id',
  `date_time` int(8) unsigned DEFAULT NULL COMMENT '当日时间（Ymd）',
  `send_time` varchar(50) DEFAULT NULL COMMENT '发送时间（时:分）',
  `push_type` tinyint(1) DEFAULT '0' COMMENT '0立即发送1指定时间发送',
  `queue_id` tinyint(1) DEFAULT '0' COMMENT '队列id',
  `status` tinyint(1) DEFAULT '0' COMMENT '发送状态 0未发送 1已发送 2发送失败',
  `push_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '成功发送时间',
  `error_msg` varchar(255) DEFAULT '' COMMENT '错误信息',
  `error_code` int(11) unsigned DEFAULT '0' COMMENT '错误码',
  `is_del` tinyint(1) DEFAULT '0' COMMENT '删除状态 0 未删除 1 已删除',
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `KEY_WORK_FOLLOW_MSG_SENDING_CORPID` (`corp_id`),
  KEY `KEY_WORK_FOLLOW_MSG_SENDING_MSGID` (`msg_id`),
  CONSTRAINT `KEY_WORK_FOLLOW_MSG_SENDING_CORPID` FOREIGN KEY (`corp_id`) REFERENCES {{%work_corp}} (`id`),
  CONSTRAINT `KEY_WORK_FOLLOW_MSG_SENDING_MSGID` FOREIGN KEY (`msg_id`) REFERENCES {{%work_follow_msg}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='跟进提醒消息发送表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200623_072934_add_work_follow_msg_sending cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200623_072934_add_work_follow_msg_sending cannot be reverted.\n";

        return false;
    }
    */
}
