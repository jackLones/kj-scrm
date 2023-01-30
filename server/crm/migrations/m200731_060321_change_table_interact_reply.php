<?php

use yii\db\Migration;

/**
 * Class m200731_060321_change_table_interact_reply
 */
class m200731_060321_change_table_interact_reply extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%interact_reply}}', 'start_time', 'timestamp NULL DEFAULT \'0000-00-00 00:00:00\' COMMENT \'开始时间\'');
	    $this->alterColumn('{{%interact_reply}}', 'end_time', 'timestamp NULL DEFAULT \'0000-00-00 00:00:00\' COMMENT \'结束时间\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200731_060321_change_table_interact_reply cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200731_060321_change_table_interact_reply cannot be reverted.\n";

        return false;
    }
    */
}
