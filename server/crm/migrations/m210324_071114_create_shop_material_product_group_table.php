<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%shop_material_product_group}}`.
 */
class m210324_071114_create_shop_material_product_group_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%shop_material_product_group}}', [
            'id'           => $this->primaryKey(11)->unsigned()->notNull()->defaultExpression('AUTO_INCREMENT'),
            'corp_id'      => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('授权的企业ID'),
            'source'       => $this->smallInteger(5)->unsigned()->notNUll()->defaultValue(0)->comment('来源：1小猪电商 等'),
            'shop_api_key' => $this->string(100)->notNUll()->defaultValue('')->comment('对接的key, 冗余'),
            'group_id'     => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('分组id'),
            'name'         => $this->string(50)->notNUll()->defaultValue('')->comment('分组名称'),
            'sort'         => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('排序'),
            'status'       => $this->tinyInteger(1)->unsigned()->notNUll()->defaultValue(1)->comment('是否显示,默认 1，0隐藏，1显示'),
            'add_time'     => $this->timestamp()->notNUll()->defaultExpression('CURRENT_TIMESTAMP')->comment('添加时间'),
            'update_time'  => $this->timestamp()->notNUll()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')->comment('更新时间')
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='电商素材商品分组表'");
        $this->createIndex('KEY_SHOP_MATERIAL_PRODUCT_GROUP_CORP_ID', '{{%shop_material_product_group}}', 'corp_id');
        $this->createIndex('KEY_SHOP_MATERIAL_PRODUCT_GROUP_SOURCE', '{{%shop_material_product_group}}', 'source');
        $this->createIndex('KEY_SHOP_MATERIAL_PRODUCT_GROUP_STATUS', '{{%shop_material_product_group}}', 'status');
        $this->createIndex('KEY_SHOP_MATERIAL_PRODUCT_GROUP_SORT', '{{%shop_material_product_group}}', 'sort');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%shop_material_product_group}}');
    }
}
