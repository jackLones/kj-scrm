<?php

use yii\db\Migration;

/**
 * Class m200717_070628_add_table_limit_word_remind
 */
class m200717_070628_add_table_limit_word_remind extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		//成员违规提醒表
	    $sql = <<<SQL
CREATE TABLE {{%limit_word_remind}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `corp_id` int(11) unsigned NOT NULL COMMENT '企业微信id',
  `agent_id` int(11) unsigned DEFAULT NULL COMMENT '应用id',
  `limit_user_id` int(11) NOT NULL COMMENT '被监控成员id',
  `is_leader` tinyint(1) DEFAULT '0' COMMENT '是否通知部门负责人：1是 0否',
  `remind_user` text NOT NULL COMMENT '接收成员',
  `word_ids` text NOT NULL COMMENT '敏感词id',
  `status` tinyint(1) DEFAULT '2' COMMENT '0删除、1关闭、2开启',
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `add_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_LIMIT_WORD_REMIND_CORPID` (`corp_id`),
  CONSTRAINT `KEY_LIMIT_WORD_REMIND_CORPID` FOREIGN KEY (`corp_id`) REFERENCES {{%work_corp}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='成员违规提醒表';
SQL;
	    $this->execute($sql);

	    //违规监控表
	    $sql = <<<SQL
CREATE TABLE {{%limit_word_msg}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `corp_id` int(11) unsigned NOT NULL COMMENT '企业微信id',
  `word_id` int(11) unsigned DEFAULT NULL COMMENT '敏感词id',
  `audit_info_id` int(11) unsigned DEFAULT NULL COMMENT '会话内容ID',
  PRIMARY KEY (`id`),
  KEY `KEY_LIMIT_WORD_MSG_CORPID` (`corp_id`),
  KEY `KEY_LIMIT_WORD_MSG_WORDID` (`word_id`),
  KEY `KEY_LIMIT_WORD_MSG_AUDIT_INFOID` (`audit_info_id`),
  CONSTRAINT `KEY_LIMIT_WORD_MSG_CORPID` FOREIGN KEY (`corp_id`) REFERENCES {{%work_corp}} (`id`),
  CONSTRAINT `KEY_LIMIT_WORD_MSG_WORDID` FOREIGN KEY (`word_id`) REFERENCES {{%limit_word}} (`id`),
  CONSTRAINT `KEY_LIMIT_WORD_MSG_AUDIT_INFOID` FOREIGN KEY (`audit_info_id`) REFERENCES {{%work_msg_audit_info}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='违规监控表';
SQL;
	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200717_070628_add_table_limit_word_remind cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200717_070628_add_table_limit_word_remind cannot be reverted.\n";

        return false;
    }
    */
}
