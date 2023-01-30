<?php

use yii\db\Migration;

/**
 * Class m200927_080127_change_table_work_external_contact_follow_user
 */
class m200927_080127_change_table_work_external_contact_follow_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_external_contact_follow_user}}', 'way_redpack_id', 'int(11) unsigned DEFAULT 0 COMMENT \'红包活动活码ID\' AFTER `baidu_way_id`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200927_080127_change_table_work_external_contact_follow_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200927_080127_change_table_work_external_contact_follow_user cannot be reverted.\n";

        return false;
    }
    */
}
