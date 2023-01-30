<?php

use yii\db\Migration;

/**
 * Class m200916_054029_add_table_wait_task
 */
class m200916_054029_add_table_wait_task extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->createTable('{{%wait_task}}', [
			'id'          => $this->primaryKey(11)->unsigned(),
			'project_id'  => $this->integer(11)->unsigned()->defaultValue(NULL)->comment('待办项目ID'),
			'follow_id'   => $this->integer(11)->unsigned()->defaultValue(NULL)->comment('跟进状态ID'),
			'days'        => $this->integer(11)->unsigned()->defaultValue(0)->comment('多少天后启动'),
			'is_del'      => $this->tinyInteger(1)->defaultValue(0)->comment('是否删除 0未删除 1已删除'),
			'create_time' => $this->integer(11)->unsigned()->defaultValue(0)->comment('创建时间'),
		], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'待办项目提醒表\'');

		$this->addForeignKey('KEY_WAIT_TASK_PROJECT_ID', '{{%wait_task}}', 'project_id', '{{%wait_project}}', 'id');
		$this->addForeignKey('KEY_WAIT_TASK_FOLLOW_ID', '{{%wait_task}}', 'follow_id', '{{%follow}}', 'id');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200916_054029_add_table_wait_task cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200916_054029_add_table_wait_task cannot be reverted.\n";

        return false;
    }
    */
}
