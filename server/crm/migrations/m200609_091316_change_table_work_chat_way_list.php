<?php

use yii\db\Migration;

/**
 * Class m200609_091316_change_table_work_chat_way_list
 */
class m200609_091316_change_table_work_chat_way_list extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%work_chat_way_list}}', 'chat_id', 'int(11) unsigned DEFAULT 0 COMMENT \'群列表id\' AFTER `way_id`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200609_091316_change_table_work_chat_way_list cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200609_091316_change_table_work_chat_way_list cannot be reverted.\n";

        return false;
    }
    */
}
