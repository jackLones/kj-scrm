<?php

use yii\db\Migration;

/**
 * Class m200709_061437_change_table_user
 */
class m200709_061437_change_table_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%user}}', 'agent_uid', 'int(11) NOT NULL DEFAULT 0 COMMENT \'代理商id 0总后台\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200709_061437_change_table_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200709_061437_change_table_user cannot be reverted.\n";

        return false;
    }
    */
}
