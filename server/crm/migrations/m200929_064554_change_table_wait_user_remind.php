<?php

use yii\db\Migration;

/**
 * Class m200929_064554_change_table_wait_user_remind
 */
class m200929_064554_change_table_wait_user_remind extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%wait_user_remind}}', 'custom_id', 'int(11) unsigned DEFAULT NULL COMMENT \'客户ID\' AFTER `task_id`');
	    $this->addForeignKey('KEY_WAIT_USER_REMIND_CUSTOM_ID', '{{%wait_user_remind}}', 'custom_id', '{{%wait_customer_task}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200929_064554_change_table_wait_user_remind cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200929_064554_change_table_wait_user_remind cannot be reverted.\n";

        return false;
    }
    */
}
