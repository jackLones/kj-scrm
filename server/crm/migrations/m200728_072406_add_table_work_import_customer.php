<?php

use yii\db\Migration;

/**
 * Class m200728_072406_add_table_work_import_customer
 */
class m200728_072406_add_table_work_import_customer extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%work_import_customer}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `corp_id` int(11) unsigned NOT NULL COMMENT '企业微信id',
  `agentid` int(11) unsigned DEFAULT NULL COMMENT '应用id',
  `title` varchar(255) DEFAULT NULL COMMENT '导入表格名称',
  `user_ids` varchar(1000) DEFAULT NULL COMMENT '分配员工集合',
  `snum` int(11) NOT NULL DEFAULT 0 COMMENT '导入客户数',
  `is_del` tinyint(1) DEFAULT 0 COMMENT '是否删除1是0否',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `upt_time` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `KEY_WORK_IMPORT_CUSTOMER_CORPID` (`corp_id`),
  KEY `KEY_WORK_IMPORT_CUSTOMER_AGENTID` (`agentid`),
  KEY `KEY_WORK_IMPORT_CUSTOMER_IS_DEL` (`is_del`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='导入客户表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200728_072406_add_table_work_import_customer cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200728_072406_add_table_work_import_customer cannot be reverted.\n";

        return false;
    }
    */
}
