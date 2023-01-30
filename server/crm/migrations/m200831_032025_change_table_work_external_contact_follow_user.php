<?php

use yii\db\Migration;

/**
 * Class m200831_032025_change_table_work_external_contact_follow_user
 */
class m200831_032025_change_table_work_external_contact_follow_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_external_contact_follow_user}}', 'activity_id', 'int(11) NOT NULL DEFAULT 0 COMMENT \'任务宝id\' ');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200831_032025_change_table_work_external_contact_follow_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200831_032025_change_table_work_external_contact_follow_user cannot be reverted.\n";

        return false;
    }
    */
}
