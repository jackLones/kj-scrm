<?php

use yii\db\Migration;

/**
 * Class m210108_084140_change_table_work_external_contact_follow_user
 */
class m210108_084140_change_table_work_external_contact_follow_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_external_contact_follow_user}}', 'third_id', 'varchar(255) DEFAULT NULL COMMENT \'外部系统的客户id\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210108_084140_change_table_work_external_contact_follow_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210108_084140_change_table_work_external_contact_follow_user cannot be reverted.\n";

        return false;
    }
    */
}
