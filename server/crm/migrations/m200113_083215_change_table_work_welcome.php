<?php

use yii\db\Migration;

/**
 * Class m200113_083215_change_table_work_welcome
 */
class m200113_083215_change_table_work_welcome extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_welcome}}', 'user_name', 'text COMMENT \'成员名称\' after `user_id`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200113_083215_change_table_work_welcome cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200113_083215_change_table_work_welcome cannot be reverted.\n";

        return false;
    }
    */
}
