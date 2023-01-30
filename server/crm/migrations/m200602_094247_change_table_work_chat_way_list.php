<?php

use yii\db\Migration;

/**
 * Class m200602_094247_change_table_work_chat_way_list
 */
class m200602_094247_change_table_work_chat_way_list extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->dropColumn('{{%work_chat_way_list}}', 'add_num');
		$this->dropColumn('{{%work_chat_way_list}}', 'total');
		$this->addColumn('{{%work_chat_way_list}}', 'chat_status', 'tinyint(1) NOT NULL DEFAULT \'0\' COMMENT \'0未开始1拉人中2已满群\'');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200602_094247_change_table_work_chat_way_list cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200602_094247_change_table_work_chat_way_list cannot be reverted.\n";

        return false;
    }
    */
}
