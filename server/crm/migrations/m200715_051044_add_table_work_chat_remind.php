<?php

use yii\db\Migration;

/**
 * Class m200715_051044_add_table_work_chat_remind
 */
class m200715_051044_add_table_work_chat_remind extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$sql = <<<SQL
CREATE TABLE {{%work_chat_remind}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `corp_id` int(11) unsigned NOT NULL COMMENT '企业微信id',
  `agentid` int(11) unsigned DEFAULT NULL COMMENT '应用id',
  `title` varchar(255) DEFAULT NULL COMMENT '规则名称',
  `chat_ids` varchar(1000) DEFAULT NULL COMMENT '适用群id集合',
  `remind_user` varchar(2000) DEFAULT NULL COMMENT '接收成员',
  `is_image` tinyint(1) DEFAULT '0' COMMENT '是否图片提醒1是0否',
  `is_link` tinyint(1) DEFAULT '0' COMMENT '是否链接提醒1是0否',
  `is_weapp` tinyint(1) DEFAULT '0' COMMENT '是否小程序提醒1是0否',
  `is_card` tinyint(1) DEFAULT '0' COMMENT '是否名片提醒1是0否',
  `is_voice` tinyint(1) DEFAULT '0' COMMENT '是否音频提醒1是0否',
  `is_redpacket` tinyint(1) DEFAULT '0' COMMENT '是否红包提醒1是0否',
  `is_text` tinyint(1) DEFAULT '0' COMMENT '是否文本关键词提醒1是0否',
  `keyword` text COMMENT '关键词集合',
  `status` tinyint(1) DEFAULT '0' COMMENT '是否有效1是0否',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `upt_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `KEY_WORK_CHAT_REMIND_CORPID` (`corp_id`),
  KEY `KEY_WORK_CHAT_REMIND_AGENTID` (`agentid`),
  KEY `KEY_WORK_CHAT_REMIND_STATUS` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='群提醒设置表';
SQL;

		$this->execute($sql);
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200715_051044_add_table_work_chat_remind cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200715_051044_add_table_work_chat_remind cannot be reverted.\n";

        return false;
    }
    */
}
