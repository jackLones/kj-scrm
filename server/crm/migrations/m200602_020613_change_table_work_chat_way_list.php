<?php

use yii\db\Migration;

/**
 * Class m200602_020613_change_table_work_chat_way_list
 */
class m200602_020613_change_table_work_chat_way_list extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_chat_way_list}}', 'media_id', 'int(11) unsigned  COMMENT \'图片的id对应attachment的id\' after `add_num`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200602_020613_change_table_work_chat_way_list cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200602_020613_change_table_work_chat_way_list cannot be reverted.\n";

        return false;
    }
    */
}
