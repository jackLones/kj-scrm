<?php

use yii\db\Migration;

/**
 * Class m201028_115622_add_table_work_group_clock_task
 */
class m201028_115622_add_table_work_group_clock_task extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->createTable('{{%work_group_clock_task}}', [
			'id'           => $this->primaryKey(11)->unsigned(),
			'activity_id'  => $this->integer(11)->unsigned()->defaultValue(NULL)->comment('活动ID'),
			'days'         => $this->integer(11)->unsigned()->defaultValue(0)->comment('打卡天数'),
			'type'         => $this->tinyInteger(1)->unsigned()->defaultValue(1)->comment('奖品类型 1实物 2红包'),
			'reward_name'  => $this->string(50)->defaultValue('')->comment('奖品名称'),
			'reward_stock' => $this->integer(11)->unsigned()->defaultValue(0)->comment('奖品库存'),
			'money_amount' => $this->decimal(10, 2)->unsigned()->defaultValue(0)->comment('红包金额'),
			'money_count'  => $this->integer(11)->unsigned()->defaultValue(0)->comment('红包数量'),
			'reward_type'  => $this->tinyInteger(1)->unsigned()->defaultValue(1)->comment('奖品方式：联系客服 2兑换链接'),
			'user_key'     => $this->text()->defaultValue(NULL)->comment('客服人员'),
			'is_open'       => $this->tinyInteger(1)->unsigned()->defaultValue(1)->comment('0不可用1可用'),
			'config_id'    => $this->string(64)->defaultValue('')->comment('联系方式的配置id'),
			'qr_code'      => $this->string(255)->defaultValue('')->comment('联系二维码的URL'),
			'create_time'  => $this->integer(11)->unsigned()->comment('创建时间'),
		], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'群打卡阶段任务表\'');
		$this->addForeignKey('KEY_work_group_clock_task_activity_id', '{{%work_group_clock_task}}', 'activity_id', '{{%work_group_clock_activity}}', 'id');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201028_115622_add_table_work_group_clock_task cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201028_115622_add_table_work_group_clock_task cannot be reverted.\n";

        return false;
    }
    */
}
