<?php

use yii\db\Migration;

/**
 * Class m200603_065251_change_table_work_external_contact_follow_user
 */
class m200603_065251_change_table_work_external_contact_follow_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_external_contact_follow_user}}', 'chat_way_id', 'int(11) unsigned DEFAULT NULL COMMENT \'群活码联系我配置ID\' AFTER `way_id`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200603_065251_change_table_work_external_contact_follow_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200603_065251_change_table_work_external_contact_follow_user cannot be reverted.\n";

        return false;
    }
    */
}
