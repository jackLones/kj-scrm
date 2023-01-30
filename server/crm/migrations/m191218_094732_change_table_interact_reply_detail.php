<?php

use yii\db\Migration;

/**
 * Class m191218_094732_change_table_interact_reply_detail
 */
class m191218_094732_change_table_interact_reply_detail extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%interact_reply_detail}}', 'inter_time', 'timestamp COMMENT \'记录最后一次关注时间和第一次收到消息时间\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191218_094732_change_table_interact_reply_detail cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191218_094732_change_table_interact_reply_detail cannot be reverted.\n";

        return false;
    }
    */
}
