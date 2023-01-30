<?php

use yii\db\Migration;

/**
 * Class m200616_030507_change_table_user
 */
class m200616_030507_change_table_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%user}}', 'source', 'tinyint(1) NOT NULL DEFAULT 1 COMMENT \'用户来源：1自助注册2手动录入\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200616_030507_change_table_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200616_030507_change_table_user cannot be reverted.\n";

        return false;
    }
    */
}
