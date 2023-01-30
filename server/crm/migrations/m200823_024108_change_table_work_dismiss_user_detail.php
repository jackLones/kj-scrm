<?php

use yii\db\Migration;

/**
 * Class m200823_024108_change_table_work_dismiss_user_detail
 */
class m200823_024108_change_table_work_dismiss_user_detail extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%work_dismiss_user_detail}}', 'status', 'tinyint(1) unsigned DEFAULT \'0\' COMMENT \'0待分配1已分配2客户拒绝3接替成员客户达到上限\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200823_024108_change_table_work_dismiss_user_detail cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200823_024108_change_table_work_dismiss_user_detail cannot be reverted.\n";

        return false;
    }
    */
}
