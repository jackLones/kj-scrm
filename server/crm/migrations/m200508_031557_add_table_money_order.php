<?php

use yii\db\Migration;

/**
 * Class m200508_031557_add_table_money_order
 */
class m200508_031557_add_table_money_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%money_order}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`order_id` varchar(50) NOT NULL DEFAULT '' COMMENT '支付订单号',
  `uid` int(10) NOT NULL DEFAULT '0' COMMENT '商户id',
  `corp_id` int(10) NOT NULL DEFAULT '0' COMMENT '企业微信id',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '成员ID',
  `external_id` int(10) NOT NULL COMMENT '外部联系人ID',
	`goods_type` varchar(50) NOT NULL COMMENT '商品类型 sendMoney企业付款到零钱 redPacket发红包',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '金额',
  `send_time` int(10) NOT NULL DEFAULT '0' COMMENT '发送时间',
	`remark` varchar(255) NOT NULL DEFAULT '' COMMENT '红包备注',
	`message` varchar(255) NOT NULL DEFAULT '' COMMENT '留言',
	`transaction_id` varchar(100) DEFAULT '' COMMENT '支付订单号',
  `third_id` varchar(100) DEFAULT '' COMMENT '第三方订单号',
  `shop` varchar(255) DEFAULT '' COMMENT '第三方店铺',
  `account` varchar(255) DEFAULT '' COMMENT '购物账号',
	`extrainfo` varchar(255) DEFAULT '' COMMENT '额外信息',
  PRIMARY KEY (`id`),
  KEY `KEY_MONEY_ORDER_UID` (`uid`),
  KEY `KEY_MONEY_ORDER_CORPID` (`corp_id`),
  KEY `KEY_MONEY_ORDER_USERID` (`user_id`),
  KEY `KEY_MONEY_ORDER_EXTERNALID` (`external_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='发送红包订单表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200508_031557_add_table_money_order cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200508_031557_add_table_money_order cannot be reverted.\n";

        return false;
    }
    */
}
