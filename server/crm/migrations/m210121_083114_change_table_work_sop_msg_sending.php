<?php

use yii\db\Migration;

/**
 * Class m210121_083114_change_table_work_sop_msg_sending
 */
class m210121_083114_change_table_work_sop_msg_sending extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_sop_msg_sending}}', 'is_chat', 'tinyint(1) DEFAULT \'0\' COMMENT \'是否群SOP消息1是0否\' after `corp_id`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210121_083114_change_table_work_sop_msg_sending cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210121_083114_change_table_work_sop_msg_sending cannot be reverted.\n";

        return false;
    }
    */
}
