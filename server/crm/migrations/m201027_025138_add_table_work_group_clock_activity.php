<?php

use yii\db\Migration;

/**
 * Class m201027_025138_add_table_work_group_clock_activity
 */
class m201027_025138_add_table_work_group_clock_activity extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->createTable('{{%work_group_clock_activity}}', [
			'id'          => $this->primaryKey(11)->unsigned(),
			'corp_id'     => $this->integer(11)->unsigned()->defaultValue(NULL)->comment('企业微信ID'),
			'agent_id'    => $this->integer(11)->unsigned()->defaultValue(NULL)->comment('应用ID'),
			'type'        => $this->tinyInteger(1)->unsigned()->defaultValue(1)->comment('类型 1永久有效 2 固定区间'),
			'title'       => $this->char(50)->defaultValue('')->comment('活动名称'),
			'start_time'  => $this->integer(11)->unsigned()->defaultValue(NULL)->comment('开始时间'),
			'end_time'    => $this->integer(11)->unsigned()->defaultValue(NULL)->comment('结束时间'),
			'rule'        => $this->char(255)->defaultValue(NULL)->comment('活动规则'),
			'choose_type' => $this->tinyInteger(1)->unsigned()->defaultValue(1)->comment('打卡类型：1连续打卡 2累计打卡'),
			'status'      => $this->tinyInteger(1)->unsigned()->defaultValue(0)->comment('活动状态：0未开始1进行中2时间结束3手动结束'),
			'is_del'      => $this->tinyInteger(0)->unsigned()->defaultValue(0)->comment('0未删除 1已删除'),
			'update_time' => $this->integer(11)->unsigned()->comment('更新时间'),
			'create_time' => $this->integer(11)->unsigned()->comment('创建时间'),
		], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'群打卡活动表\'');
		$this->createIndex('KEY_WORK_GROUP_CLOCK_ACTIVITY_CHOOSE_TYPE', '{{%work_group_clock_activity}}', 'choose_type');
		$this->createIndex('KEY_WORK_GROUP_CLOCK_ACTIVITY_STATUS', '{{%work_group_clock_activity}}', 'status');
		$this->addForeignKey('KEY_WORK_GROUP_CLOCK_ACTIVITY_CORP_ID', '{{%work_group_clock_activity}}', 'corp_id', '{{%work_corp}}', 'id');
		$this->addForeignKey('KEY_WORK_GROUP_CLOCK_ACTIVITY_AGENT_ID', '{{%work_group_clock_activity}}', 'agent_id', '{{%work_corp_agent}}', 'id');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201027_025138_add_table_work_group_clock_activity cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201027_025138_add_table_work_group_clock_activity cannot be reverted.\n";

        return false;
    }
    */
}
