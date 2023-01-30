<?php

use yii\db\Migration;

/**
 * Class m200514_050553_add_table_money_payconfig
 */
class m200514_050553_add_table_money_payconfig extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%money_payconfig}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '商户id',
  `corp_id` int(11) NOT NULL COMMENT '企业微信id',
  `appid` char(64) NOT NULL DEFAULT '' COMMENT 'appid(corpid)',
  `mchid` varchar(30) NOT NULL DEFAULT '' COMMENT '分配的商户号',
  `key` varchar(200) NOT NULL DEFAULT '' COMMENT '商户密钥',
  `apiclient_cert` varchar(255) NOT NULL DEFAULT '' COMMENT '证书apiclient_cert.pem文件路径',
  `apiclient_key` varchar(255) NOT NULL DEFAULT '' COMMENT '证书密钥apiclient_key.pem文件路径',
  `rootca` varchar(255) NOT NULL DEFAULT '' COMMENT 'CA证书文件路径',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态：1启用2未启用3删除',
  `add_time` int(11) DEFAULT '0' COMMENT '添加时间',
  `upt_time` int(11) DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `MONEY_PAYCONFIG_CORPID` (`corp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='支付配置';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200514_050553_add_table_money_payconfig cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200514_050553_add_table_money_payconfig cannot be reverted.\n";

        return false;
    }
    */
}
