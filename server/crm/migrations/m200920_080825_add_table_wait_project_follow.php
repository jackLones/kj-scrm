<?php

use yii\db\Migration;

/**
 * Class m200920_080825_add_table_wait_project_follow
 */
class m200920_080825_add_table_wait_project_follow extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->createTable('{{%wait_project_follow}}', [
			'id'               => $this->primaryKey(11)->unsigned(),
			'customer_task_id' => $this->integer(11)->unsigned()->defaultValue(NULL)->comment('客户任务ID'),
			'per'              => $this->char(32)->defaultValue(NULL)->comment('进度百分比'),
			'per_desc'         => $this->char(255)->defaultValue(NULL)->comment('进度说明'),
			'create_time'      => $this->integer(11)->unsigned()->defaultValue(0)->comment('创建时间'),
		], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'待办项目跟进表\'');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200920_080825_add_table_wait_project_follow cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200920_080825_add_table_wait_project_follow cannot be reverted.\n";

        return false;
    }
    */
}
