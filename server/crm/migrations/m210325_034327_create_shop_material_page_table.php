<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%shop_material_page}}`.
 */
class m210325_034327_create_shop_material_page_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%shop_material_page}}', [
            'id'           => $this->primaryKey(11)->unsigned()->notNull()->defaultExpression('AUTO_INCREMENT'),
            'corp_id'      => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('授权的企业ID'),
            'source'       => $this->smallInteger(5)->unsigned()->notNUll()->defaultValue(0)->comment('来源：1小猪电商 等'),
            'shop_api_key' => $this->string(100)->notNUll()->defaultValue('')->comment('对接的key, 冗余'),
            'page_id'      => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('第三方页面id'),
            'title'        => $this->string(200)->notNUll()->defaultValue('')->comment('页面标题'),
            'image'        => $this->string(100)->notNUll()->defaultValue('')->comment('页面封面'),
            'desc'         => $this->text()->comment('页面描述'),
            'weapp_url'    => $this->string(225)->defaultValue('')->notNull()->comment('该页面小程序路径'),
            'web_url'      => $this->string(225)->defaultValue('')->notNull()->comment('该页面H5的路径'),
            'status'       => $this->tinyInteger(1)->unsigned()->notNUll()->defaultValue(1)->comment('是否显示,默认 1，0隐藏，1显示'),
            'add_time'     => $this->timestamp()->notNUll()->defaultExpression('CURRENT_TIMESTAMP')->comment('添加时间'),
            'update_time'  => $this->timestamp()->notNUll()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')->comment('更新时间')
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='电商素材页面表'");
        $this->createIndex('KEY_SHOP_MATERIAL_PAGE_CORP_ID', '{{%shop_material_page}}', 'corp_id');
        $this->createIndex('KEY_SHOP_MATERIAL_PAGE_SOURCE', '{{%shop_material_page}}', 'source');
        $this->createIndex('KEY_SHOP_MATERIAL_PAGE_TITLE', '{{%shop_material_page}}', 'title(10)');
        $this->createIndex('KEY_SHOP_MATERIAL_PAGE_STATUS', '{{%shop_material_page}}', 'status');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%shop_material_page}}');
    }
}
