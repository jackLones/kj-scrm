<?php

use yii\db\Migration;

/**
 * Class m191204_033632_add_table_message_pack
 */
class m191204_033632_add_table_message_pack extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%message_pack}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `num` int(11) unsigned  NOT NULL COMMENT '短信包条数',
  `price` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '当前售价',
  `status` tinyint(1) DEFAULT '1' COMMENT '是否启用，1：启用、0：不启用',
  `update_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `create_time` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='短信包表';
SQL;
	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191204_033632_add_table_message_pack cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191204_033632_add_table_message_pack cannot be reverted.\n";

        return false;
    }
    */
}
