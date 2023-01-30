<?php

use yii\db\Migration;

/**
 * Class m200723_075312_change_table_system_authority
 */
class m200723_075312_change_table_system_authority extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%system_authority}}', 'method', 'char(80) NOT NULL DEFAULT \'\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200723_075312_change_table_system_authority cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200723_075312_change_table_system_authority cannot be reverted.\n";

        return false;
    }
    */
}
