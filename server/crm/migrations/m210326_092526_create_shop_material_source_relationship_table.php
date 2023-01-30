<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%shop_material_source_relationship}}`.
 */
class m210326_092526_create_shop_material_source_relationship_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%shop_material_source_relationship}}', [
            'id'            => $this->primaryKey(11)->unsigned()->notNull()->defaultExpression('AUTO_INCREMENT'),
            'corp_id'       => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('授权的企业ID'),
            'user_id'       => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('发送人id(导购id)'),
            'material_id'   => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('素材id'),
            'material_type' => $this->tinyInteger(1)->unsigned()->notNUll()->defaultValue(0)->comment('素材类型 1 product 商品，2 page 页面，3 coupon 券，'),
            'type'          => $this->tinyInteger(1)->unsigned()->notNUll()->defaultValue(0)->comment('发送类型 1h5,2 小程序'),
            'review_count'  => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('浏览人数'),
            'channel'       => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('发送渠道 （1:好友，2:群）'),
            'chat_id'       => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('群聊id或者好友id'),
            'ext_json'      => $this->text()->comment('发送内容快照'),
            'info'          => $this->string(200)->notNUll()->defaultValue('')->comment('备注'),
            'short_flag'    => $this->string(10)->notNUll()->defaultValue('')->comment('短地址标识'),
            'send_time'     => $this->timestamp()->notNUll()->defaultExpression('CURRENT_TIMESTAMP')->comment('发送时间'),
            'update_time'   => $this->timestamp()->notNUll()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')->comment('更新时间')
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='电商素材关系溯源表'");
        $this->createIndex('KEY_SHOP_MATERIAL_SOURCE_RELATIONSHIP_CORP_ID', '{{%shop_material_source_relationship}}', 'corp_id');
        $this->createIndex('KEY_SHOP_MATERIAL_SOURCE_RELATIONSHIP_USER_ID', '{{%shop_material_source_relationship}}', 'user_id');
        $this->createIndex('KEY_SHOP_MATERIAL_SOURCE_RELATIONSHIP_CHAT_ID', '{{%shop_material_source_relationship}}', 'chat_id');
        $this->createIndex('KEY_SHOP_MATERIAL_SOURCE_RELATIONSHIP_CHANNEL', '{{%shop_material_source_relationship}}', 'channel');
        $this->createIndex('KEY_SHOP_MATERIAL_SOURCE_RELATIONSHIP_TYPE', '{{%shop_material_source_relationship}}', 'type');
        $this->createIndex('KEY_SHOP_MATERIAL_SOURCE_RELATIONSHIP_MATERIAL_ID', '{{%shop_material_source_relationship}}', 'material_id');
        $this->createIndex('KEY_SHOP_MATERIAL_SOURCE_RELATIONSHIP_MATERIAL_TYPE', '{{%shop_material_source_relationship}}', 'material_type');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%shop_material_source_relationship}}');
    }
}
