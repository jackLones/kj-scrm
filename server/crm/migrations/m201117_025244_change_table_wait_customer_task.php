<?php

use yii\db\Migration;

/**
 * Class m201117_025244_change_table_wait_customer_task
 */
class m201117_025244_change_table_wait_customer_task extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%wait_customer_task}}', 'open_time', 'int(11) DEFAULT 0 COMMENT \'启动时间\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201117_025244_change_table_wait_customer_task cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201117_025244_change_table_wait_customer_task cannot be reverted.\n";

        return false;
    }
    */
}
