<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%shop_material_config}}`.
 */
class m210325_095212_create_shop_material_config_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%shop_material_config}}', [
            'id'           => $this->primaryKey(11)->unsigned()->notNull()->defaultExpression('AUTO_INCREMENT'),
            'corp_id'      => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('授权的企业ID'),
            'product'      => $this->tinyInteger(1)->unsigned()->notNUll()->defaultValue(0)->comment('商品标签，0关闭，1开启'),
            'page'         => $this->tinyInteger(1)->unsigned()->notNUll()->defaultValue(0)->comment('页面标签，0关闭，1开启'),
            'coupon'       => $this->tinyInteger(1)->unsigned()->notNUll()->defaultValue(0)->comment('优惠券标签，0关闭，1开启'),
            'weapp_name'   => $this->string(255)->notNUll()->defaultValue('')->comment('小程序名称字段'),
            'weapp_appid'  => $this->string(50)->notNUll()->defaultValue('')->comment('小程序APPID'),
            'page_image'   => $this->string(225)->notNUll()->defaultValue('')->comment('小程序分享页面默认图片'),
            'coupon_image' => $this->string(225)->notNUll()->defaultValue('')->comment('小程序分享优惠券默认图片'),
            'web_open'     => $this->tinyInteger(1)->unsigned()->notNUll()->defaultValue(0)->comment('H5商城是否开启0关闭，1开启'),
            'add_time'     => $this->timestamp()->notNUll()->defaultExpression('CURRENT_TIMESTAMP')->comment('添加时间'),
            'update_time'  => $this->timestamp()->notNUll()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')->comment('更新时间')
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='电商素材配置表'");
        $this->createIndex('KEY_SHOP_MATERIAL_CONFIG_CORP_ID', '{{%shop_material_config}}', 'corp_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%shop_material_config}}');
    }
}
