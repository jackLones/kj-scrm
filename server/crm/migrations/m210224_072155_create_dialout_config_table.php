<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%dialout_config}}`.
 */
class m210224_072155_create_dialout_config_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = <<<SQL
CREATE TABLE {{%dialout_config}} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `corp_id` int(11) NOT NULL,
  `exten_money` decimal(12,2) DEFAULT NULL COMMENT '坐席价格（含月租） 元/月/个',
  `phone_money` decimal(12,2) DEFAULT NULL COMMENT '花费价格  元/分钟',
  `balance` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '账户余额',
  `remark` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='坐席配置表';
SQL;

        $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%dialout_config}}');
    }
}
