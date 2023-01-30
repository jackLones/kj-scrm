<?php

use yii\db\Migration;

/**
 * Class m200726_055102_change_table_user
 */
class m200726_055102_change_table_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%user}}', 'eid', 'int(11) NOT NULL DEFAULT \'0\' COMMENT \'后台员工id\' after `agent_uid`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200726_055102_change_table_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200726_055102_change_table_user cannot be reverted.\n";

        return false;
    }
    */
}
