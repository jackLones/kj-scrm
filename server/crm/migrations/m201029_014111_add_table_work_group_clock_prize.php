<?php

use yii\db\Migration;

/**
 * Class m201029_014111_add_table_work_group_clock_prize
 */
class m201029_014111_add_table_work_group_clock_prize extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->createTable('{{%work_group_clock_prize}}', [
			'id'           => $this->primaryKey(11)->unsigned(),
			'join_id'      => $this->integer(11)->unsigned()->defaultValue(NULL)->comment('参与者ID'),
			'task_id'      => $this->integer(11)->unsigned()->defaultValue(NULL)->comment('任务ID'),
			'send'         => $this->tinyInteger(1)->unsigned()->defaultValue(0)->comment('0未发送1已发送'),
			'send_time'    => $this->integer(11)->unsigned()->defaultValue(0)->comment('发送时间'),
			'days'         => $this->integer(11)->unsigned()->defaultValue(0)->comment('打卡天数'),
			'type'         => $this->tinyInteger(1)->unsigned()->defaultValue(1)->comment('奖品类型 1实物 2红包'),
			'money_amount' => $this->decimal(10, 2)->unsigned()->defaultValue(0)->comment('红包金额'),
			'reward_name'  => $this->string(50)->defaultValue('')->comment('奖品名称'),
			'create_time'  => $this->integer(11)->unsigned()->comment('创建时间'),
		], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'群打卡奖品发放表\'');
		$this->addForeignKey('KEY_WORK_GROUP_CLOCK_PRIZE_TASK_ID', '{{%work_group_clock_prize}}', 'task_id', '{{%work_group_clock_task}}', 'id');
		$this->addForeignKey('KEY_WORK_GROUP_CLOCK_PRIZE_JOIN_ID', '{{%work_group_clock_prize}}', 'join_id', '{{%work_group_clock_join}}', 'id');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201029_014111_add_table_work_group_clock_prize cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201029_014111_add_table_work_group_clock_prize cannot be reverted.\n";

        return false;
    }
    */
}
