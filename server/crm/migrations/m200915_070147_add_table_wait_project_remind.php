<?php

use yii\db\Migration;

/**
 * Class m200915_070147_add_table_wait_project_remind
 */
class m200915_070147_add_table_wait_project_remind extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->createTable('{{%wait_project_remind}}', [
			'id'          => $this->primaryKey(11)->unsigned(),
			'project_id'  => $this->integer(11)->unsigned()->defaultValue(NULL)->comment('待办项目ID'),
			'type'        => $this->tinyInteger(1)->defaultValue(0)->comment('1 预计结束时间前 2 项目超时'),
			'days'        => $this->integer(11)->unsigned()->defaultValue(NULL)->comment('天数'),
			'create_time' => $this->integer(11)->unsigned()->defaultValue(0)->comment('创建时间'),
		], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'待办项目提醒表\'');

		$this->addForeignKey('KEY_WAIT_PROJECT_REMIND_PROJECT_ID', '{{%wait_project_remind}}', 'project_id', '{{%wait_project}}', 'id');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200915_070147_add_table_wait_project_remind cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200915_070147_add_table_wait_project_remind cannot be reverted.\n";

        return false;
    }
    */
}
