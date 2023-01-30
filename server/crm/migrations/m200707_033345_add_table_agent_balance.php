<?php

use yii\db\Migration;

/**
 * Class m200707_033345_add_table_agent_balance
 */
class m200707_033345_add_table_agent_balance extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%agent_balance}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT 0 COMMENT '代理商用户id',
  `balance` float(11,2) DEFAULT NULL COMMENT '金额',
  `type` tinyint(1) DEFAULT 0 COMMENT '金额变化类别 0：减少、1：增加',
  `blance_type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '明细类型1充值 2提单 9其他',
  `des` longtext COMMENT '描述',
  `order_id` int(11) NOT NULL DEFAULT 0 COMMENT '订单ID',
  `operator_type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '操作者类别1总后台2财务3代理商',
  `time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_AGENT_BALANCE_UID` (`uid`),
  KEY `KEY_AGENT_BALANCE_TYPE` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='代理商余额明细表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200707_033345_add_table_agent_balance cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200707_033345_add_table_agent_balance cannot be reverted.\n";

        return false;
    }
    */
}
