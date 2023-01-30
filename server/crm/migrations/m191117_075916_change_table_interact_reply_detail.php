<?php

use yii\db\Migration;

/**
 * Class m191117_075916_change_table_interact_reply_detail
 */
class m191117_075916_change_table_interact_reply_detail extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%interact_reply_detail}}', 'msg_id', 'text  DEFAULT NULL COMMENT \'消息发送任务的ID，多个已逗号隔开 \' AFTER `create_time`');
	    $this->addColumn('{{%interact_reply_detail}}', 'queue_id', 'INT(11) unsigned DEFAULT "0" COMMENT \'队列id\' AFTER `msg_id`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191117_075916_change_table_interact_reply_detail cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191117_075916_change_table_interact_reply_detail cannot be reverted.\n";

        return false;
    }
    */
}
