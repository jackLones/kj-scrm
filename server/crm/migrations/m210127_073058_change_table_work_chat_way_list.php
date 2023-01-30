<?php

use yii\db\Migration;

/**
 * Class m210127_073058_change_table_work_chat_way_list
 */
class m210127_073058_change_table_work_chat_way_list extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_chat_way_list}}', 'chat_way_name', 'varchar(255) DEFAULT NULL COMMENT \'群活码名称\' after `chat_id`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210127_073058_change_table_work_chat_way_list cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210127_073058_change_table_work_chat_way_list cannot be reverted.\n";

        return false;
    }
    */
}
