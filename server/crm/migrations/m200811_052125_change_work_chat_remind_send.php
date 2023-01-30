<?php

use yii\db\Migration;

/**
 * Class m200811_052125_change_work_chat_remind_send
 */
class m200811_052125_change_work_chat_remind_send extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->dropForeignKey('KEY_WORK_CHAT_REMIND_SEND_REMINDID', '{{%work_chat_remind_send}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200811_052125_change_work_chat_remind_send cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200811_052125_change_work_chat_remind_send cannot be reverted.\n";

        return false;
    }
    */
}
