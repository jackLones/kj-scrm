<?php

use yii\db\Migration;

/**
 * Class m201016_094556_change_table_work_group_sending_redpacket_send
 */
class m201016_094556_change_table_work_group_sending_redpacket_send extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_group_sending_redpacket_send}}', 'get_money', 'decimal(12,2) DEFAULT \'0.00\' COMMENT \'群红包领取金额（is_chat=1时）\' AFTER `send_money`');
	    $this->addColumn('{{%work_group_sending_redpacket_send}}', 'get_num', 'int(11) DEFAULT \'0\' COMMENT \'群红包领取人数（is_chat=1时）\' AFTER `get_money`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201016_094556_change_table_work_group_sending_redpacket_send cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201016_094556_change_table_work_group_sending_redpacket_send cannot be reverted.\n";

        return false;
    }
    */
}
