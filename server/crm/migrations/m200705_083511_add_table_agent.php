<?php

use yii\db\Migration;

/**
 * Class m200705_083511_add_table_agent
 */
class m200705_083511_add_table_agent extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%agent}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT 0 COMMENT '代理商用户id',
  `aname` varchar(255) NOT NULL DEFAULT '' COMMENT '公司名称',
  `lxname` varchar(255) NOT NULL DEFAULT '' COMMENT '联系人名称',
  `discount` float(2,1) DEFAULT '0.0' COMMENT '代理商折扣',
  `balance` float(30,2) NOT NULL DEFAULT '0.00' COMMENT '账户余额',
  `cash_deposit` float(30,2) NOT NULL DEFAULT '0.00' COMMENT '保证金',
  `is_contract` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否签约1是0否',
  `contract_time` int(11) NOT NULL DEFAULT 0 COMMENT '签约时间',
  `endtime` int(11) NOT NULL DEFAULT 0 COMMENT '签约到期时间',
  `province` int(10) NOT NULL DEFAULT 0 COMMENT '所在区域',
  `city` int(10) NOT NULL DEFAULT 0 COMMENT '所在市',
  `addtime` int(11) NOT NULL COMMENT '创建时间',
  `upttime` int(11) NOT NULL DEFAULT 0 COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `KEY_AGENT_UID` (`uid`),
  KEY `KEY_AGENT_IS_CONTRACT` (`is_contract`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='代理商信息表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200705_083511_add_table_agent cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200705_083511_add_table_agent cannot be reverted.\n";

        return false;
    }
    */
}
