<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%shop_material_collect}}`.
 */
class m210325_024832_create_shop_material_collect_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%shop_material_collect}}', [
            'id'            => $this->primaryKey(11)->unsigned()->notNull()->defaultExpression('AUTO_INCREMENT'),
            'corp_id'       => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('授权的企业ID'),
            'user_id'       => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('收藏人id(导购id)'),
            'material_id'   => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('素材id'),
            'material_type' => $this->tinyInteger(1)->unsigned()->notNUll()->defaultValue(0)->comment('素材类型 1 product商品，2 page页面，3 coupon券'),
            'add_time'      => $this->timestamp()->notNUll()->defaultExpression('CURRENT_TIMESTAMP')->comment('收藏时间')
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='电商素材收藏表'");
        $this->createIndex('KEY_SHOP_MATERIAL_COLLECT_CORP_ID', '{{%shop_material_collect}}', 'corp_id');
        $this->createIndex('KEY_SHOP_MATERIAL_COLLECT_USER_ID', '{{%shop_material_collect}}', 'user_id');
        $this->createIndex('KEY_SHOP_MATERIAL_COLLECT_MATERIAL_ID', '{{%shop_material_collect}}', 'material_id');
        $this->createIndex('KEY_SHOP_MATERIAL_COLLECT_MATERIAL_TYPE', '{{%shop_material_collect}}', 'material_type');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%shop_material_collect}}');
    }
}
