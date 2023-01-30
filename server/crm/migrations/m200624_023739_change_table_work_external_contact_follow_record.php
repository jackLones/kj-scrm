<?php

use yii\db\Migration;

/**
 * Class m200624_023739_change_table_work_external_contact_follow_record
 */
class m200624_023739_change_table_work_external_contact_follow_record extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_external_contact_follow_record}}', 'is_master', 'tinyint(1) unsigned DEFAULT \'0\' COMMENT \'状态 0 主账户添加 1 子账户添加 \'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200624_023739_change_table_work_external_contact_follow_record cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
F
    }

    public function down()
    {
        echo "m200624_023739_change_table_work_external_contact_follow_record cannot be reverted.\n";

        return false;
    }
    */
}
