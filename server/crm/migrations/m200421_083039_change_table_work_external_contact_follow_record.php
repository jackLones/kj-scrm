<?php

use yii\db\Migration;

/**
 * Class m200421_083039_change_table_work_external_contact_follow_record
 */
class m200421_083039_change_table_work_external_contact_follow_record extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn("{{%work_external_contact_follow_record}}", "upt_time", "int(10) DEFAULT 0 COMMENT '更新时间' after `time`");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200421_083039_change_table_work_external_contact_follow_record cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200421_083039_change_table_work_external_contact_follow_record cannot be reverted.\n";

        return false;
    }
    */
}
