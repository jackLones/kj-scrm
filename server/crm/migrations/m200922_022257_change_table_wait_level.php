<?php

use yii\db\Migration;

/**
 * Class m200922_022257_change_table_wait_level
 */
class m200922_022257_change_table_wait_level extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%wait_level}}', 'key', 'tinyint(1) DEFAULT 0 COMMENT \'用于前端传的默认字段\' AFTER `sort`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200922_022257_change_table_wait_level cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200922_022257_change_table_wait_level cannot be reverted.\n";

        return false;
    }
    */
}
