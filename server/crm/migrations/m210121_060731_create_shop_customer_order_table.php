<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%shop_customer_order}}`.
 */
class m210121_060731_create_shop_customer_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%shop_customer_order}}', [
            'id'                  => $this->primaryKey(11)->unsigned()->notNull()->defaultExpression('AUTO_INCREMENT'),
            'corp_id'             => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('授权的企业ID'),
            'cus_id'              => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('顾客ID'),
            'name'                => $this->string(100)->notNUll()->defaultValue('')->comment('顾客姓名'),
            'source'              => $this->tinyInteger(1)->unsigned()->notNUll()->defaultValue(0)->comment('订单来源:0 手工录入 1小猪电商 2淘宝 3有赞 '),
            'payment_method'      => $this->tinyInteger(1)->unsigned()->notNUll()->defaultValue(0)->comment('支付方式 0 未知 1支付宝 2微信 3系统余额 4银行卡 等'),
            'payment_method_name' => $this->string(100)->defaultValue('')->comment('支付方式名称:微信 建设银行 易宝等'),
            'order_no'            => $this->string(100)->notNUll()->defaultValue('')->comment('唯一订单号'),
            'payment_amount'      => $this->decimal(10, 2)->defaultValue('0.00')->notNull()->comment('订单实际⽀付⾦额'),
            'refund_amount'       => $this->decimal(10, 2)->defaultValue('0.00')->notNull()->comment('订单退款⾦额'),
            'other_store_id'      => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('第三方店铺id:小猪电商'),
            'other_store_name'    => $this->string(200)->notNUll()->defaultValue('')->comment('第三方店铺信息:小猪电商'),
            'guide_id'            => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('归属导购员ID'),
            'guide_name'          => $this->string(200)->notNUll()->defaultValue('')->comment('导购姓名'),
            'store_id'            => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('⻔店ID'),
            'store_name'          => $this->string(200)->notNUll()->defaultValue('')->comment('门店信息'),
            'pay_time'            => $this->timestamp()->notNUll()->defaultValue('0000-00-00 00:00:00')->comment('⽀付时间'),
            'buy_name'            => $this->string(150)->notNUll()->defaultValue('')->comment('购买⼈姓名'),
            'buy_phone'           => $this->string(11)->notNUll()->defaultValue('')->comment('购买⼈⼿机号'),
            'first_buy'           => $this->tinyInteger(1)->unsigned()->notNUll()->defaultValue(0)->comment('首次购买0否1是'),
            'order_type'          => $this->tinyInteger(1)->unsigned()->notNUll()->defaultValue(0)->comment('订单类型：0正常下单 1拼团 2砍价 等'),
            'status'              => $this->tinyInteger(1)->unsigned()->notNUll()->defaultValue(0)->comment('支付状态: 1正常 2退款'),
            'from_id'             => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('渠道id'),
            'update_time'         => $this->timestamp()->notNUll()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')->comment('更新时间'),
            'add_time'            => $this->timestamp()->notNUll()->defaultExpression('CURRENT_TIMESTAMP')->comment('入库时间')],
            'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
        $this->createIndex('KEY_SHOP_CUS_O_CORP_ID', '{{%shop_customer_order}}', 'corp_id');
        $this->createIndex('KEY_SHOP_CUS_O_CUS_ID', '{{%shop_customer_order}}', 'cus_id');
        $this->createIndex('KEY_SHOP_CUS_O_NAME', '{{%shop_customer_order}}', 'name');
        $this->createIndex('KEY_SHOP_CUS_O_SOURCE', '{{%shop_customer_order}}', 'source');
        $this->createIndex('KEY_SHOP_CUS_O_PAYMENT_METHOD', '{{%shop_customer_order}}', 'payment_method');
        $this->createIndex('KEY_SHOP_CUS_O_ORDER_NO', '{{%shop_customer_order}}', 'order_no');
        $this->createIndex('KEY_SHOP_CUS_O_GUIDE_ID', '{{%shop_customer_order}}', 'guide_id');
        $this->createIndex('KEY_SHOP_CUS_O_STORE_ID', '{{%shop_customer_order}}', 'store_id');
        $this->createIndex('KEY_SHOP_CUS_O_BUY_NAME', '{{%shop_customer_order}}', 'buy_name');
        $this->createIndex('KEY_SHOP_CUS_O_BUY_PHONE', '{{%shop_customer_order}}', 'buy_phone');
        $this->createIndex('KEY_SHOP_CUS_O_PAY_TIME', '{{%shop_customer_order}}', 'pay_time');
        $this->addCommentOnTable('{{%shop_customer_order}}', '顾客订单表');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%shop_customer_order}}');
        return false;
    }
}
