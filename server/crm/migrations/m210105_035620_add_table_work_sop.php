<?php

use yii\db\Migration;

/**
 * Class m210105_035620_add_table_work_sop
 */
class m210105_035620_add_table_work_sop extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%work_sop}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned DEFAULT NULL COMMENT '账户ID',
  `sub_id` int(11) unsigned DEFAULT '0' COMMENT '子账户id',
  `corp_id` int(11) unsigned DEFAULT NULL COMMENT '授权的企业ID',
  `type` tinyint(1) unsigned DEFAULT NULL COMMENT '1新客培育、2客户跟进',
  `title` varchar(255) DEFAULT NULL COMMENT '规则名称',
  `user_ids` varchar(5000) DEFAULT NULL COMMENT '规则成员',
  `follow_id` int(11) unsigned DEFAULT '0' COMMENT '跟进状态id(type=2)',
  `is_all` tinyint(1) unsigned DEFAULT '1' COMMENT '是否全部客户1是0否',
  `task_id` int(11) unsigned DEFAULT '0' COMMENT '任务标签id(type=2)', 
  `no_send_type` tinyint(1) unsigned DEFAULT NULL COMMENT '不推送时间段1开启0关闭',
  `no_send_time` varchar(32) DEFAULT NULL COMMENT '不推送时间段',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '是否开启1是0否',
  `is_del` tinyint(1) unsigned DEFAULT '0' COMMENT '是否删除1是0否',
  PRIMARY KEY (`id`),
  KEY `KEY_WORK_SOP_UID` (`uid`),
  KEY `KEY_WORK_SOP_CORPID` (`corp_id`),
  CONSTRAINT `KEY_WORK_SOP_UID` FOREIGN KEY (`uid`) REFERENCES {{%user}} (`uid`),
  CONSTRAINT `KEY_WORK_SOP_CORPID` FOREIGN KEY (`corp_id`) REFERENCES {{%work_corp}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='SOP规则表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210105_035620_add_table_work_sop cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210105_035620_add_table_work_sop cannot be reverted.\n";

        return false;
    }
    */
}
