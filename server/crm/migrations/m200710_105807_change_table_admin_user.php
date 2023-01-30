<?php

use yii\db\Migration;

/**
 * Class m200710_105807_change_table_admin_user
 */
class m200710_105807_change_table_admin_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%admin_user}}', 'phone', 'char(64) DEFAULT \'\' COMMENT \'手机号（代理商）\' after `account`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200710_105807_change_table_admin_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200710_105807_change_table_admin_user cannot be reverted.\n";

        return false;
    }
    */
}
