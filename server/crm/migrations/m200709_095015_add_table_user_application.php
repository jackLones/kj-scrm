<?php

use yii\db\Migration;

/**
 * Class m200709_095015_add_table_user_application
 */
class m200709_095015_add_table_user_application extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%user_application}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户id',
  `merchant` varchar(255) DEFAULT NULL COMMENT '商户名称',
  `license` varchar(30) DEFAULT NULL COMMENT '营业执照号',
  `license_cp` varchar(255) DEFAULT NULL COMMENT '营业执照照片',
  `organization_cp` varchar(255) DEFAULT NULL COMMENT '组织机构代码证件照',
  `possessor_type` varchar(30) DEFAULT NULL COMMENT '证件持有人类型',
  `possessor` varchar(30) DEFAULT NULL COMMENT '证件持有人姓名',
  `id_number` varchar(30) DEFAULT NULL COMMENT '证件号码',
  `id_cp_a` varchar(255) DEFAULT NULL COMMENT '证件照正面',
  `id_cp_b` varchar(255) DEFAULT NULL COMMENT '证件照反面',
  `id_cp_c` varchar(255) DEFAULT NULL COMMENT '手持身份证照片',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '客户资料状态：1未审核，2审核通过，3审核失败',
  `addtime` int(11) DEFAULT NULL COMMENT '提交时间',
  `pass_time` int(11) DEFAULT NULL COMMENT '审核通过时间',
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `KEY_USER_APPLICATION_UID` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户资料信息表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200709_095015_add_table_user_application cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200709_095015_add_table_user_application cannot be reverted.\n";

        return false;
    }
    */
}
