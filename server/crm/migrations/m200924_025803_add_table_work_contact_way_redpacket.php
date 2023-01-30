<?php

use yii\db\Migration;

/**
 * Class m200924_025803_add_table_work_contact_way_redpacket
 */
class m200924_025803_add_table_work_contact_way_redpacket extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%work_contact_way_redpacket}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `corp_id` int(11) unsigned DEFAULT NULL COMMENT '授权的企业ID',
	`name` varchar(255) NOT NULL DEFAULT '' COMMENT '活动名称',
  `time_type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '时间设置1永久有效2时间区间',
  `start_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '开始日期',
  `end_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '结束日期',
  `rule_id` int(11) DEFAULT 0 COMMENT '红包规则id',
  `rule_text` text COMMENT '红包规则内容（非存储规则）',
  `redpacket_amount` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '活动投放金额',
  `send_amount` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '活动已发放领取金额',
  `redpacket_status` tinyint(1) DEFAULT 1 COMMENT '红包活动状态1未发布2已发布3已失效4已删除',
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `config_id` char(64) DEFAULT NULL COMMENT '联系方式的配置id',
  `title` varchar(200) DEFAULT NULL COMMENT '活码名称',
  `type` tinyint(1) unsigned DEFAULT NULL COMMENT '联系方式类型,1-单人, 2-多人',
  `scene` tinyint(1) unsigned DEFAULT NULL COMMENT '场景，1-在小程序中联系，2-通过二维码联系',
  `style` tinyint(2) unsigned DEFAULT NULL COMMENT '在小程序中联系时使用的控件样式，详见附表',
  `remark` char(64) DEFAULT NULL COMMENT '联系方式的备注信息，用于助记，不超过30个字符',
  `skip_verify` tinyint(1) DEFAULT NULL COMMENT '是否需要验证，1需要 0不需要',
  `verify_all_day` tinyint(1) DEFAULT '1' COMMENT '自动验证1全天开启2分时段',
  `state` char(64) DEFAULT NULL COMMENT '企业自定义的state参数，用于区分不同的添加渠道，在调用“获取外部联系人详情”时会返回该参数值',
  `spare_employee` text COMMENT '备用员工',
  `is_welcome_date` tinyint(1) DEFAULT '1' COMMENT '欢迎语时段日期 1关 2开',
  `is_welcome_week` tinyint(1) DEFAULT '1' COMMENT '欢迎语时段周 1关 2开',
  `is_limit` tinyint(1) DEFAULT '1' COMMENT '员工上限 1关 2开',
  `is_del` tinyint(1) DEFAULT '0' COMMENT '0：未删除；1：已删除',
  `qr_code` varchar(255) DEFAULT NULL COMMENT '联系二维码的URL',
  `open_date` tinyint(1) DEFAULT '0' COMMENT '0关闭1开启',
  `add_num` int(11) unsigned DEFAULT '0' COMMENT '添加人数',
  `tag_ids` text COMMENT '给客户打的标签',
  `user_key` varchar(255) DEFAULT NULL COMMENT '用户选择的key值',
  `content` text COMMENT '渠道活码的欢迎语内容',
  `status` tinyint(1) DEFAULT '0' COMMENT '渠道活码的欢迎语是否开启0关闭1开启',
  `sync_attachment_id` int(11) unsigned DEFAULT '0' COMMENT '同步后的素材id',
  `work_material_id` int(11) unsigned DEFAULT '0' COMMENT '企业微信素材id',
  `groupId` int(11) unsigned DEFAULT '0' COMMENT '分组id',
  `material_sync` tinyint(1) unsigned DEFAULT '0' COMMENT '不同步到内容库1同步',
  `attachment_id` int(11) unsigned DEFAULT '0' COMMENT '内容引擎id',
  `local_path` text COMMENT '二维码图片本地地址',
  PRIMARY KEY (`id`),
  KEY `KEY_WORK_CONTACT_WAY_REDPACKET_CONFIGID` (`config_id`),
  KEY `KEY_WORK_CONTACT_WAY_REDPACKET_CORPID` (`corp_id`),
  CONSTRAINT `KEY_WORK_CONTACT_WAY_REDPACKET_CORPID` FOREIGN KEY (`corp_id`) REFERENCES {{%work_corp}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='渠道活码红包活动表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200924_025803_add_table_work_contact_way_redpacket cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200924_025803_add_table_work_contact_way_redpacket cannot be reverted.\n";

        return false;
    }
    */
}
