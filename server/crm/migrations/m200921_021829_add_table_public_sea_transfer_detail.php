<?php

use yii\db\Migration;

/**
 * Class m200921_021829_add_table_public_sea_transfer_detail
 */
class m200921_021829_add_table_public_sea_transfer_detail extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
//成员客户认领分配表
	    $sql = <<<SQL
CREATE TABLE {{%public_sea_transfer_detail}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned DEFAULT NULL COMMENT '账户ID',
  `sea_id` int(11) unsigned DEFAULT NULL COMMENT '公海客户ID',
  `corp_id` int(11) unsigned DEFAULT NULL COMMENT '授权的企业ID',
  `external_userid` int(11) unsigned DEFAULT 0 COMMENT '企微外部联系人id',
  `handover_userid` int(11) unsigned DEFAULT 0 COMMENT '原归属成员id',
  `takeover_userid` int(11) unsigned DEFAULT 0 COMMENT '接替成员的id',
  `status` tinyint(1) unsigned DEFAULT 0 COMMENT '分配状态：0待分配1已分配2客户拒绝3接替成员客户达到上限4分配中5未知',
  `allocate_time` int(11) NOT NULL DEFAULT 0 COMMENT '分配时间',
  `add_time` int(11) NOT NULL DEFAULT 0 COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `KEY_PUBLIC_SEA_TRANSFER_DETAIL_CORPID` (`corp_id`),
  KEY `KEY_PUBLIC_SEA_TRANSFER_DETAIL_EXTERNAL_USERID` (`external_userid`),
  KEY `KEY_PUBLIC_SEA_TRANSFER_DETAIL_HANDOVER_USERID` (`handover_userid`),
  CONSTRAINT `KEY_PUBLIC_SEA_TRANSFER_DETAIL_CORPID` FOREIGN KEY (`corp_id`) REFERENCES {{%work_corp}} (`id`),
  CONSTRAINT `KEY_PUBLIC_SEA_TRANSFER_DETAIL_EXTERNAL_USERID` FOREIGN KEY (`external_userid`) REFERENCES {{%work_external_contact}} (`id`),
  CONSTRAINT `KEY_PUBLIC_SEA_TRANSFER_DETAIL_HANDOVER_USERID` FOREIGN KEY (`handover_userid`) REFERENCES {{%work_user}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='成员客户认领分配表';
SQL;
	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200921_021829_add_table_public_sea_transfer_detail cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200921_021829_add_table_public_sea_transfer_detail cannot be reverted.\n";

        return false;
    }
    */
}
