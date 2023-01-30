<?php

use yii\db\Migration;

/**
 * Class m210326_093208_change_shop_third_order_table
 */
class m210326_093208_change_shop_third_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%shop_third_order}}', 'order_share_id', "int(11) unsigned not NULL DEFAULT 0 COMMENT 'scrm商品或者页面分享id'  after `order_type` ");
        $this->createIndex('KEY_shop_third_order_order_share_id', '{{%shop_third_order}}', 'order_share_id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210326_084903_change_shop_third_order_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210326_093208_change_shop_third_order_table cannot be reverted.\n";

        return false;
    }
    */
}
