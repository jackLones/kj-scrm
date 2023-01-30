<?php

use yii\db\Migration;

/**
 * Class m200415_014426_change_table_work_contact_way_date
 */
class m200415_014426_change_table_work_contact_way_date extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%work_contact_way_date}}','day','char(32) DEFAULT NULL COMMENT \'周几\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200415_014426_change_table_work_contact_way_date cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200415_014426_change_table_work_contact_way_date cannot be reverted.\n";

        return false;
    }
    */
}
