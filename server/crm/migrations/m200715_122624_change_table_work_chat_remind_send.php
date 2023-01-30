<?php

use yii\db\Migration;

/**
 * Class m200715_122624_change_table_work_chat_remind_send
 */
class m200715_122624_change_table_work_chat_remind_send extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%work_chat_remind_send}}', 'send_user_id', 'varchar(1000) NOT NULL COMMENT \'提醒人成员ID集合\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200715_122624_change_table_work_chat_remind_send cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200715_122624_change_table_work_chat_remind_send cannot be reverted.\n";

        return false;
    }
    */
}
