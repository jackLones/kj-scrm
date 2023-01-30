<?php

use yii\db\Migration;

/**
 * Class m200715_063811_change_table_work_chat_remind
 */
class m200715_063811_change_table_work_chat_remind extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_chat_remind}}', 'is_video', 'tinyint(1) DEFAULT \'0\' COMMENT \'是否视频提醒1是0否\' after `is_voice`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200715_063811_change_table_work_chat_remind cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200715_063811_change_table_work_chat_remind cannot be reverted.\n";

        return false;
    }
    */
}
