<?php

use yii\db\Migration;

/**
 * Class m200509_080516_add_table_money_set
 */
class m200509_080516_add_table_money_set extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%money_set}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL DEFAULT '0' COMMENT '商户id',
  `corp_id` int(10) NOT NULL DEFAULT '0' COMMENT '企业微信id',
  `sub_id` int(10) NOT NULL DEFAULT '0' COMMENT '子账户ID',
  `money` decimal(10,2) NOT NULL COMMENT '金额',
  `send_num` int(10) NOT NULL DEFAULT '0' COMMENT '发送次数',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1启用2禁用3删除',
  `time` int(10) NOT NULL DEFAULT '0' COMMENT '设置时间',
  PRIMARY KEY (`id`),
  KEY `KEY_MONEY_SET_UID` (`uid`),
  KEY `KEY_MONEY_SET_CORPID` (`corp_id`),
  KEY `KEY_MONEY_SET_STATUS` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='红包档段设置表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200509_080516_add_table_money_set cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200509_080516_add_table_money_set cannot be reverted.\n";

        return false;
    }
    */
}
