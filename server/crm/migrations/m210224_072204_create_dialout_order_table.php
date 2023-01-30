<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%dialout_order}}`.
 */
class m210224_072204_create_dialout_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = <<<SQL
CREATE TABLE {{%dialout_order}} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `corp_id` int(11) NOT NULL,
  `exten` int(11) COMMENT '坐席号',
  `type` tinyint(2) NOT NULL COMMENT '1:花费充值；2：话费消耗；3：坐席充值；4：开通坐席；5：续费坐席',
  `money` decimal(12,2) NOT NULL COMMENT '出账/进账 金额',
  `status` tinyint(1) NOT NULL COMMENT '1:已到账；2：未到账',
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='坐席消费表';
SQL;

        $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%dialout_order}}');
    }
}
