<?php

use yii\db\Migration;

/**
 * Class m200420_030015_change_table_custom_field_user
 */
class m200420_030015_change_table_custom_field_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn("{{%custom_field_user}}", "sort", "int(10) DEFAULT 0 COMMENT '排序值'");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200420_030015_change_table_custom_field_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200420_030015_change_table_custom_field_user cannot be reverted.\n";

        return false;
    }
    */
}
