<?php

use yii\db\Migration;

/**
 * Class m200708_024757_add_table_agent_order
 */
class m200708_024757_add_table_agent_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%agent_order}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `agent_uid` int(11) NOT NULL DEFAULT '0' COMMENT '代理商用户id',
  `uid` int(11) unsigned DEFAULT NULL COMMENT '用户ID',
  `type` tinyint(2) DEFAULT NULL COMMENT '订单类别 1：新开、2：延期、3：升级、4：降级',
  `money` float(11,2) unsigned DEFAULT NULL COMMENT '实际价格',
  `discount` float(3,2) unsigned DEFAULT NULL COMMENT '折扣',
  `original_price` float(11,2) unsigned DEFAULT NULL COMMENT '原价',
  `status` tinyint(1) DEFAULT 1 COMMENT '订单状态 1：未审核、2：已审核、3：已撤销',
  `package_id` int(11) unsigned DEFAULT 0 COMMENT '套餐ID',
  `package_time` int(11) NOT NULL DEFAULT '0' COMMENT '套餐时长',
  `time_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '套餐时长类型:1日2月3年',
  `end_time` int(11) NOT NULL DEFAULT '0' COMMENT '套餐失效时间',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间',
  `pass_time` int(11) DEFAULT '0' COMMENT '提单审核时间',
  `eid` int(11) NOT NULL DEFAULT '0' COMMENT '代理商员工id',
  PRIMARY KEY (`id`),
  KEY `KEY_AGENT_ORDER_AGENT_UID` (`agent_uid`),
  KEY `KEY_AGENT_ORDER_UID` (`uid`),
  KEY `KEY_AGENT_ORDER_TYPE` (`type`),
  KEY `KEY_AGENT_ORDER_STATUS` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='代理商提单表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200708_024757_add_table_agent_order cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200708_024757_add_table_agent_order cannot be reverted.\n";

        return false;
    }
    */
}
