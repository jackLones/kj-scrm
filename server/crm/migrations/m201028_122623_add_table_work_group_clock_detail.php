<?php

use yii\db\Migration;

/**
 * Class m201028_122623_add_table_work_group_clock_detail
 */
class m201028_122623_add_table_work_group_clock_detail extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->createTable('{{%work_group_clock_detail}}', [
			'id'          => $this->primaryKey(11)->unsigned(),
			'join_id'     => $this->integer(11)->unsigned()->defaultValue(NULL)->comment('参与者ID'),
			'punch_time'  => $this->string(32)->defaultValue('')->comment('打卡时间'),
			'create_time' => $this->integer(11)->unsigned()->comment('创建时间'),
		], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'群打卡阶段任务表\'');
		$this->addForeignKey('KEY_WORK_GROUP_CLOCK_DETAIL_JOIN_ID', '{{%work_group_clock_detail}}', 'join_id', '{{%work_group_clock_join}}', 'id');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201028_122623_add_table_work_group_clock_detail cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201028_122623_add_table_work_group_clock_detail cannot be reverted.\n";

        return false;
    }
    */
}
