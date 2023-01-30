<?php

use yii\db\Migration;

/**
 * Class m200203_053543_change_table_work_external_contact_follow_user
 */
class m200203_053543_change_table_work_external_contact_follow_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_external_contact_follow_user}}', 'del_time', 'char(32) DEFAULT NULL COMMENT \'删除时间\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200203_053543_change_table_work_external_contact_follow_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200203_053543_change_table_work_external_contact_follow_user cannot be reverted.\n";

        return false;
    }
    */
}
