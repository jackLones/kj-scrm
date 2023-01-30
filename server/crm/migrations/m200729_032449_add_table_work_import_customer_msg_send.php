<?php

use yii\db\Migration;

/**
 * Class m200729_032449_add_table_work_import_customer_msg_send
 */
class m200729_032449_add_table_work_import_customer_msg_send extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%work_import_customer_msg_send}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `corp_id` int(11) unsigned NOT NULL COMMENT '企业微信id',
  `import_id` int(11) unsigned DEFAULT NULL COMMENT '导入表id',
  `user_id` int(11) unsigned DEFAULT NULL COMMENT '提醒成员ID',
  `add_num` int(11) NOT NULL DEFAULT '0' COMMENT '分配客户数',
  `status` tinyint(1) DEFAULT '0' COMMENT '发送状态 0未发送 1已发送 2发送失败',
  `time` int(11) NOT NULL DEFAULT '0' COMMENT '发送时间',
  `error_code` int(11) unsigned DEFAULT '0' COMMENT '错误码',
  `error_msg` varchar(255) DEFAULT '' COMMENT '错误信息',
  PRIMARY KEY (`id`),
  KEY `KEY_WORK_IMPORT_CUSTOMER_MSG_SEND_CORPID` (`corp_id`),
  KEY `KEY_WORK_IMPORT_CUSTOMER_MSG_SEND_USERID` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='导入客户提醒发送表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200729_032449_add_table_work_import_customer_msg_send cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200729_032449_add_table_work_import_customer_msg_send cannot be reverted.\n";

        return false;
    }
    */
}
