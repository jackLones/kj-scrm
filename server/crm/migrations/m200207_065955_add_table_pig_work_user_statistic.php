<?php

use yii\db\Migration;

/**
 * Class m200207_065955_add_table_pig_work_user_statistic
 */
class m200207_065955_add_table_pig_work_user_statistic extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
DROP TABLE IF EXISTS {{%work_user_statistic}};
CREATE TABLE {{%work_user_statistic}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `corp_id` int(11) unsigned DEFAULT NULL COMMENT '授权的企业ID',
  `userid` char(64) DEFAULT NULL COMMENT '企业成员userid',
  `new_apply_cnt` int(11) unsigned DEFAULT 0 COMMENT '发起申请数',
  `new_contact_cnt` int(11) unsigned DEFAULT 0 COMMENT '新增客户数',
  `negative_feedback_cnt` int(11) unsigned DEFAULT 0 COMMENT '删除/拉黑成员的客户数',
  `chat_cnt` int(11) unsigned DEFAULT 0 COMMENT '聊天总数',
  `message_cnt` int(11) unsigned DEFAULT 0 COMMENT '发送消息数',
  `reply_percentage` char(8) DEFAULT '' COMMENT '已回复聊天占比',
  `avg_reply_time` char(16) DEFAULT '' COMMENT '平均首次回复时长(分钟)',
  `time` int(11) unsigned DEFAULT 0 COMMENT '数据当日0点的时间戳',
  `data_time` char(16) DEFAULT '' COMMENT '统计时间 如2020-02-07 ',
  `create_time` timestamp NULL DEFAULT NULL COMMENT '创建日期',
  PRIMARY KEY (`id`),
  KEY `KEY_WORK_USER_USERID` (`userid`),
  KEY `KEY_WORK_USER_CORPID` (`corp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='成员客户数据日统计';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200207_065955_add_table_pig_work_user_statistic cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200207_065955_add_table_pig_work_user_statistic cannot be reverted.\n";

        return false;
    }
    */
}
