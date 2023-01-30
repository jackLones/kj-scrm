<?php

use yii\db\Migration;

/**
 * Class m200415_033311_change_table_work_external_contact_follow_record
 */
class m200415_033311_change_table_work_external_contact_follow_record extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn("{{%work_external_contact_follow_record}}", "type", "tinyint(1) DEFAULT 1 COMMENT '跟进类型：1客户2粉丝' AFTER `uid`");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200415_033311_change_table_work_external_contact_follow_record cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200415_033311_change_table_work_external_contact_follow_record cannot be reverted.\n";

        return false;
    }
    */
}
