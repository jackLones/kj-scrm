<?php

use yii\db\Migration;

/**
 * Class m200217_011716_change_table_work_group_sending
 */
class m200217_011716_change_table_work_group_sending extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_group_sending}}', 'success_list', 'text COMMENT \'成功人员\' AFTER `fail_list`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200217_011716_change_table_work_group_sending cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200217_011716_change_table_work_group_sending cannot be reverted.\n";

        return false;
    }
    */
}
