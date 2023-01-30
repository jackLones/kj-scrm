<?php

use yii\db\Migration;

/**
 * Class m200820_012814_change_table_system_authority
 */
class m200820_012814_change_table_system_authority extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->insert('{{%system_authority}}', [
		    'pid'         => 2,
		    'url'         => '/admin/user-manage/set-user-agent',
		    'title'       => '客户分配',
		    'nav_display' => 0,
		    'nav_type'    => 1,
		    'status'      => 1,
		    'sort'        => 0,
		    'module'      => 'admin',
		    'controller'  => 'user-manage',
		    'method'      => 'set-user-agent',
	    ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200820_012814_change_table_system_authority cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200820_012814_change_table_system_authority cannot be reverted.\n";

        return false;
    }
    */
}
