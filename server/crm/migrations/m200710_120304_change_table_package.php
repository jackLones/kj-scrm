<?php

use yii\db\Migration;

/**
 * Class m200710_120304_change_table_package
 */
class m200710_120304_change_table_package extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%package}}', 'is_agent', 'tinyint(1) DEFAULT 0 COMMENT \'代理商是否可用1是0否\' after `is_trial`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200710_120304_change_table_package cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200710_120304_change_table_package cannot be reverted.\n";

        return false;
    }
    */
}
