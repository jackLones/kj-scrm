<?php

use yii\db\Migration;

/**
 * Class m201007_065601_add_table_public_sea_protect
 */
class m201007_065601_add_table_public_sea_protect extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		//成员客户保护表
	    $sql = <<<SQL
CREATE TABLE {{%public_sea_protect}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned DEFAULT NULL COMMENT '账户ID',
  `sub_id` int(11) unsigned DEFAULT 0 COMMENT '子账户ID',
  `corp_id` int(11) unsigned DEFAULT NULL COMMENT '授权的企业ID',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '操作的成员ID',
  `is_master` tinyint(1) unsigned DEFAULT 0 COMMENT '状态：0、主账户添加，1、子账户添加 ',
  `type` tinyint(1) unsigned DEFAULT 0 COMMENT '客户类型：0非企微客户1企微客户',
  `external_id` int(11) NOT NULL COMMENT '外部联系人ID',
  `follow_user_id` int(11) NOT NULL DEFAULT '0' COMMENT '外部联系人添加信息表id',
  PRIMARY KEY (`id`),
  KEY `KEY_PUBLIC_SEA_PROTECT_UID` (`uid`),
  CONSTRAINT `KEY_PUBLIC_SEA_PROTECT_UID` FOREIGN KEY (`uid`) REFERENCES `pig_user` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='成员客户保护表';
SQL;
	    $this->execute($sql);

	    //公海池客户标签表
	    $sql = <<<SQL
CREATE TABLE {{%public_sea_private_tag}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned DEFAULT NULL COMMENT '账户ID',
  `sub_id` int(11) unsigned DEFAULT 0 COMMENT '子账户ID',
  `corp_id` int(11) unsigned DEFAULT NULL COMMENT '授权的企业ID',
  `sea_id` int(11) unsigned DEFAULT NULL COMMENT '公海池客户ID',
  `tag_id` int(11) unsigned DEFAULT NULL COMMENT '授权的企业的标签ID',
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '0不显示1显示',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `KEY_PUBLIC_SEA_PRIVATE_TAG_UID` (`uid`),
  CONSTRAINT `KEY_PUBLIC_SEA_PRIVATE_TAG_UID` FOREIGN KEY (`uid`) REFERENCES `pig_user` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='公海池客户标签表';
SQL;
	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201007_065601_add_table_public_sea_protect cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201007_065601_add_table_public_sea_protect cannot be reverted.\n";

        return false;
    }
    */
}
