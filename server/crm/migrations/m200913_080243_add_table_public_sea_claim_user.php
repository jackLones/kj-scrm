<?php

use yii\db\Migration;

/**
 * Class m200913_080243_add_table_public_sea_claim_user
 */
class m200913_080243_add_table_public_sea_claim_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
//成员客户认领表
	    $sql = <<<SQL
CREATE TABLE {{%public_sea_claim_user}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned DEFAULT NULL COMMENT '账户ID',
  `sea_id` int(11) unsigned DEFAULT NULL COMMENT '公海客户ID',
  `corp_id` int(11) unsigned DEFAULT NULL COMMENT '授权的企业ID',
  `external_userid` int(11) unsigned DEFAULT 0 COMMENT '企微外部联系人id',
  `old_user_id` int(11) unsigned DEFAULT 0 COMMENT '原归属成员id',
  `old_follow_user_id` int(11) unsigned DEFAULT 0 COMMENT '原外部联系人添加信息表id',
  `new_user_id` int(11) unsigned DEFAULT 0 COMMENT '认领成员id',
  `new_follow_user_id` int(11) unsigned DEFAULT 0 COMMENT '认领成员外部联系人添加信息表id',
  `reclaim_rule` varchar(64) NOT NULL DEFAULT '' COMMENT '回收条件',
  `reclaim_time` int(11) NOT NULL DEFAULT 0 COMMENT '回收时间',
  `status` tinyint(1) unsigned DEFAULT 0 COMMENT '添加状态：0未添加、1已添加',
  `add_time` int(11) NOT NULL DEFAULT 0 COMMENT '添加时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `KEY_PUBLIC_SEA_CLAIM_USER_CORPID` (`corp_id`),
  CONSTRAINT `KEY_PUBLIC_SEA_CLAIM_USER_CORPID` FOREIGN KEY (`corp_id`) REFERENCES {{%work_corp}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='成员客户认领表';
SQL;
	    $this->execute($sql);

	    $this->addColumn('{{%public_sea_customer}}', 'reclaim_rule', 'varchar(64) NOT NULL DEFAULT \'\' COMMENT \'回收条件\' ');

	    $this->addColumn('{{%work_external_contact_follow_user}}', 'is_reclaim', 'tinyint(1) unsigned DEFAULT 0 COMMENT \'是否已回收：0未回收、1已回收\' ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200913_080243_add_table_public_sea_claim_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200913_080243_add_table_public_sea_claim_user cannot be reverted.\n";

        return false;
    }
    */
}
