<?php

use yii\db\Migration;

/**
 * Class m200424_055443_change_table_work_contact_way_date_user
 */
class m200424_055443_change_table_work_contact_way_date_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%work_contact_way_date_user}}', 'user_key', 'text COMMENT \'用户选择的key值\' AFTER `time`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200424_055443_change_table_work_contact_way_date_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200424_055443_change_table_work_contact_way_date_user cannot be reverted.\n";

        return false;
    }
    */
}
