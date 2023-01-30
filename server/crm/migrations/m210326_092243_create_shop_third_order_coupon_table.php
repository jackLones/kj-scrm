<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%shop_third_order_coupon}}`.
 */
class m210326_092243_create_shop_third_order_coupon_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%shop_third_order_coupon}}', [
            'id'              => $this->primaryKey(11)->unsigned()->notNull()->defaultExpression('AUTO_INCREMENT'),
            'corp_id'         => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('授权的企业ID'),
            'third_order_id'  => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('第三方订单id'),
            'coupon_id'       => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('第三方券id'),
            'coupon_name'     => $this->string(100)->notNUll()->defaultValue('')->comment('券名称'),
            'coupon_desc'     => $this->string(225)->notNUll()->defaultValue('')->comment(' 券的使用描述（例如抵扣13元）'),
            'coupon_share_id' => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('电商优惠券分享记录ID'),
            'add_time'        => $this->timestamp()->notNUll()->defaultExpression('CURRENT_TIMESTAMP')->comment('添加时间')
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='第三方订单关联优惠券表'");
        $this->createIndex('KEY_SHOP_THIRD_ORDER_COUPON_CORP_ID', '{{%shop_third_order_coupon}}', 'corp_id');
        $this->createIndex('KEY_SHOP_THIRD_ORDER_COUPON_THIRD_ORDER_ID', '{{%shop_third_order_coupon}}', 'third_order_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%shop_third_order_coupon}}');
    }
}
