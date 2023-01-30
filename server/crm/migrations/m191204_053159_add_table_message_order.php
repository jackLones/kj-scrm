<?php

use yii\db\Migration;

/**
 * Class m191204_053159_add_table_message_order
 */
class m191204_053159_add_table_message_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%message_order}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned DEFAULT NULL COMMENT '用户ID',
  `order_id` varchar(50) NOT NULL DEFAULT '' COMMENT '支付订单号',
  `pay_way` varchar(50) NOT NULL COMMENT '支付方式 weixin 等',
  `pay_type` varchar(50) NOT NULL COMMENT '支付类型 ',
  `goods_type` varchar(50) NOT NULL COMMENT '商品类型',
  `goods_id` int(11) NOT NULL DEFAULT '0' COMMENT '产品id',
  `goods_name` varchar(200) NOT NULL DEFAULT '' COMMENT '产品名称',
  `goods_describe` text  COMMENT '产品描述',
  `goods_price` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '产品价格',
  `add_time` int(11) NOT NULL COMMENT '创建时间',
  `paytime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '支付时间',
  `truename` varchar(250) NOT NULL DEFAULT '' COMMENT '支付人姓名',
  `ispay` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1已支付',
  `openid` varchar(250) NOT NULL DEFAULT '',
  `transaction_id` varchar(250) NOT NULL DEFAULT '' COMMENT '第三方支付订单号',
  PRIMARY KEY (`id`),
  KEY `KEY_MESSAGE_ORDER_UID` (`uid`),
  CONSTRAINT `KEY_MESSAGE_ORDER_UID` FOREIGN KEY (`uid`) REFERENCES {{%user}} (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='短信购买订单表';
SQL;
	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191204_053159_add_table_message_order cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191204_053159_add_table_message_order cannot be reverted.\n";

        return false;
    }
    */
}
