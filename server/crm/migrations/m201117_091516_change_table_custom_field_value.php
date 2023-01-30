<?php

use yii\db\Migration;

/**
 * Class m201117_091516_change_table_custom_field_value
 */
class m201117_091516_change_table_custom_field_value extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%custom_field_value}}', 'user_id', 'int(11) DEFAULT "0" COMMENT \'员工id\' after `cid`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201117_091516_change_table_custom_field_value cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201117_091516_change_table_custom_field_value cannot be reverted.\n";

        return false;
    }
    */
}
