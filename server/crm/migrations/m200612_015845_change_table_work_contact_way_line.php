<?php

use yii\db\Migration;

/**
 * Class m200612_015845_change_table_work_contact_way_line
 */
class m200612_015845_change_table_work_contact_way_line extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    	$this->addColumn('{{%work_contact_way_line}}','gender','tinyint(2) DEFAULT NULL COMMENT \'外部联系人性别 0-未知 1-男性 2-女性\' AFTER `user_id`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200612_015845_change_table_work_contact_way_line cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200612_015845_change_table_work_contact_way_line cannot be reverted.\n";

        return false;
    }
    */
}
