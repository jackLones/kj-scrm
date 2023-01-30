<?php

use yii\db\Migration;

/**
 * Class m200715_110654_add_table_work_chat_remind_send
 */
class m200715_110654_add_table_work_chat_remind_send extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%work_chat_remind_send}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `audit_info_id` int(11) unsigned DEFAULT 0 COMMENT '会话内容ID',
  `chat_id` int(11) unsigned DEFAULT 0 COMMENT '群ID',
  `from_type` tinyint(1) unsigned DEFAULT 0 COMMENT '发送者身份：1、企业成员；2、外部联系人；3、群机器人',
  `user_id` int(11) unsigned DEFAULT NULL COMMENT '成员ID',
  `external_id` int(11) unsigned DEFAULT NULL COMMENT '外部联系人ID',
  `send_user_id` int(11) unsigned NOT NULL COMMENT '提醒人成员ID',
  `msgtype` char(32) NOT NULL COMMENT '消息类型：文本：text； 图片：image；语音：voice；视频：video；名片：card；链接：link；小程序：weapp；红包：redpacket',
  `content` VARCHAR(1000) NOT NULL DEFAULT '' COMMENT '提醒内容',
  `time` int(11) NOT NULL DEFAULT '0' COMMENT '发送时间',
  PRIMARY KEY (`id`),
  KEY `KEY_WORK_CHAT_REMIND_SEND_CHATID` (`chat_id`),
  KEY `KEY_WORK_CHAT_REMIND_SEND_FROM_TYPE` (`from_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='群提醒发送表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200715_110654_add_table_work_chat_remind_send cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200715_110654_add_table_work_chat_remind_send cannot be reverted.\n";

        return false;
    }
    */
}
