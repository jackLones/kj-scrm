<?php

use yii\db\Migration;

/**
 * Class m200416_121230_change_table_external_time_line
 */
class m200416_121230_change_table_external_time_line extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn("{{%external_time_line}}", "related_id", "int(10) DEFAULT 0 COMMENT '相关表id' AFTER `event_id`");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200416_121230_change_table_external_time_line cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200416_121230_change_table_external_time_line cannot be reverted.\n";

        return false;
    }
    */
}
