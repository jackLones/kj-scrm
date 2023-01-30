<?php

use yii\db\Migration;

/**
 * Class m200907_071846_change_table_work_contact_way_date_welcome_content
 */
class m200907_071846_change_table_work_contact_way_date_welcome_content extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_contact_way_date_welcome_content}}', 'welcome', 'text COMMENT \'欢迎语给前端用的\' AFTER `content`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200907_071846_change_table_work_contact_way_date_welcome_content cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200907_071846_change_table_work_contact_way_date_welcome_content cannot be reverted.\n";

        return false;
    }
    */
}
