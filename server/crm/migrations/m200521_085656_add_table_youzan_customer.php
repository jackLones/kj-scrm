<?php

use yii\db\Migration;

/**
 * Class m200521_085656_add_table_youzan_customer
 */
class m200521_085656_add_table_youzan_customer extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%youzan_customer}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL DEFAULT '0' COMMENT '商户id',
  `kdt_id` int(11) NOT NULL DEFAULT '0' COMMENT '有赞店铺id',
  `yz_uid` int(11) NOT NULL DEFAULT '0' COMMENT '有赞用户ID',
  `fans_id` int(11) NOT NULL DEFAULT '0' COMMENT '粉丝id',
  `gender` tinyint(1) NOT NULL DEFAULT '0' COMMENT '性别，0:其他 1:男 2:女',
  `is_member` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否是会员1是0否',
  `trade_count` int(10) NOT NULL DEFAULT '0' COMMENT '购次',
  `show_name` varchar(100) NOT NULL DEFAULT '' COMMENT '展示姓名(取值顺序为手机-姓名-昵称)',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '客户姓名',
  `points` int(11) NOT NULL DEFAULT '0' COMMENT '用户积分',
  `mobile` varchar(50) NOT NULL DEFAULT '' COMMENT '手机号',
  `created_at` int(11) NOT NULL DEFAULT '0' COMMENT '成为客户的时间',
  `member_created_at` int(11) NOT NULL DEFAULT '0' COMMENT '成为会员的时间',
  `time` int(11) NOT NULL DEFAULT '0' COMMENT '客户同步时间',
  PRIMARY KEY (`id`),
  KEY `KEY_YOUZAN_CUSTOMER_UID` (`uid`),
  KEY `KEY_YOUZAN_CUSTOMER_KDTID` (`kdt_id`),
  KEY `KEY_YOUZAN_CUSTOMER_YZUID` (`yz_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='有赞客户表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200521_085656_add_table_youzan_customer cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200521_085656_add_table_youzan_customer cannot be reverted.\n";

        return false;
    }
    */
}
