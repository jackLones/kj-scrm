<?php

use yii\db\Migration;

/**
 * Class m200718_100644_add_table_admin_user_employee
 */
class m200718_100644_add_table_admin_user_employee extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%admin_user_employee}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '帐号id',
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT '父级id',
  `role_id` int(11) NOT NULL DEFAULT '0' COMMENT '角色id',
  `account` varchar(100) NOT NULL DEFAULT '' COMMENT '帐号',
  `pwd` varchar(50) NOT NULL COMMENT '加密后的密码',
  `salt` varchar(10) NOT NULL COMMENT '加密校验码',
  `phone` varchar(15) NOT NULL DEFAULT '' COMMENT '电话',
  `name` varchar(32) NOT NULL COMMENT '姓名',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT ' 0不可用 1可用',
  `add_time` int(10) DEFAULT NULL COMMENT '添加日期',
  `upt_time` int(10) DEFAULT NULL COMMENT '添加日期',
  `city_all` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否全国',
  PRIMARY KEY (`id`),
  KEY `KEY_ADMIN_USER_EMPLOYEE_UID` (`uid`),
  KEY `KEY_ADMIN_USER_EMPLOYEE_ROLE_ID` (`role_id`),
  KEY `KEY_ADMIN_USER_EMPLOYEE_ACCOUNT` (`account`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='总后台员工表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200718_100644_add_table_admin_user_employee cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200718_100644_add_table_admin_user_employee cannot be reverted.\n";

        return false;
    }
    */
}
