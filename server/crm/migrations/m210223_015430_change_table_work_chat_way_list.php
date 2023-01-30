<?php

use yii\db\Migration;

/**
 * Class m210223_015430_change_table_work_chat_way_list
 */
class m210223_015430_change_table_work_chat_way_list extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->dropForeignKey('KEY_WORK_CHAT_WAY_LIST_CHAT_ID', '{{%work_chat_way_list}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210223_015430_change_table_work_chat_way_list cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210223_015430_change_table_work_chat_way_list cannot be reverted.\n";

        return false;
    }
    */
}
