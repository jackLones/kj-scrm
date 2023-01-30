<?php

use yii\db\Migration;

/**
 * Class m191218_080237_change_table_interact_reply_detail
 */
class m191218_080237_change_table_interact_reply_detail extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%interact_reply_detail}}', 'status', 'tinyint(1) unsigned DEFAULT NULL COMMENT \'0成功1失败2未发送\'');
	    $this->addColumn('{{%interact_reply_detail}}', 'push_time', 'timestamp COMMENT \'发送时间\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191218_080237_change_table_interact_reply_detail cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191218_080237_change_table_interact_reply_detail cannot be reverted.\n";

        return false;
    }
    */
}
