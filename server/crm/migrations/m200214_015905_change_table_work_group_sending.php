<?php

use yii\db\Migration;

/**
 * Class m200214_015905_change_table_work_group_sending
 */
class m200214_015905_change_table_work_group_sending extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->addColumn('{{%work_group_sending}}', 'error_code', 'int(11) unsigned DEFAULT \'0\' COMMENT \'错误码\' AFTER `status`');
		$this->addColumn('{{%work_group_sending}}', 'error_msg', 'varchar(255)  COMMENT \'错误信息\' AFTER `status`');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200214_015905_change_table_work_group_sending cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200214_015905_change_table_work_group_sending cannot be reverted.\n";

        return false;
    }
    */
}
