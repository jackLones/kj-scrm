<?php

use yii\db\Migration;

/**
 * Class m200414_091157_change_table_work_contact_way_date
 */
class m200414_091157_change_table_work_contact_way_date extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_contact_way_date}}', 'day', 'tinyint(1) DEFAULT \'0\' COMMENT \'1到7代表周一到周日\' after `end_date`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200414_091157_change_table_work_contact_way_date cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200414_091157_change_table_work_contact_way_date cannot be reverted.\n";

        return false;
    }
    */
}
