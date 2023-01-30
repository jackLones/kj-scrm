<?php

	use yii\db\Migration;

	/**
	 * Class m200903_022514_add_table_public_sea_time_line
	 */
	class m200903_022514_add_table_public_sea_time_line extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			//非企微客户行为轨迹表
			$sql = <<<SQL
CREATE TABLE {{%public_sea_time_line}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL DEFAULT '0' COMMENT '商户id',
  `sea_id` int(11) unsigned DEFAULT NULL COMMENT '公海客户ID',
  `sub_id` int(10) NOT NULL DEFAULT '0' COMMENT '子账户ID',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '成员ID',
  `event` char(32) DEFAULT NULL COMMENT '行为，类别见model',
  `event_time` int(10) NOT NULL DEFAULT '0' COMMENT '行为时间',
  `event_id` int(11) unsigned DEFAULT '0' COMMENT '行为事件id',
  `related_id` int(10) DEFAULT '0' COMMENT '相关表id',
  `remark` varchar(500) DEFAULT '' COMMENT '行为相关备注',
  `is_sync` tinyint(1) unsigned DEFAULT 0 COMMENT '是否已同步：0否、1是',
  PRIMARY KEY (`id`),
  KEY `KEY_PUBLIC_SEA_TIME_LINE_UID` (`uid`),
  KEY `KEY_PUBLIC_SEA_TIME_LINE_SEAID` (`sea_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='非企微客户行为轨迹表';
SQL;
			$this->execute($sql);

			//非企微客户标签
			$sql = <<<SQL
CREATE TABLE {{%public_sea_tag}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `corp_id` int(11) unsigned DEFAULT NULL COMMENT '授权的企业ID',
  `follow_user_id` int(11) unsigned DEFAULT NULL COMMENT '外部联系人对应的ID',
  `tag_id` int(11) unsigned DEFAULT NULL COMMENT '授权的企业的标签ID',
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '0不显示1显示',
  `add_time` int(11) NOT NULL DEFAULT 0 COMMENT '添加时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '修改时间',
  `is_sync` tinyint(1) unsigned DEFAULT 0 COMMENT '是否已同步：0否、1是',
  PRIMARY KEY (`id`),
  KEY `KEY_PUBLIC_SEA_TAG_CORPID` (`corp_id`),
  KEY `KEY_PUBLIC_SEA_TAG_FOLLOW_USER_ID` (`follow_user_id`),
  CONSTRAINT `KEY_PUBLIC_SEA_TAG_CORPID` FOREIGN KEY (`corp_id`) REFERENCES {{%work_corp}} (`id`),
  CONSTRAINT `KEY_PUBLIC_SEA_TAG_FOLLOW_USER_ID` FOREIGN KEY (`follow_user_id`) REFERENCES {{%public_sea_contact_follow_user}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='非企微客户标签';
SQL;
			$this->execute($sql);

			$this->alterColumn('{{%custom_field_value}}', 'type', 'tinyint(1) DEFAULT 1 COMMENT \'跟进类型：1客户2粉丝3客户群4非企微客户\' ');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200903_022514_add_table_public_sea_time_line cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200903_022514_add_table_public_sea_time_line cannot be reverted.\n";

			return false;
		}
		*/
	}
