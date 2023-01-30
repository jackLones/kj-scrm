<?php

use yii\db\Migration;

/**
 * Handles dropping columns from table `{{%youzan_shop}}`.
 */
class m200527_075923_drop_client_secret_columns_from_youzan_shop_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%youzan_shop}}', 'client_secret');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('{{%youzan_shop}}', 'client_secret', $this->char(18)->comment('有赞云颁 发给开发者的client_secret即应用密钥 长度32位的字母和数字组合的字符串'));
    }
}
