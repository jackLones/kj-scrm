<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%shop_doudian_order}}`.
 */
class m210414_012502_create_shop_doudian_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%shop_doudian_order}}', [
            'id'                 => $this->primaryKey(11)->unsigned()->notNull()->defaultExpression('AUTO_INCREMENT'),
            'corp_id'            => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('授权的企业ID'),
            'shop_id'            => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('店铺id'),
            'order_id'           => $this->string(100)->notNUll()->defaultValue('')->comment('订单号'),
            'order_status'       => $this->tinyInteger(1)->unsigned()->notNUll()->defaultValue(0)->comment('订单状态'),
            'post_receiver'      => $this->string(100)->notNUll()->defaultValue('')->comment('收件人姓名'),
            'post_tel'           => $this->string(11)->notNUll()->defaultValue('')->comment('收件人电话'),
            'province'           => $this->string(50)->notNUll()->defaultValue('')->comment('省'),
            'city'               => $this->string(50)->notNUll()->defaultValue('')->comment('市'),
            'town'               => $this->string(50)->notNUll()->defaultValue('')->comment('区'),
            'detail'             => $this->string(100)->notNUll()->defaultValue('')->comment('详细地址'),
            'post_addr'          => $this->text()->comment('收件人地址'),
            'order_total_amount' => $this->decimal(10, 2)->unsigned()->notNUll()->defaultValue(0.00)->comment('订单实付金额（不包含运费）'),
            'refund_amount'      => $this->decimal(10, 2)->unsigned()->notNUll()->defaultValue(0.00)->comment('退款金额'),
            'b_type'             => $this->tinyInteger(1)->unsigned()->notNUll()->defaultValue(0)->comment('订单APP渠道，0:站外 1:火山 2:抖音 3:头条 4:西瓜 5:微信 6:闪购 7:头条lite版本 8:懂车帝 9:皮皮虾 11:抖音极速版 12:TikTok'),
            'c_biz'              => $this->tinyInteger(1)->unsigned()->notNUll()->defaultValue(0)->comment('订单业务类型，1:鲁班广告 2:联盟 4:商城 8:自主经营 10:线索通支付表单 12:抖音门店 14:抖+ 15:穿山甲'),
            'pay_type'           => $this->tinyInteger(1)->unsigned()->notNUll()->defaultValue(1)->comment('支付类型，0：货到付款，1：微信，2：支付宝,·4：银行卡,5：余额, 8：Dou分期, 9：新卡支付'),
            'sub_b_type'         => $this->tinyInteger(1)->unsigned()->notNUll()->defaultValue(1)->comment('订单来源类型 0:未知 1:app 2:小程序 3:h5'),
            'order_type'         => $this->tinyInteger(1)->unsigned()->notNUll()->defaultValue(1)->comment('订单类型，0实物，2普通虚拟，4poi核销，5三方核销，6服务市场'),
            'product_info'       => $this->text()->comment('订单商品详情'),
            'coupon_info'        => $this->text()->comment('优惠券详情'),
            'pay_time'           => $this->timestamp()->comment('支付时间 (pay_type为0货到付款时, 此字段为空)，例如"2018-06-01 12:00:00'),
            'add_time'           => $this->timestamp()->notNUll()->defaultExpression('CURRENT_TIMESTAMP')->comment('添加时间'),
            'update_time'        => $this->timestamp()->notNUll()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')->comment('更新时间')
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='抖店订单表'");
        $this->createIndex('KEY_SHOP_DOUDIAN_ORDER_ORDER_ID', '{{%shop_doudian_order}}', ['corp_id', 'order_id(10)']);
        $this->createIndex('KEY_SHOP_DOUDIAN_ORDER_POST_RECEIVER', '{{%shop_doudian_order}}', 'post_receiver(8)');
        $this->createIndex('KEY_SHOP_DOUDIAN_ORDER_POST_TEL', '{{%shop_doudian_order}}', 'post_tel');
        $this->createIndex('KEY_SHOP_DOUDIAN_ORDER_ORDER_STATUS', '{{%shop_doudian_order}}', 'order_status');
        $this->createIndex('KEY_SHOP_DOUDIAN_ORDER_PAY_TIME', '{{%shop_doudian_order}}', 'pay_time');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%shop_doudian_order}}');
    }
}
