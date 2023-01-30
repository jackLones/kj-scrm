<?php

use yii\db\Migration;

/**
 * Class m210107_074814_change_table_work_sop_msg_sending
 */
class m210107_074814_change_table_work_sop_msg_sending extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_sop_msg_sending}}', 'user_id', 'int(11) unsigned DEFAULT NULL COMMENT \'成员ID\' AFTER `sop_time_id`');
	    $this->addColumn('{{%work_sop_msg_sending}}', 'external_id', 'int(11) unsigned DEFAULT NULL COMMENT \'外部联系人ID\' AFTER `user_id`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210107_074814_change_table_work_sop_msg_sending cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210107_074814_change_table_work_sop_msg_sending cannot be reverted.\n";

        return false;
    }
    */
}
