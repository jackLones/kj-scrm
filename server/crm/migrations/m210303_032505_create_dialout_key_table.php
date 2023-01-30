<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%dialout_key}}`.
 */
class m210303_032505_create_dialout_key_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = <<<SQL
CREATE TABLE {{%dialout_key}} (
`id` int(11) NOT NULL AUTO_INCREMENT,
  `api_type` varchar(255) NOT NULL,
  `api_key` varchar(255) NOT NULL COMMENT '平台分给的key',
  `remark` varchar(1024) DEFAULT NULL COMMENT '描述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='平台分配的key表';
SQL;

        $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%dialout_key}}');
    }
}
