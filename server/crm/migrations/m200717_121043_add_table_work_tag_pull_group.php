<?php

use yii\db\Migration;

/**
 * Class m200717_121043_add_table_work_tag_pull_group
 */
class m200717_121043_add_table_work_tag_pull_group extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%work_tag_pull_group}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `corp_id` int(11) unsigned DEFAULT NULL COMMENT '授权的企业ID',
  `title` char(64) DEFAULT NULL COMMENT '标题',
  `send_type` tinyint(1) unsigned DEFAULT NULL COMMENT '1、全部客户 2、按条件筛选客户',
  `user_key` text COMMENT '选择的成员或客户标志',
  `content` text COMMENT '入群引导语',
  `is_filter` tinyint(1) DEFAULT '0' COMMENT '是否过滤0不过滤1过滤',
  `status` tinyint(1) DEFAULT '0' COMMENT '发送状态 0未发送 1已发送 2发送失败 3发送中',
  `sender` text COMMENT '成员确认信息',
  `others` text COMMENT '客户其他筛选字段值',
  `fail_list` text COMMENT '失败的人员',
  `success_list` text COMMENT '成功人员',
  `error_code` int(11) unsigned DEFAULT NULL COMMENT '错误码',
  `error_msg` char(255) DEFAULT NULL COMMENT '错误信息',
  `queue_id` int(11) DEFAULT NULL COMMENT '队列id',
  `real_num` int(11) unsigned DEFAULT '0' COMMENT '实际发送人数',
  `will_num` int(11) unsigned DEFAULT '0' COMMENT '预计发送人数',
  `is_del` tinyint(1) DEFAULT '0' COMMENT '删除状态 0 未删除 1 已删除',
  `create_time` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_WORK_TAG_PULL_GROUP_CORP_ID` (`corp_id`),
  CONSTRAINT `KEY_WORK_TAG_PULL_GROUP_CORP_ID` FOREIGN KEY (`corp_id`) REFERENCES {{%work_corp}} (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COMMENT='标签拉群表';
SQL;

	    $this->execute($sql);

	    $sql = <<<SQL
CREATE TABLE {{%work_tag_group_statistic}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pull_id` int(11) unsigned DEFAULT NULL COMMENT '自动拉群表的ID',
  `external_id` int(11) unsigned DEFAULT NULL COMMENT '外部联系人ID',
  `chat_id` int(11) unsigned DEFAULT NULL COMMENT '群列表ID',
  `status` tinyint(1) DEFAULT '0' COMMENT '入群状态0未入群1已入群',
  `send` tinyint(1) DEFAULT '0' COMMENT '送达状态0未收到邀请1已收到邀请2客户已不是好友3客户接收已达上限',
  `create_time` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_WORK_TAG_GROUP_STATISTIC_PULL_ID` (`pull_id`),
  KEY `KEY_WORK_TAG_GROUP_STATISTIC_EXTERNAL_ID` (`external_id`),
  KEY `KEY_WORK_TAG_GROUP_STATISTIC_STATUS` (`status`),
  KEY `KEY_WORK_TAG_GROUP_STATISTIC_CHAT_ID` (`chat_id`),
  KEY `KEY_WORK_TAG_GROUP_STATISTIC_SEND` (`send`),
  CONSTRAINT `KEY_WORK_TAG_GROUP_STATISTIC_EXTERNAL_ID` FOREIGN KEY (`external_id`) REFERENCES {{%work_external_contact}} (`id`),
  CONSTRAINT `KEY_WORK_TAG_GROUP_STATISTIC_PULL_ID` FOREIGN KEY (`pull_id`) REFERENCES {{%work_tag_pull_group}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='标签拉群客户明细表';
SQL;
	    $this->execute($sql);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200717_121043_add_table_work_tag_pull_group cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200717_121043_add_table_work_tag_pull_group cannot be reverted.\n";

        return false;
    }
    */
}
