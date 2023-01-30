<?php

use yii\db\Migration;

/**
 * Class m200918_014847_change_table_wait_task
 */
class m200918_014847_change_table_wait_task extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%wait_task}}', 'queue_id', 'int(11) unsigned DEFAULT \'0\' COMMENT \'队列ID\' AFTER `days`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200918_014847_change_table_wait_task cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200918_014847_change_table_wait_task cannot be reverted.\n";

        return false;
    }
    */
}
