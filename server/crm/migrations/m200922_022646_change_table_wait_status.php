<?php

use yii\db\Migration;

/**
 * Class m200922_022646_change_table_wait_status
 */
class m200922_022646_change_table_wait_status extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%wait_status}}', 'key', 'tinyint(1) DEFAULT 0 COMMENT \'用于前端传的默认字段\' AFTER `sort`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200922_022646_change_table_wait_status cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200922_022646_change_table_wait_status cannot be reverted.\n";

        return false;
    }
    */
}
