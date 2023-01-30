<?php

use yii\db\Migration;

/**
 * Class m201105_060859_add_table_wait_project_follow
 */
class m201105_060859_add_table_wait_project_follow extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->addColumn('{{%wait_project_follow}}', 'sea_id', 'int(11) unsigned DEFAULT NULL COMMENT \'公海客户\' after `customer_task_id`');
		$this->addColumn('{{%wait_project_follow}}', 'external_userid', 'int(11) unsigned DEFAULT NULL COMMENT \'企微客户外部联系人\' after `customer_task_id`');
		$this->addColumn('{{%wait_project_follow}}', 'task_id', 'int(11) unsigned DEFAULT NULL COMMENT \'任务ID\' after `customer_task_id`');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201105_060859_add_table_wait_project_follow cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201105_060859_add_table_wait_project_follow cannot be reverted.\n";

        return false;
    }
    */
}
