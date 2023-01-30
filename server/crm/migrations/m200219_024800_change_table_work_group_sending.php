<?php

use yii\db\Migration;

/**
 * Class m200219_024800_change_table_work_group_sending
 */
class m200219_024800_change_table_work_group_sending extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_group_sending}}', 'update_time', 'timestamp NOT NULL DEFAULT \'0000-00-00 00:00:00\' COMMENT \'更新时间\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200219_024800_change_table_work_group_sending cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200219_024800_change_table_work_group_sending cannot be reverted.\n";

        return false;
    }
    */
}
