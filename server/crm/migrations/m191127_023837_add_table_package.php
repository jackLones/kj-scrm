<?php

use yii\db\Migration;

/**
 * Class m191127_023837_add_table_package
 */
class m191127_023837_add_table_package extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%package}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '套餐名称',
  `old_price` float(11,2) DEFAULT NULL COMMENT '原价',
  `price` float(11,2) DEFAULT NULL COMMENT '现价',
  `is_trial` tinyint(1) DEFAULT '0' COMMENT '是否是试用',
  `status` tinyint(1) DEFAULT '0' COMMENT '状态：1、开启，2、禁用，3、删除',
  `update_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `create_time` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='套餐表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191127_023837_add_table_package cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191127_023837_add_table_package cannot be reverted.\n";

        return false;
    }
    */
}
