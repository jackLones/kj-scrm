<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%shop_doudian}}`.
 */
class m210413_065129_create_shop_doudian_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%shop_doudian}}', [
            'id'            => $this->primaryKey(11)->unsigned()->notNull()->defaultExpression('AUTO_INCREMENT'),
            'corp_id'       => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('授权的企业ID'),
            'shop_id'       => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('店铺id'),
            'shop_name'     => $this->string(225)->notNUll()->defaultValue('')->comment('店铺名称'),
            'access_token'  => $this->string(225)->notNUll()->defaultValue('')->comment('用于调用API的access_token'),
            'expires_in'    => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('access_token接口调用凭证超时时间，单位（秒），默认有效期：7天'),
            'scope'         => $this->string(225)->notNUll()->defaultValue('')->comment('授权作用域，使用逗号,分隔。预留字段'),
            'refresh_token' => $this->string(225)->notNUll()->defaultValue('')->comment('用于刷新access_token的刷新令牌（有效期：14 天）'),
            'auth_status'   => $this->tinyInteger(1)->unsigned()->notNUll()->defaultValue(0)->comment('授权状态 0 未授权 1授权 '),
            'auth_time'     => $this->timestamp()->notNUll()->defaultExpression('CURRENT_TIMESTAMP')->comment('最近更新时间'),
            'add_time'      => $this->timestamp()->notNUll()->defaultExpression('CURRENT_TIMESTAMP')->comment('添加时间')
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='抖店店铺表'");
        $this->createIndex('KEY_SHOP_DOUDIAN_CORP_SHOP_ID', '{{%shop_doudian}}', ['corp_id','auth_status']);
        $this->createIndex('KEY_SHOP_DOUDIAN_SHOP_NAME', '{{%shop_doudian}}', 'shop_name(10)');
        $this->createIndex('KEY_SHOP_DOUDIAN_SHOP_ID', '{{%shop_doudian}}', 'shop_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%shop_doudian}}');
    }
}
