<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%shop_started_source_relationship}}`.
 */
class m210202_010216_create_shop_started_source_relationship_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%shop_started_source_relationship}}', [
            'id'          => $this->primaryKey(),
            'user_id'     => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment(' 发送人ID'),
            'ext_json'    => $this->text()->notNUll()->defaultValue('')->comment('json 存储依赖关系'),
            'info'        => $this->string(100)->unsigned()->notNUll()->defaultValue(0)->comment('备注'),
            'channel'     => $this->tinyInteger(1)->unsigned()->notNUll()->defaultValue(0)->comment('发送渠道，0未知，1：好友ID，2：群ID'),
            'send_from'   => $this->tinyInteger(1)->unsigned()->notNUll()->defaultValue(0)->comment('发送介质，0未知，1：小程序，2：H5'),
            'add_time'    => $this->timestamp()->notNUll()->defaultExpression('CURRENT_TIMESTAMP')->comment('添加时间'),
            'send_time'   => $this->timestamp()->notNUll()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')->comment('更新时间'),
            'update_time' => $this->timestamp()->notNUll()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')->comment('更新时间'),
        ]);
        $this->createIndex('KEY_SHOP_RELATIONSHIP_USER_ID', '{{%shop_started_source_relationship}}', 'user_id');
        $this->createIndex('KEY_SHOP_RELATIONSHIP_CHANNEL', '{{%shop_started_source_relationship}}', 'channel');
        $this->createIndex('KEY_SHOP_RELATIONSHIP_FROM', '{{%shop_started_source_relationship}}', 'send_from');
        $this->addCommentOnTable('{{%shop_started_source_relationship}}', 'scrm外链关系溯源表');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%shop_started_source_relationship}}');
    }
}
