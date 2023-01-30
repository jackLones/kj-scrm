<?php

	use yii\db\Migration;

	/**
	 * Class m200522_063745_add_table_taobao_order
	 */
	class m200522_063745_add_table_taobao_order extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$sql = <<<SQL
CREATE TABLE {{%taobao_order}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned DEFAULT NULL COMMENT '商户id',
  `sign_id` int(11) unsigned DEFAULT NULL COMMENT '店铺id',
  `order_id` varchar(50) NOT NULL DEFAULT '' COMMENT '订单编号',
  `buyer_nick` varchar(50) NOT NULL DEFAULT '' COMMENT '买家会员名',
  `buyer_account` varchar(50) NOT NULL DEFAULT '' COMMENT '买家支付宝账号',
  `price` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '买家应付货款',
  `post_fee` decimal(12,2) NOT NULL DEFAULT '0' COMMENT '买家应付邮费',
  `cost_point` int(11) NOT NULL DEFAULT '0' COMMENT '买家支付积分',
  `total_fee` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '总金额',
  `back_point` int(11) NOT NULL DEFAULT '0' COMMENT '返点积分',
  `payment` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '买家实际支付金额',
  `order_status_id` int(11) NOT NULL DEFAULT '0' COMMENT '订单状态',
  `receiver_name` varchar(50) NOT NULL DEFAULT '' COMMENT '收货人姓名',
  `receiver_address` varchar(250) NOT NULL DEFAULT '' COMMENT '收货地址',
  `express` varchar(50) NOT NULL DEFAULT '' COMMENT '运送方式',
  `receiver_phone` varchar(50) NOT NULL DEFAULT '' COMMENT '收货人的手机号码',
  `pay_time` int(11) NOT NULL DEFAULT '0' COMMENT '订单付款时间',
  `title` varchar(250) NOT NULL DEFAULT '' COMMENT '宝贝标题',
  `baby_type` int(11) NOT NULL DEFAULT '0' COMMENT '宝贝种类',
  `baby_num` int(11) NOT NULL DEFAULT '0' COMMENT '宝贝总数量',
  `logistic_num` varchar(50) NOT NULL DEFAULT '' COMMENT '物流单号',
  `logistic_company` varchar(50) NOT NULL DEFAULT '' COMMENT '物流公司',
  `remark` varchar(250) NOT NULL DEFAULT '' COMMENT '订单备注',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '订单同步时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '订单同步修改时间',
  PRIMARY KEY (`id`),
  KEY `KEY_TAOBAO_ORDER_UID` (`uid`),
  CONSTRAINT `KEY_TAOBAO_ORDER_UID` FOREIGN KEY (`uid`) REFERENCES {{%user}} (`uid`),
  CONSTRAINT `KEY_TAOBAO_ORDER_SIGNID` FOREIGN KEY (`sign_id`) REFERENCES {{%application_sign}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='淘宝订单表';
SQL;

			$this->execute($sql);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200522_063745_add_table_taobao_order cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200522_063745_add_table_taobao_order cannot be reverted.\n";

			return false;
		}
		*/
	}
