<?php

use yii\db\Migration;

/**
 * Handles dropping columns from table `{{%youzan_shop}}`.
 */
class m200527_075813_drop_client_id_columns_from_youzan_shop_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%youzan_shop}}', 'client_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('{{%youzan_shop}}', 'client_id', $this->char(18)->comment('有赞云颁发给开发者的client_id即应用ID 长度18位字母和数字组合的字符串'));
    }
}
