<?php

use yii\db\Migration;

/**
 * Class m200121_053549_change_table_work_corp
 */
class m200121_053549_change_table_work_corp extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_corp}}', 'last_tag_time', 'int(11) unsigned COMMENT \'最后一次同步企业微信标签\' AFTER `sync_user_time`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200121_053549_change_table_work_corp cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200121_053549_change_table_work_corp cannot be reverted.\n";

        return false;
    }
    */
}
