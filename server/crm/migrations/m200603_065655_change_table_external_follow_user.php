<?php

use yii\db\Migration;

/**
 * Class m200603_065655_change_table_external_follow_user
 */
class m200603_065655_change_table_external_follow_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_external_contact_follow_user}}', 'red_pack_id', 'int(11) unsigned DEFAULT "0" COMMENT \'红包裂变id\' AFTER `award_id`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200603_065655_change_table_external_follow_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200603_065655_change_table_external_follow_user cannot be reverted.\n";

        return false;
    }
    */
}
