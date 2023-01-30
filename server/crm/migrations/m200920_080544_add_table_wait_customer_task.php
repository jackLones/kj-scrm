<?php

use yii\db\Migration;

/**
 * Class m200920_080544_add_table_wait_customer_task
 */
class m200920_080544_add_table_wait_customer_task extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->createTable('{{%wait_customer_task}}', [
			'id'              => $this->primaryKey(11)->unsigned(),
			'task_id'         => $this->integer(11)->unsigned()->defaultValue(NULL)->comment('任务ID'),
			'external_userid' => $this->integer(11)->unsigned()->defaultValue(NULL)->comment('企微客户外部联系人'),
			'sea_id'          => $this->integer(11)->unsigned()->defaultValue(NULL)->comment('公海客户'),
			'start_time'      => $this->integer(11)->unsigned()->defaultValue(0)->comment('开始时间'),
			'end_time'        => $this->integer(11)->unsigned()->defaultValue(0)->comment('结束时间'),
			'status'          => $this->integer(11)->unsigned()->defaultValue(0)->comment('0未开始 其他代表项目执行状态表的ID'),
			'queue_id'        => $this->integer(11)->unsigned()->defaultValue(0)->comment('队列ID'),
			'type'            => $this->tinyInteger(1)->defaultValue(0)->comment('0企微客户1公海客户'),
			'is_finish'       => $this->tinyInteger(1)->defaultValue(0)->comment('是否完成 0未完成 1已完成'),
			'finish_time'     => $this->integer(11)->defaultValue(0)->comment('实际完成时间'),
			'per'             => $this->char(32)->defaultValue(NULL)->comment('进度百分比'),
			'per_desc'        => $this->char(255)->defaultValue(NULL)->comment('进度说明'),
			'is_del'          => $this->tinyInteger(1)->defaultValue(0)->comment('是否删除 0未删除 1已删除'),
			'create_time'     => $this->integer(11)->unsigned()->defaultValue(0)->comment('创建时间'),
		], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'待办项目客户表\'');

		$this->addForeignKey('KEY_WAIT_CUSTOMER_TASK_TASK_ID', '{{%wait_customer_task}}', 'task_id', '{{%wait_task}}', 'id');
		$this->addForeignKey('KEY_WAIT_CUSTOMER_TASK_EXTERNAL_USERID', '{{%wait_customer_task}}', 'external_userid', '{{%work_external_contact}}', 'id');
		$this->addForeignKey('KEY_WAIT_CUSTOMER_TASK_SEA_ID', '{{%wait_customer_task}}', 'sea_id', '{{%public_sea_customer}}', 'id');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200920_080544_add_table_wait_customer_task cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200920_080544_add_table_wait_customer_task cannot be reverted.\n";

        return false;
    }
    */
}
