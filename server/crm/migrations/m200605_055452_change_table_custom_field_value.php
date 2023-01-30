<?php

use yii\db\Migration;

/**
 * Class m200605_055452_change_table_custom_field_value
 */
class m200605_055452_change_table_custom_field_value extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->alterColumn('{{%custom_field_value}}', 'value', 'text COMMENT \'用户属性值\' AFTER `fieldid`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200605_055452_change_table_custom_field_value cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200605_055452_change_table_custom_field_value cannot be reverted.\n";

        return false;
    }
    */
}
