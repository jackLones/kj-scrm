<?php

use yii\db\Migration;

/**
 * Class m200927_054739_add_table_wait_agent
 */
class m200927_054739_add_table_wait_agent extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->createTable('{{%wait_agent}}', [
			'id'          => $this->primaryKey(11)->unsigned(),
			'corp_id'     => $this->integer(11)->unsigned()->defaultValue(NULL)->comment('企业ID'),
			'agent_id'    => $this->integer(11)->unsigned()->defaultValue(NULL)->comment('应用ID'),
			'create_time' => $this->integer(11)->unsigned()->defaultValue(0)->comment('创建时间'),
		], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'待办应用表\'');

		$this->addForeignKey('KEY_WAIT_AGENT_CORP_ID', '{{%wait_agent}}', 'corp_id', '{{%work_corp}}', 'id');
		$this->addForeignKey('KEY_WAIT_AGENT_AGENT_ID', '{{%wait_agent}}', 'agent_id', '{{%work_corp_agent}}', 'id');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200927_054739_add_table_wait_agent cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200927_054739_add_table_wait_agent cannot be reverted.\n";

        return false;
    }
    */
}
