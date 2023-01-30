<?php

use yii\db\Migration;

/**
 * Class m200513_121909_change_table_money_order
 */
class m200513_121909_change_table_money_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn("{{%money_order}}", "goods_id", "int(10) NOT NULL DEFAULT 0 COMMENT '订单关联' after `goods_type`");
	    $this->addColumn("{{%money_order}}", "ispay", "tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否支付1是0否' after `send_time`");
	    $this->addColumn("{{%money_order}}", "pay_time", "int(10) NOT NULL DEFAULT 0 COMMENT '支付时间' after `ispay`");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200513_121909_change_table_money_order cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200513_121909_change_table_money_order cannot be reverted.\n";

        return false;
    }
    */
}
