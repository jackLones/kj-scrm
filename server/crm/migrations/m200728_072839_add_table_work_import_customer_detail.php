<?php

use yii\db\Migration;

/**
 * Class m200728_072839_add_table_work_import_customer_detail
 */
class m200728_072839_add_table_work_import_customer_detail extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%work_import_customer_detail}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `corp_id` int(11) unsigned NOT NULL COMMENT '企业微信id',
  `import_id` int(11) unsigned DEFAULT NULL COMMENT '导入表id',
  `user_id` int(11) unsigned DEFAULT NULL COMMENT '成员ID',
  `phone` varchar(20) NOT NULL COMMENT '手机号',
  `nickname` varchar(255) NOT NULL DEFAULT '' COMMENT '微信昵称',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '姓名',
  `sex` tinyint(1) NOT NULL DEFAULT 0 COMMENT '性别 1男2女0未知',
  `area` varchar(255) NOT NULL DEFAULT '' COMMENT '区域',
  `des` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `is_add` tinyint(1) DEFAULT 0 COMMENT '是否添加1是0否',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间',
  `external_follow_id` int(11) DEFAULT 0 COMMENT '成员客户表ID',
  PRIMARY KEY (`id`),
  KEY `KEY_WORK_IMPORT_CUSTOMER_DETAIL_CORPID` (`corp_id`),
  KEY `KEY_WORK_IMPORT_CUSTOMER_DETAIL_IMPORTID` (`import_id`),
  KEY `KEY_WORK_IMPORT_CUSTOMER_DETAIL_UIDID` (`user_id`),
  KEY `KEY_WORK_IMPORT_CUSTOMER_DETAIL_PHONE` (`phone`),
  KEY `KEY_WORK_IMPORT_CUSTOMER_DETAIL_IS_ADD` (`is_add`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='导入客户详情表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200728_072839_add_table_work_import_customer_detail cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200728_072839_add_table_work_import_customer_detail cannot be reverted.\n";

        return false;
    }
    */
}
