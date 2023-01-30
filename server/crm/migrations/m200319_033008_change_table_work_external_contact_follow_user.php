<?php

use yii\db\Migration;

/**
 * Class m200319_033008_change_table_work_external_contact_follow_user
 */
class m200319_033008_change_table_work_external_contact_follow_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_external_contact_follow_user}}', 'fission_id', 'int(11) DEFAULT \'0\' COMMENT \'裂变任务id\' AFTER `way_id`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200319_033008_change_table_work_external_contact_follow_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200319_033008_change_table_work_external_contact_follow_user cannot be reverted.\n";

        return false;
    }
    */
}
