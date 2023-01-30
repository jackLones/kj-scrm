<?php

use yii\db\Migration;

/**
 * Class m200923_064328_add_table_red_pack_rule
 */
class m200923_064328_add_table_red_pack_rule extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%red_pack_rule}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned DEFAULT NULL COMMENT '账户ID',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '规则名称',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '单个红包金额类型：1、固定金额，2、随机金额',
  `fixed_amount` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '固定金额',
  `min_random_amount` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '最小随机金额',
  `max_random_amount` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '最大随机金额',
  `pic_url` varchar(500) NOT NULL DEFAULT '' COMMENT '红包封面路径',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '红包标题',
  `des` varchar(500) NOT NULL DEFAULT '' COMMENT '红包描述',
  `thanking` varchar(255) NOT NULL DEFAULT '' COMMENT '感谢语',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态：0删除、1正常',
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `KEY_RED_PACK_RULE_UID` (`uid`),
  CONSTRAINT `KEY_RED_PACK_RULE_UID` FOREIGN KEY (`uid`) REFERENCES {{%user}} (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='红包规则表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200923_064328_add_table_red_pack_rule cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200923_064328_add_table_red_pack_rule cannot be reverted.\n";

        return false;
    }
    */
}
