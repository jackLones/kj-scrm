<?php

use yii\db\Migration;

/**
 * Class m200423_060652_change_table_work_contact_way_date_user
 */
class m200423_060652_change_table_work_contact_way_date_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->createIndex('KEY_WORK_CONTACT_WAY_DATE_USER_TIME', '{{%work_contact_way_date_user}}', 'time');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200423_060652_change_table_work_contact_way_date_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200423_060652_change_table_work_contact_way_date_user cannot be reverted.\n";

        return false;
    }
    */
}
