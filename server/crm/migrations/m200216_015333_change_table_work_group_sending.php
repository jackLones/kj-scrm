<?php

use yii\db\Migration;

/**
 * Class m200216_015333_change_table_work_group_sending
 */
class m200216_015333_change_table_work_group_sending extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_group_sending}}', 'will_num', 'int(11) unsigned DEFAULT 0 COMMENT \'预计人数\' AFTER `fail_list`');
	    $this->addColumn('{{%work_group_sending}}', 'real_num', 'int(11) unsigned DEFAULT 0 COMMENT \'实际人数\' AFTER `fail_list`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200216_015333_change_table_work_group_sending cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200216_015333_change_table_work_group_sending cannot be reverted.\n";

        return false;
    }
    */
}
