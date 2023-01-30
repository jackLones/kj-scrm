<?php

use yii\db\Migration;

/**
 * Class m200319_065421_change_table_interact_reply
 */
class m200319_065421_change_table_interact_reply extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%interact_reply}}', 'update_time', 'timestamp NOT NULL DEFAULT \'0000-00-00 00:00:00\' COMMENT \'修改时间\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200319_065421_change_table_interact_reply cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200319_065421_change_table_interact_reply cannot be reverted.\n";

        return false;
    }
    */
}
