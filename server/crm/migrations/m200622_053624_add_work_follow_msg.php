<?php

use yii\db\Migration;

/**
 * Class m200622_053624_add_work_follow_msg
 */
class m200622_053624_add_work_follow_msg extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%work_follow_msg}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL COMMENT 'uid',
  `agentid` int(11) unsigned DEFAULT NULL COMMENT '应用id',
  `user_id` int(11) unsigned DEFAULT NULL COMMENT '成员ID',
  `userid` char(64) DEFAULT NULL COMMENT '企业成员userid',
  `is_all` tinyint(1) DEFAULT '0' COMMENT '是否接收全员数据1是0否',
  `follow_party` varchar(255) DEFAULT NULL COMMENT '接收部门',
  `follow_user` varchar(255) DEFAULT NULL COMMENT '接收成员',
  `send_time` text COMMENT '发送时间json',
  `send_content` text COMMENT '发送内容',  
  `status` tinyint(1) DEFAULT 0 COMMENT '是否有效1是0否',
  `is_del` tinyint(1) DEFAULT 0 COMMENT '是否删除1是0否',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `upt_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `KEY_WORK_FOLLOW_MSG_UID` (`uid`),
  KEY `KEY_WORK_FOLLOW_MSG_AGENTID` (`agentid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='跟进提醒设置表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200622_053624_add_work_follow_msg cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200622_053624_add_work_follow_msg cannot be reverted.\n";

        return false;
    }
    */
}
