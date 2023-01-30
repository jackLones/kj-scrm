<?php

use yii\db\Migration;

/**
 * Class m200706_082805_change_table_work_external_contact_follow_user
 */
class m200706_082805_change_table_work_external_contact_follow_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_external_contact_follow_user}}', 'is_chat', ' tinyint(2) unsigned DEFAULT 0 COMMENT \'沟通状态 0一直未沟通 1已沟通\' AFTER `update_time`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200706_082805_change_table_work_external_contact_follow_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200706_082805_change_table_work_external_contact_follow_user cannot be reverted.\n";

        return false;
    }
    */
}
