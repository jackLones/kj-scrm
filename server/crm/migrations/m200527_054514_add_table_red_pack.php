<?php

	use yii\db\Migration;

	/**
	 * Class m200527_054514_add_table_red_pack
	 */
	class m200527_054514_add_table_red_pack extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			// 红包裂变表
			$sql = <<<SQL
CREATE TABLE {{%red_pack}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned DEFAULT NULL COMMENT '账户ID',
  `corp_id` int(11) unsigned DEFAULT NULL COMMENT '企业ID',
  `agent_id` int(11) unsigned DEFAULT NULL COMMENT '应用ID',
  `config_id` varchar(64) DEFAULT '' COMMENT '联系方式的配置id',
  `qr_code` varchar(255) DEFAULT '' COMMENT '联系二维码的URL',
  `state` varchar(64) DEFAULT '' COMMENT '企业自定义的state参数，用于区分不同的添加渠道，在调用“获取外部联系人详情”时会返回该参数值',
  `title` varchar(25) NOT NULL DEFAULT '' COMMENT '活动标题',
  `start_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '开始日期',
  `end_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '结束日期',
  `activity_rule` text COMMENT '活动规则',
  `contact_phone` varchar(25) NOT NULL DEFAULT '' COMMENT '联系电话',
  `redpack_price` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '裂变红包金额',
  `redpack_num` int(11) NOT NULL DEFAULT '0' COMMENT '裂变红包个数',
  `complete_num` int(11) unsigned DEFAULT '0' COMMENT '裂变完成数量',
  `first_detach_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '用户首次拆领类型：1、随机金额，2、固定金额，3、百分比金额',
  `min_random_amount` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '最小随机金额',
  `max_random_amount` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '最大随机金额',
  `fixed_amount` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '固定金额',
  `min_random_amount_per` int(11) NOT NULL DEFAULT '0' COMMENT '最小随机金额百分比',
  `max_random_amount_per` int(11) NOT NULL DEFAULT '0' COMMENT '最大随机金额百分比',
  `invite_amount` int(11) NOT NULL DEFAULT '0' COMMENT '裂变人数数量',
  `friend_detach_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '好友拆领类型：1、随机金额，2、固定金额',
  `min_friend_random_amount` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '最小随机金额',
  `max_friend_random_amount` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '最大随机金额',
  `fixed_friend_amount` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '固定金额',
  `total_amount` varchar(32) NOT NULL DEFAULT '' COMMENT '活动总金额',
  `give_out` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '已发放金额',
  `send_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '发放红包类型：1、手动发送，2、自动发放',
  `sex_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '性别类型：1、不限制，2、男性，3、女性，4、未知',
  `area_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '区域类型：1、不限制，2、部分地区',
  `area_data` text COMMENT '区域数据',
  `tag_ids` varchar(250) NOT NULL DEFAULT '' COMMENT '给客户打的标签',
  `pic_rule` text COMMENT '图片规则',
  `user_key` text COMMENT '引流成员',
  `user` text COMMENT '用户userID列表',
  `welcome` text COMMENT '欢迎语',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态0删除、1未发布、2已发布、3到期结束、4裂变红包个数已用完、5、手动提前结束',
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  PRIMARY KEY (`id`),
  CONSTRAINT `KEY_RED_PACK_UID` FOREIGN KEY (`uid`) REFERENCES {{%user}} (`uid`),
  CONSTRAINT `KEY_RED_PACK_CORPID` FOREIGN KEY (`corp_id`) REFERENCES {{%work_corp}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='红包裂变表';
SQL;
			$this->execute($sql);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200527_054514_add_table_red_pack cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200527_054514_add_table_red_pack cannot be reverted.\n";

			return false;
		}
		*/
	}
