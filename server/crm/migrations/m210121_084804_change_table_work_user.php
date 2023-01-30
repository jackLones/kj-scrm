<?php

use yii\db\Migration;

/**
 * Class m210121_084804_change_table_work_user
 */
class m210121_084804_change_table_work_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_user}}', 'sop_chat_msg_status', 'tinyint(1) DEFAULT \'0\' COMMENT \'SOP群消息免打扰是否开启1是0否\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210121_084804_change_table_work_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210121_084804_change_table_work_user cannot be reverted.\n";

        return false;
    }
    */
}
