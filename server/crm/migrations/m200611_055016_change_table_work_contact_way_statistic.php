<?php

use yii\db\Migration;

/**
 * Class m200611_055016_change_table_work_contact_way_statistic
 */
class m200611_055016_change_table_work_contact_way_statistic extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_contact_way_statistic}}', 'group_id', 'int(11) unsigned DEFAULT NULL COMMENT \'分组id\'');
	    $this->createIndex('KEY_WORK_CONTACT_WAY_STATISTIC_GROUP_ID', '{{%work_contact_way_statistic}}', 'group_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200611_055016_change_table_work_contact_way_statistic cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200611_055016_change_table_work_contact_way_statistic cannot be reverted.\n";

        return false;
    }
    */
}
