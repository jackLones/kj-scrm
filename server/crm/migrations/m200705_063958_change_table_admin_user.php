<?php

use yii\db\Migration;

/**
 * Class m200705_063958_change_table_admin_user
 */
class m200705_063958_change_table_admin_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%admin_user}}', 'type', 'tinyint(1) DEFAULT 0 COMMENT \'帐号类型：0总账户 1独家代理 2普通代理\' after `salt`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200705_063958_change_table_admin_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200705_063958_change_table_admin_user cannot be reverted.\n";

        return false;
    }
    */
}
