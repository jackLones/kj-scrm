<?php

	use yii\db\Migration;

	/**
	 * Class m200528_065049_add_table_red_pack_join
	 */
	class m200528_065049_add_table_red_pack_join extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
//红包裂变参与表
			$sql = <<<SQL
CREATE TABLE {{%red_pack_join}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned DEFAULT NULL COMMENT '账户ID',
  `rid` int(11) unsigned DEFAULT NULL COMMENT '裂变任务id',
  `external_id` int(11) DEFAULT NULL COMMENT '外部联系人id',
  `openid` varchar(64) DEFAULT '' COMMENT '外部联系人openid',
  `config_id` varchar(64) DEFAULT '' COMMENT '联系方式的配置id',
  `qr_code` varchar(255) DEFAULT '' COMMENT '联系二维码的URL',
  `state` varchar(64) DEFAULT '' COMMENT '企业自定义的state参数，用于区分不同的添加渠道，在调用“获取外部联系人详情”时会返回该参数值',
  `help_num` int(11) NOT NULL DEFAULT '0' COMMENT '有效助力数',
  `invite_amount` int(11) NOT NULL DEFAULT '0' COMMENT '裂变人数数量',
  `redpack_price` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '裂变红包金额',
  `first_amount` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '首拆金额',
  `rest_amount` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '裂变成功剩余金额',
  `friend_amount` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '好友拆红包金额',
  `status` tinyint(1) DEFAULT '0' COMMENT '任务状态：0未完成、1进行中、2已完成',
  `first_send_status` tinyint(1) DEFAULT '0' COMMENT '首拆发放状态：0未发放、1已发放',
  `first_send_type` tinyint(1) DEFAULT '0' COMMENT '首拆发放类型：1零钱发放、2标记发放',
  `send_status` tinyint(1) DEFAULT '0' COMMENT '剩余发放状态：0未发放、1已发放',
  `send_type` tinyint(1) DEFAULT '0' COMMENT '剩余发放类型：1零钱发放、2标记发放',
  `join_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '参与时间',
  `complete_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '完成时间',
  `complete_second` int(11) NOT NULL DEFAULT '0' COMMENT '完成耗时',
  `is_remind` tinyint(1) DEFAULT '0' COMMENT '是否需要提醒：0否、1是',
  PRIMARY KEY (`id`),
  CONSTRAINT `KEY_RED_PACK_JOIN_UID` FOREIGN KEY (`uid`) REFERENCES {{%user}} (`uid`),
  CONSTRAINT `KEY_RED_PACK_JOIN_RID` FOREIGN KEY (`rid`) REFERENCES {{%red_pack}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='红包裂变参与表';
SQL;
			$this->execute($sql);

			//红包裂变参与助力表
			$sql = <<<SQL
CREATE TABLE {{%red_pack_help_detail}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `rid` int(11) unsigned DEFAULT NULL COMMENT '裂变任务id',
  `jid` int(11) unsigned DEFAULT NULL COMMENT '参与表id',
  `external_id` int(11) DEFAULT NULL COMMENT '外部联系人id',
  `openid` varchar(64) DEFAULT '' COMMENT '外部联系人openid',
  `amount` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '红包金额',
  `status` tinyint(1) DEFAULT '0' COMMENT '有效状态：0无效、1有效',
  `send_status` tinyint(1) DEFAULT '0' COMMENT '发放状态：0未发放、1已发放',
  `send_type` tinyint(1) DEFAULT '0' COMMENT '发放类型：1零钱发放、2标记发放',
  `help_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '助力时间',
  `is_remind` tinyint(1) DEFAULT '0' COMMENT '是否需要提醒：0否、1是',
  PRIMARY KEY (`id`),
  CONSTRAINT `KEY_RED_PACK_HELP_DETAIL_RID` FOREIGN KEY (`rid`) REFERENCES {{%red_pack}} (`id`),
  CONSTRAINT `KEY_RED_PACK_HELP_DETAIL_JID` FOREIGN KEY (`jid`) REFERENCES {{%red_pack_join}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='红包裂变参与助力表';
SQL;
			$this->execute($sql);

			//红包裂变发送订单表
			$sql = <<<SQL
CREATE TABLE {{%red_pack_order}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned DEFAULT NULL COMMENT '账户ID',
  `corp_id` int(11) unsigned DEFAULT NULL COMMENT '企业ID',
  `rid` int(11) unsigned DEFAULT NULL COMMENT '裂变任务id',
  `jid` int(11) unsigned DEFAULT NULL COMMENT '参与表id',
  `hid` int(11) unsigned DEFAULT NULL COMMENT '好友助力表id',
  `external_id` int(11) DEFAULT NULL COMMENT '外部联系人id',
  `openid` varchar(64) DEFAULT '' COMMENT '外部联系人openid',
  `amount` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '红包金额',
  `order_id` varchar(50) NOT NULL DEFAULT '' COMMENT '支付订单号',
  `ispay` tinyint(1) DEFAULT '0' COMMENT '是否支付1是0否',
  `pay_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '支付时间',
  `transaction_id` varchar(50) DEFAULT '' COMMENT '第三方支付订单号',
  `remark` varchar(100) DEFAULT '' COMMENT '备注',
  `send_time` int(11) NOT NULL DEFAULT '0' COMMENT '发送时间',
  `send_type` tinyint(1) DEFAULT '0' COMMENT '发送类型：1、首拆，2、首拆剩余，3、好友拆，4、首拆+剩下',
  PRIMARY KEY (`id`),
  CONSTRAINT `KEY_RED_PACK_ORDER_UID` FOREIGN KEY (`uid`) REFERENCES {{%user}} (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='红包裂变发送订单表';
SQL;
			$this->execute($sql);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200528_065049_add_table_red_pack_join cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200528_065049_add_table_red_pack_join cannot be reverted.\n";

			return false;
		}
		*/
	}
