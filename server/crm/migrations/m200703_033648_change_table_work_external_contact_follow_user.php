<?php

use yii\db\Migration;

/**
 * Class m200703_033648_change_table_work_external_contact_follow_user
 */
class m200703_033648_change_table_work_external_contact_follow_user extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->addColumn('{{%work_external_contact_follow_user}}', 'follow_num', 'int(11) unsigned DEFAULT \'0\' COMMENT \'跟进次数\'');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200703_033648_change_table_work_external_contact_follow_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200703_033648_change_table_work_external_contact_follow_user cannot be reverted.\n";

        return false;
    }
    */
}
