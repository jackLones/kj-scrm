<?php

use yii\db\Migration;

/**
 * Class m200811_050959_change_table_work_group_sending
 */
class m200811_050959_change_table_work_group_sending extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->addColumn('{{%work_group_sending}}', 'belong_id', 'tinyint(1) unsigned DEFAULT \'2\' COMMENT \'成员归属1全部成员2部分成员\' AFTER `push_type`');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200811_050959_change_table_work_group_sending cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200811_050959_change_table_work_group_sending cannot be reverted.\n";

        return false;
    }
    */
}
