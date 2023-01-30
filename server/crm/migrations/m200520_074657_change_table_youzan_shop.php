<?php

use yii\db\Migration;

/**
 * Class m200520_074657_change_table_youzan_shop
 */
class m200520_074657_change_table_youzan_shop extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%youzan_shop}}', 'refresh_token', 'varchar(255) NOT NULL DEFAULT \'\' COMMENT \'用于刷新 access_token 的刷新令牌（过期时间：28 天）\' AFTER `expires`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200520_074657_change_table_youzan_shop cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200520_074657_change_table_youzan_shop cannot be reverted.\n";

        return false;
    }
    */
}
