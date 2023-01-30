<?php

use yii\db\Migration;

/**
 * Class m200410_010154_change_table_custom_field_option
 */
class m200410_010154_change_table_custom_field_option extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%custom_field_option}}', 'is_del', 'tinyint(1) DEFAULT \'0\' COMMENT \'是否删除\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200410_010154_change_table_custom_field_option cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200410_010154_change_table_custom_field_option cannot be reverted.\n";

        return false;
    }
    */
}
