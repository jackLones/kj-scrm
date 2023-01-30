<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%shop_doudian_config}}`.
 */
class m210418_093907_create_shop_doudian_config_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%shop_doudian_config}}', [
            'id'          => $this->primaryKey(11)->unsigned()->notNull()->defaultExpression('AUTO_INCREMENT'),
            'app_key'     => $this->string(50)->notNUll()->defaultValue('')->comment('证书信息app_key'),
            'app_secret'  => $this->string(50)->notNUll()->defaultValue('')->comment('证书信息app_secret'),
            'service_id'  => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('证书信息service_id'),
            'update_time' => $this->timestamp()->notNUll()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')->comment('更新时间'),
            'add_time'    => $this->timestamp()->notNUll()->defaultExpression('CURRENT_TIMESTAMP')->comment('添加时间')
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='抖店总配置表'");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%shop_doudian_config}}');
    }
}
