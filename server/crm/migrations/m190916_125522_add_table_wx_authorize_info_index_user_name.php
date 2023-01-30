<?php

use yii\db\Migration;

/**
 * Class m190916_125522_add_table_wx_authorize_info_index_user_name
 */
class m190916_125522_add_table_wx_authorize_info_index_user_name extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    	$this->createIndex('KEY_WX_AUTHORIZE_INFO_USERNAME', '{{%wx_authorize_info}}', 'user_name');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190916_125522_add_table_wx_authorize_info_index_user_name cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190916_125522_add_table_wx_authorize_info_index_user_name cannot be reverted.\n";

        return false;
    }
    */
}
