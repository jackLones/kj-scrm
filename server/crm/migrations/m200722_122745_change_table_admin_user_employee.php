<?php

use yii\db\Migration;

/**
 * Class m200722_122745_change_table_admin_user_employee
 */
class m200722_122745_change_table_admin_user_employee extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%admin_user_employee}}', 'pwd', 'varchar(255) NOT NULL COMMENT \'加密后的密码\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200722_122745_change_table_admin_user_employee cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200722_122745_change_table_admin_user_employee cannot be reverted.\n";

        return false;
    }
    */
}
