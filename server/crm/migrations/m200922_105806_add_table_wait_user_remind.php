<?php

use yii\db\Migration;

/**
 * Class m200922_105806_add_table_wait_user_remind
 */
class m200922_105806_add_table_wait_user_remind extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->createTable('{{%wait_user_remind}}', [
			'id'          => $this->primaryKey(11)->unsigned(),
			'task_id'     => $this->integer(11)->unsigned()->defaultValue(NULL)->comment('任务ID'),
			'user_id'     => $this->integer(11)->unsigned()->defaultValue(NULL)->comment('员工ID'),
			'end_time'    => $this->integer(11)->unsigned()->defaultValue(0)->comment('截止时间'),
			'days'        => $this->integer(11)->unsigned()->defaultValue(0)->comment('天数'),
			'type'        => $this->tinyInteger(1)->unsigned()->defaultValue(0)->comment('1 预计结束时间前 2 项目超时'),
			'create_time' => $this->integer(11)->unsigned()->defaultValue(0)->comment('创建时间'),
		], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'员工任务提醒表\'');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200922_105806_add_table_wait_user_remind cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200922_105806_add_table_wait_user_remind cannot be reverted.\n";

        return false;
    }
    */
}
