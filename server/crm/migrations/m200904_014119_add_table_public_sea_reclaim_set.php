<?php

	use yii\db\Migration;

	/**
	 * Class m200904_014119_add_table_public_sea_reclaim_set
	 */
	class m200904_014119_add_table_public_sea_reclaim_set extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			//客户回收设置表
			$sql = <<<SQL
CREATE TABLE {{%public_sea_reclaim_set}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned DEFAULT NULL COMMENT '账户ID',
  `corp_id` int(11) unsigned DEFAULT NULL COMMENT '授权的企业ID',
  `agent_id` int(11) unsigned DEFAULT NULL COMMENT '应用ID',
  `valid_type` tinyint(1) unsigned DEFAULT 1 COMMENT '沟通状态：1通用、2仅企业成员适用',
  `user_key` text COMMENT '生效成员',
  `user` text COMMENT '用户userID列表',
  `party` text COMMENT '生效部门',
  `reclaim_rule` text COMMENT '回收规则',
  `private_num` int(11) unsigned DEFAULT 0 COMMENT '私有池数量',
  `is_delay` tinyint(1) unsigned DEFAULT 0 COMMENT '是否延期：0否、1是',
  `delay_day` int(11) unsigned DEFAULT 0 COMMENT '延期天数',
  `reclaim_day` int(11) unsigned DEFAULT 0 COMMENT '可捡回天数',
  `status` tinyint(1) unsigned DEFAULT 1 COMMENT '规则状态：0删除、1可用',
  `add_time` int(11) NOT NULL DEFAULT 0 COMMENT '添加时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `KEY_PUBLIC_SEA_RECLAIM_SET_CORPID` (`corp_id`),
  CONSTRAINT `KEY_PUBLIC_SEA_RECLAIM_SET_CORPID` FOREIGN KEY (`corp_id`) REFERENCES {{%work_corp}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='客户回收设置表';
SQL;
			$this->execute($sql);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200904_014119_add_table_public_sea_reclaim_set cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200904_014119_add_table_public_sea_reclaim_set cannot be reverted.\n";

			return false;
		}
		*/
	}
