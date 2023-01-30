<?php

use yii\db\Migration;

/**
 * Class m200917_021229_change_table_sea_customer
 */
class m200917_021229_change_table_sea_customer extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%public_sea_customer}}', 'sub_id', 'int(11) unsigned DEFAULT 0 COMMENT \'子账户id\' AFTER `uid`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200917_021229_change_table_sea_customer cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200917_021229_change_table_sea_customer cannot be reverted.\n";

        return false;
    }
    */
}
