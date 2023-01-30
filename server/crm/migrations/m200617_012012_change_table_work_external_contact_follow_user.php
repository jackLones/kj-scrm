<?php

use yii\db\Migration;

/**
 * Class m200617_012012_change_table_work_external_contact_follow_user
 */
class m200617_012012_change_table_work_external_contact_follow_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_external_contact_follow_user}}', 'follow_id', 'int(11) unsigned DEFAULT 0 COMMENT \'状态id\' AFTER `red_pack_id`');
	    $this->createIndex('KEY_WORK_EXTERNAL_CONTACT_FOLLOW_USER_FOLLOW_ID', '{{%work_external_contact_follow_user}}', 'follow_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200617_012012_change_table_work_external_contact_follow_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200617_012012_change_table_work_external_contact_follow_user cannot be reverted.\n";

        return false;
    }
    */
}
