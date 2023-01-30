<?php

use yii\db\Migration;

/**
 * Class m200813_065002_change_table_custom_field_value
 */
class m200813_065002_change_table_custom_field_value extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%custom_field_value}}', 'type', ' tinyint(1) NOT NULL DEFAULT \'1\' COMMENT \'类型：1客户2粉丝3客户群\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200813_065002_change_table_custom_field_value cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200813_065002_change_table_custom_field_value cannot be reverted.\n";

        return false;
    }
    */
}
