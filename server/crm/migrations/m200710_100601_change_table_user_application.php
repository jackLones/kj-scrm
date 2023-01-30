<?php

use yii\db\Migration;

/**
 * Class m200710_100601_change_table_user_application
 */
class m200710_100601_change_table_user_application extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%user_application}}', 'remark', 'varchar(1000) DEFAULT \'\' COMMENT \'审核备注\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200710_100601_change_table_user_application cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200710_100601_change_table_user_application cannot be reverted.\n";

        return false;
    }
    */
}
