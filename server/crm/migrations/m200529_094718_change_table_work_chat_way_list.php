<?php

use yii\db\Migration;

/**
 * Class m200529_094718_change_table_work_chat_way_list
 */
class m200529_094718_change_table_work_chat_way_list extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_chat_way_list}}', 'sort', 'int(11) unsigned  COMMENT \'排序\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200529_094718_change_table_work_chat_way_list cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200529_094718_change_table_work_chat_way_list cannot be reverted.\n";

        return false;
    }
    */
}
