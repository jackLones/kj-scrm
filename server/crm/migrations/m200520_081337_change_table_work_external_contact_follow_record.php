<?php

use yii\db\Migration;

/**
 * Class m200520_081337_change_table_work_external_contact_follow_record
 */
class m200520_081337_change_table_work_external_contact_follow_record extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_external_contact_follow_record}}', 'follow_id', 'int(11) DEFAULT 0 COMMENT \'跟进状态id\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200520_081337_change_table_work_external_contact_follow_record cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200520_081337_change_table_work_external_contact_follow_record cannot be reverted.\n";

        return false;
    }
    */
}
