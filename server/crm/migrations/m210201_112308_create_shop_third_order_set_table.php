<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%shop_third_order_set}}`.
 */
class m210201_112308_create_shop_third_order_set_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%shop_third_order_set}}', [
            'id'               => $this->primaryKey(),
            'corp_id'          => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('授权的企业ID'),
            'shop_name'        => $this->string(100)->notNUll()->defaultValue('')->comment('店铺名称'),
            'shop_api_key'     => $this->string(100)->notNUll()->defaultValue('')->comment('对接key，全表唯一'),
            'shop_api_secret'  => $this->string(200)->notNUll()->defaultValue('')->comment('对接密钥'),
            'order_pull_url'   => $this->string(200)->notNUll()->defaultValue('')->comment('第三方的订单拉取地址'),
            'third_api_key'    => $this->string(100)->notNUll()->defaultValue('')->comment('第三方对接key'),
            'third_api_secret' => $this->string(200)->notNUll()->defaultValue('')->comment('第三方对接密钥'),
            'status'           => $this->tinyInteger(1)->unsigned()->notNUll()->defaultValue(0)->comment('默认0未上线 1已上线，已上线才可以生效'),
            'add_time'         => $this->timestamp()->notNUll()->defaultExpression('CURRENT_TIMESTAMP')->comment('添加时间'),
            'update_time'      => $this->timestamp()->notNUll()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')->comment('更新时间'),
        ]);
        $this->createIndex('KEY_SHOP_THIRD_SET_CORP_ID', '{{%shop_third_order_set}}', 'corp_id');
        $this->createIndex('KEY_SHOP_THIRD_SET_KEY', '{{%shop_third_order_set}}', 'shop_api_key');
        $this->addCommentOnTable('{{%shop_third_order_set}}', '第三方订单数据拉取配置');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%shop_third_order_set}}');
    }
}
