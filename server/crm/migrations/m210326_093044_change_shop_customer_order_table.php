<?php

use yii\db\Migration;

/**
 * Class m210326_093044_change_shop_customer_order_table
 */
class m210326_093044_change_shop_customer_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%shop_customer_order}}', 'from_id');
        $this->addColumn('{{%shop_customer_order}}', 'order_share_id', "int(11) unsigned not NULL DEFAULT 0 COMMENT 'scrm商品或者页面分享id' after `status`");
        $this->createIndex('KEY_SHOP_CUS_O_ORDER_SHARE_ID', '{{%shop_customer_order}}', 'order_share_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210326_084832_change_shop_customer_order_table cannot be reverted.\n";

        return false;
    }


    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210326_093044_change_shop_customer_order_table cannot be reverted.\n";

        return false;
    }
    */
}
