<?php

use yii\db\Migration;

/**
 * Class m200305_094425_change_table_work_group_sending
 */
class m200305_094425_change_table_work_group_sending extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%work_group_sending}}', 'push_time', 'timestamp NOT NULL DEFAULT \'0000-00-00 00:00:00\' COMMENT \'发送时间\' AFTER `push_type`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200305_094425_change_table_work_group_sending cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200305_094425_change_table_work_group_sending cannot be reverted.\n";

        return false;
    }
    */
}
