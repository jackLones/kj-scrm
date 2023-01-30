<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%shop_material_product}}`.
 */
class m210324_071758_create_shop_material_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%shop_material_product}}', [
            'id'                 => $this->primaryKey(11)->unsigned()->notNull()->defaultExpression('AUTO_INCREMENT'),
            'corp_id'            => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('授权的企业ID'),
            'product_id'         => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('商品第三方'),
            'shop_api_key'       => $this->string(100)->notNUll()->defaultValue('')->comment('对接的key, 冗余'),
            'source'             => $this->smallInteger(5)->unsigned()->notNUll()->defaultValue(0)->comment('来源：1小猪电商 等'),
            'group_id'           => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('分组id'),
            'cate_id'            => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('分类id'),
            'name'               => $this->string(200)->notNUll()->defaultValue('')->comment('商品名称'),
            'code'               => $this->string(100)->notNUll()->defaultValue('')->comment('商品编码'),
            'type'               => $this->tinyInteger(1)->unsigned()->notNUll()->defaultValue(0)->comment('商品类型：0普通，1拼团，2积分，3秒杀，4砍价，5限时活动'),
            'stock'              => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('库存'),
            'sales'              => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('销量'),
            'original_price'     => $this->decimal(10, 2)->unsigned()->defaultValue('0.00')->notNull()->comment(' 原价'),
            'original_end_price' => $this->decimal(10, 2)->unsigned()->defaultValue('0.00')->notNull()->comment('默认0，原价为区间时此为区间最大值'),
            'price'              => $this->decimal(10, 2)->unsigned()->defaultValue('0.00')->notNull()->comment('售价'),
            'end_price'          => $this->decimal(10, 2)->unsigned()->defaultValue('0.00')->notNull()->comment('默认0，售价为区间时此为区间最大值'),
            'image'              => $this->string(225)->defaultValue('')->notNull()->comment('商品主图地址'),
            'weapp_url'          => $this->string(225)->defaultValue('')->notNull()->comment('该商品小程序路径'),
            'web_url'            => $this->string(225)->defaultValue('')->notNull()->comment('该商品 H5 的路径'),
            'recommend_remark'   => $this->text()->comment('商品推荐语'),
            'status'             => $this->tinyInteger(1)->unsigned()->notNUll()->defaultValue(1)->comment('是否显示,默认 1，0隐藏，1显示'),
            'add_time'           => $this->timestamp()->notNUll()->defaultExpression('CURRENT_TIMESTAMP')->comment('添加时间'),
            'update_time'        => $this->timestamp()->notNUll()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')->comment('更新时间')
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='电商素材商品表'");
        $this->createIndex('KEY_SHOP_MATERIAL_PRODUCT_INDEX', '{{%shop_material_product}}', ['corp_id','source','product_id']);
        $this->createIndex('KEY_SHOP_MATERIAL_PRODUCT_GROUP_ID', '{{%shop_material_product}}', 'group_id');
        $this->createIndex('KEY_SHOP_MATERIAL_PRODUCT_CATE_ID', '{{%shop_material_product}}', 'cate_id');
        $this->createIndex('KEY_SHOP_MATERIAL_PRODUCT_NAME', '{{%shop_material_product}}', 'name(10)');
        $this->createIndex('KEY_SHOP_MATERIAL_PRODUCT_CODE', '{{%shop_material_product}}', 'code');
        $this->createIndex('KEY_SHOP_MATERIAL_PRODUCT_PRICE', '{{%shop_material_product}}', 'price');
        $this->createIndex('KEY_SHOP_MATERIAL_PRODUCT_STATUS', '{{%shop_material_product}}', 'status');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%shop_material_product}}');
    }
}
