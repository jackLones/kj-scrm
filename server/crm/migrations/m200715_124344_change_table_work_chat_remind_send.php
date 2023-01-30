<?php

use yii\db\Migration;

/**
 * Class m200715_124344_change_table_work_chat_remind_send
 */
class m200715_124344_change_table_work_chat_remind_send extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_chat_remind_send}}', 'remind_id', 'int(11) unsigned DEFAULT 0 COMMENT \'群提醒ID\' after `audit_info_id`');
	    $this->addColumn('{{%work_chat_remind_send}}', 'status', 'tinyint(1) DEFAULT 0 COMMENT \'发送状态 0未发送 1已发送 2发送失败\'');
	    $this->addColumn('{{%work_chat_remind_send}}', 'error_code', 'int(11) unsigned DEFAULT 0 COMMENT \'错误码\'');
	    $this->addColumn('{{%work_chat_remind_send}}', 'error_msg', 'varchar(255) DEFAULT \'\' COMMENT \'错误信息\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200715_124344_change_table_work_chat_remind_send cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200715_124344_change_table_work_chat_remind_send cannot be reverted.\n";

        return false;
    }
    */
}
