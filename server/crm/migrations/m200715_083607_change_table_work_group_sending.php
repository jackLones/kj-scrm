<?php

use yii\db\Migration;

/**
 * Class m200715_083607_change_table_work_group_sending
 */
class m200715_083607_change_table_work_group_sending extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->alterColumn('{{%work_group_sending}}', 'queue_id', 'int(11) DEFAULT \'0\' COMMENT \'队列id\' AFTER `push_time`');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200715_083607_change_table_work_group_sending cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200715_083607_change_table_work_group_sending cannot be reverted.\n";

        return false;
    }
    */
}
