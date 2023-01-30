<?php

use yii\db\Migration;

/**
 * Class m200531_060812_change_table_work_chat_info
 */
class m200531_060812_change_table_work_chat_info extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_chat_info}}', 'leave_time', 'int(11) NOT NULL DEFAULT 0 COMMENT \'离开时间\' after `join_time`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200531_060812_change_table_work_chat_info cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200531_060812_change_table_work_chat_info cannot be reverted.\n";

        return false;
    }
    */
}
