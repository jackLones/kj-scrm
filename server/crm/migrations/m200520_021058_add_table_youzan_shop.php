<?php

use yii\db\Migration;

/**
 * Class m200520_021058_add_table_youzan_shop
 */
class m200520_021058_add_table_youzan_shop extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%youzan_shop}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned DEFAULT NULL COMMENT 'uid',
  `client_id` varchar(255) NOT NULL COMMENT '有赞云开发者的应用ID',
  `client_secret` varchar(255) NOT NULL COMMENT '有赞云开发者的应用secret',
  `kdt_id` int(11) NOT NULL DEFAULT 0 COMMENT '授权店铺id',
  `access_token` varchar(255) NOT NULL DEFAULT '' COMMENT '调用API的access_token,有效7天',
  `expires` int(11) NOT NULL DEFAULT 0 COMMENT 'access_token过期时间',
  `type` tinyint(2) NOT NULL DEFAULT 0 COMMENT '店铺类型：0、2、9微商城 7零售 6美业',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '店铺名称',
  `logo` varchar(255) NOT NULL DEFAULT '' COMMENT '店铺logo',
  `intro` varchar(1000) NOT NULL DEFAULT '' COMMENT '店铺简介',
  `add_time` int(11) NOT NULL DEFAULT 0 COMMENT '添加时间',
  `upt_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `KEY_YOUZAN_SHOP_UID` (`uid`),
  KEY `KEY_YOUZAN_SHOP_KDTID` (`kdt_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='有赞店铺信息表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200520_021058_add_table_youzan_shop cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200520_021058_add_table_youzan_shop cannot be reverted.\n";

        return false;
    }
    */
}
