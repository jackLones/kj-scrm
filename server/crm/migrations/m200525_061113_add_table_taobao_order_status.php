<?php

use yii\db\Migration;

/**
 * Class m200525_061113_add_table_taobao_order_status
 */
class m200525_061113_add_table_taobao_order_status extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%taobao_order_status}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL DEFAULT '' COMMENT '状态名称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='淘宝订单状态表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200525_061113_add_table_taobao_order_status cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200525_061113_add_table_taobao_order_status cannot be reverted.\n";

        return false;
    }
    */
}
