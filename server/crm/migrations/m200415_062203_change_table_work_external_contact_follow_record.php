<?php

use yii\db\Migration;

/**
 * Class m200415_062203_change_table_work_external_contact_follow_record
 */
class m200415_062203_change_table_work_external_contact_follow_record extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn("{{%work_external_contact_follow_record}}", "file", "varchar(1000) DEFAULT '' COMMENT '图片附件' AFTER `record`");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200415_062203_change_table_work_external_contact_follow_record cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200415_062203_change_table_work_external_contact_follow_record cannot be reverted.\n";

        return false;
    }
    */
}
