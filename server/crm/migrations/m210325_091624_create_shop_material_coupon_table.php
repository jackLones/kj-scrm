<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%shop_material_coupon}}`.
 */
class m210325_091624_create_shop_material_coupon_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%shop_material_coupon}}', [
            'id'             => $this->primaryKey(11)->unsigned()->notNull()->defaultExpression('AUTO_INCREMENT'),
            'corp_id'        => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('授权的企业ID'),
            'source'         => $this->smallInteger(5)->unsigned()->notNUll()->defaultValue(0)->comment('来源：1小猪电商 等'),
            'shop_api_key'   => $this->string(100)->notNUll()->defaultValue('')->comment('对接的key, 冗余'),
            'coupon_id'      => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('第三方券id'),
            'name'           => $this->string(255)->notNUll()->defaultValue('')->comment('券名称'),
            'type'           => $this->tinyInteger(1)->unsigned()->notNUll()->defaultValue(1)->comment('券类型 1：优惠券，2：赠送券 3 通用券 4 店铺券 5 兑换券 6代金券'),
            'face_money'     => $this->string(200)->defaultValue('')->notNull()->comment('券面额或者内容'),
            'limit_money'    => $this->decimal(10, 2)->unsigned()->defaultValue('0.00')->notNull()->comment('使用优惠券的订单金额下限 0：为不限定）'),
            'desc'           => $this->text()->comment('使用说明'),
            'is_all_product' => $this->tinyInteger(1)->unsigned()->notNUll()->defaultValue(1)->comment('使用范围 0：全店通用，1：指定商品使用'),
            'time_type'      => $this->tinyInteger(1)->unsigned()->notNUll()->defaultValue(0)->comment('时间类型 0：固定区间，1：固定时长'),
            'start_time'     => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('固定区间：开始时间'),
            'end_time'       => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('固定区间：过期时间'),
            'time_fixed'     => $this->string(225)->defaultValue('')->notNull()->comment('固定时长时文字描述'),
            'weapp_url'      => $this->string(225)->defaultValue('')->notNull()->comment('该优惠券小程序路径'),
            'web_url'        => $this->string(225)->defaultValue('')->notNull()->comment('该优惠券H5的路径'),
            'status'         => $this->tinyInteger(1)->unsigned()->notNUll()->defaultValue(1)->comment('是否显示,默认 1，0隐藏，1显示'),
            'add_time'       => $this->timestamp()->notNUll()->defaultExpression('CURRENT_TIMESTAMP')->comment('添加时间'),
            'update_time'    => $this->timestamp()->notNUll()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')->comment('更新时间')
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='电商素材优惠券'");
        $this->createIndex('KEY_SHOP_MATERIAL_COUPON_CORP_ID', '{{%shop_material_coupon}}', 'corp_id');
        $this->createIndex('KEY_SHOP_MATERIAL_COUPON_SOURCE', '{{%shop_material_coupon}}', 'source');
        $this->createIndex('KEY_SHOP_MATERIAL_COUPON_NAME', '{{%shop_material_coupon}}', 'name(10)');
        $this->createIndex('KEY_SHOP_MATERIAL_COUPON_STATUS', '{{%shop_material_coupon}}', 'status');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%shop_material_coupon}}');
    }
}
