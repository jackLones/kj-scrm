<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%shop_doudian_category}}`.
 */
class m210413_065200_create_shop_doudian_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%shop_doudian_category}}', [
            'id'          => $this->primaryKey(11)->unsigned()->notNull()->defaultExpression('AUTO_INCREMENT'),
            'corp_id'     => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('授权的企业ID'),
            'shop_id'     => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('店铺id'),
            'cid'         => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('类目id'),
            'name'        => $this->string(225)->notNUll()->defaultValue('')->comment('类目名称'),
            'level'       => $this->tinyInteger(1)->unsigned()->notNUll()->defaultValue(0)->comment('类目级别：1，2，3级类目'),
            'parent_id'   => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('父类目id'),
            'is_leaf'     => $this->tinyInteger(1)->unsigned()->notNUll()->defaultValue(0)->comment('是否是叶子节点 0 否 1是 '),
            'enable'      => $this->tinyInteger(1)->unsigned()->notNUll()->defaultValue(1)->comment('是否有效 0 否 1是 '),
            'add_time'    => $this->timestamp()->notNUll()->defaultExpression('CURRENT_TIMESTAMP')->comment('添加时间'),
            'update_time' => $this->timestamp()->notNUll()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')->comment('更新时间')
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商家发布商品的类目'");
        $this->createIndex('KEY_SHOP_DOUDIAN_CATEGORY_CORP_ENABLE_SHOP_ID', '{{%shop_doudian_category}}', ['corp_id', 'shop_id', 'cid']);
        $this->createIndex('KEY_SHOP_DOUDIAN_CATEGORY_ENABLE', '{{%shop_doudian_category}}', 'enable');
        $this->createIndex('KEY_SHOP_DOUDIAN_CATEGORY_LEVEL', '{{%shop_doudian_category}}', 'level');
        $this->createIndex('KEY_SHOP_DOUDIAN_CATEGORY_PARENT_ID', '{{%shop_doudian_category}}', 'parent_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%shop_doudian_category}}');
    }
}
