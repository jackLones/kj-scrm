<?php

use yii\db\Migration;

/**
 * Class m200624_052944_change_table_work_external_contact_follow_user
 */
class m200624_052944_change_table_work_external_contact_follow_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%work_external_contact_follow_user}}', 'update_time', 'int(10) NOT NULL DEFAULT \'0\' COMMENT \'最后一次跟进状态时间\' AFTER `repeat_type`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200624_052944_change_table_work_external_contact_follow_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200624_052944_change_table_work_external_contact_follow_user cannot be reverted.\n";

        return false;
    }
    */
}
