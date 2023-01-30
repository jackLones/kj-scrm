<?php

use yii\db\Migration;

/**
 * Class m200727_122840_change_table_work_group_sending
 */
class m200727_122840_change_table_work_group_sending extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->alterColumn('{{%work_group_sending}}', 'status', 'tinyint(1) DEFAULT \'0\' COMMENT \'发送状态 0未发送（定时发送） 1已发送（员工已确认） 2发送失败 3发送中（员工未确认）\'');
		$this->alterColumn('{{%work_group_sending}}', 'push_time', 'timestamp NOT NULL DEFAULT \'0000-00-00 00:00:00\' COMMENT \'定时发送时间\'');
		$this->addColumn('{{%work_group_sending}}', 'times', 'int(11) unsigned DEFAULT 0 COMMENT \'当前员工发送次数\' ');
		$this->addColumn('{{%work_group_sending}}', 'interval', 'tinyint(1) unsigned DEFAULT 1 COMMENT \'是否开启间隔 1关 2开\' ');
		$this->addColumn('{{%work_group_sending}}', 'interval_time', 'tinyint(1) unsigned DEFAULT 0 COMMENT \'间隔时间 1 （30分钟）  2 （1小时）  3 （2小时）  4 （3小时）  5 （4小时）\' ');
		$this->addColumn('{{%work_group_sending}}', 'interval_num', 'int(11) unsigned DEFAULT 0 COMMENT \'间隔人数 \' ');
		$this->addColumn('{{%work_group_sending}}', 'chat_ids', 'text COMMENT \'群聊id\' ');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200727_122840_change_table_work_group_sending cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200727_122840_change_table_work_group_sending cannot be reverted.\n";

        return false;
    }
    */
}
