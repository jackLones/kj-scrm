<?php

use yii\db\Migration;

/**
 * Class m200716_012841_add_table_work_chat_remind_send
 */
class m200716_012841_add_table_work_chat_remind_send extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_chat_remind_send}}', 'corp_id', 'int(11) unsigned NOT NULL COMMENT \'企业微信id\' after `id`');
	    $this->addColumn('{{%work_chat_remind_send}}', 'tolist', 'text NOT NULL COMMENT \'消息接收方列表\' after `external_id`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200716_012841_add_table_work_chat_remind_send cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200716_012841_add_table_work_chat_remind_send cannot be reverted.\n";

        return false;
    }
    */
}
