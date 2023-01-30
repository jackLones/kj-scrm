<?php

use yii\db\Migration;

/**
 * Class m200921_124806_change_table_wait_task
 */
class m200921_124806_change_table_wait_task extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%wait_task}}', 'content', 'text DEFAULT NULL  COMMENT \'用于前端传值\' AFTER `queue_id`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200921_124806_change_table_wait_task cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200921_124806_change_table_wait_task cannot be reverted.\n";

        return false;
    }
    */
}
