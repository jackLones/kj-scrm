<?php

use yii\db\Migration;

/**
 * Class m200223_055233_change_table_work_corp_auth
 */
class m200223_055233_change_table_work_corp_auth extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_corp_auth}}', 'last_customer_tag_time', ' int(11) unsigned DEFAULT NULL COMMENT \'最后一次同步客户标签\' AFTER `last_tag_time`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200223_055233_change_table_work_corp_auth cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200223_055233_change_table_work_corp_auth cannot be reverted.\n";

        return false;
    }
    */
}
