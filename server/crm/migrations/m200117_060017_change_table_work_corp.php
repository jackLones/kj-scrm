<?php

use yii\db\Migration;

/**
 * Class m200117_060017_change_table_work_corp
 */
class m200117_060017_change_table_work_corp extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->addColumn('{{%work_corp}}', 'sync_user_time', 'int(11) unsigned COMMENT \'最后一次同步通讯录时间\' AFTER `auth_type`');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200117_060017_change_table_work_corp cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200117_060017_change_table_work_corp cannot be reverted.\n";

        return false;
    }
    */
}
