<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%shop_third_order_product}}`.
 */
class m210202_005256_create_shop_third_order_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%shop_third_order_product}}', [
            'id'             => $this->primaryKey(),
            'third_order_id' => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('关联 shop_third_order 主键 '),
            'product_id'     => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment(' 商品ID'),
            'sku_id'         => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment(' 规格ID'),
            'name'           => $this->string(100)->notNUll()->defaultValue('')->comment(' 商品名称'),
            'product_number' => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment(' 商品数量'),
            'price'          => $this->decimal(10, 2)->defaultValue('0.00')->notNull()->comment('价格'),
            'return_status'  => $this->tinyInteger(1)->unsigned()->notNUll()->defaultValue(0)->comment('产品退款状态，0：未退款，1：部分退款，2：全部退完'),
            'add_time'       => $this->timestamp()->notNUll()->defaultExpression('CURRENT_TIMESTAMP')->comment('添加时间'),
            'update_time'    => $this->timestamp()->notNUll()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')->comment('更新时间'),
        ]);
        $this->createIndex('KEY_SHOP_PRODUCT_ORDER_ID', '{{%shop_third_order_product}}', 'third_order_id');
        $this->createIndex('KEY_SHOP_PRODUCT_STATUS',  '{{%shop_third_order_product}}', 'return_status');
        $this->addCommentOnTable('{{%shop_third_order_product}}', '第三方订单关联产品列表');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%shop_third_order_product}}');
    }
}
