<?php

use yii\db\Migration;

/**
 * Class m201015_094921_change_table_work_group_sending_redpacket_send
 */
class m201015_094921_change_table_work_group_sending_redpacket_send extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_group_sending_redpacket_send}}', 'is_chat', 'tinyint(1) DEFAULT 0 COMMENT \'是否群红包1是0否（=1时external_userid字段为chat_id）\' AFTER `external_userid`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201015_094921_change_table_work_group_sending_redpacket_send cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201015_094921_change_table_work_group_sending_redpacket_send cannot be reverted.\n";

        return false;
    }
    */
}
