<?php

use yii\db\Migration;

/**
 * Class m200917_020136_change_table_wait_task
 */
class m200917_020136_change_table_wait_task extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%wait_task}}', 'type', 'tinyint(1) DEFAULT \'0\' COMMENT \'1手动开启2自动开启3N天后开启\' AFTER `follow_id`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200917_020136_change_table_wait_task cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200917_020136_change_table_wait_task cannot be reverted.\n";

        return false;
    }
    */
}
