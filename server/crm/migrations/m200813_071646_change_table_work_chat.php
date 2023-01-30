<?php

use yii\db\Migration;

/**
 * Class m200813_071646_change_table_work_chat
 */
class m200813_071646_change_table_work_chat extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->addColumn('{{%work_chat}}', 'follow_id', 'int(11) NOT NULL DEFAULT \'0\' COMMENT \'跟进状态ID\'');
		$this->addColumn('{{%work_chat}}', 'update_time', 'int(11) NOT NULL DEFAULT \'0\' COMMENT \'最后一次跟进状态时间\'');
		$this->addColumn('{{%work_chat}}', 'remark', 'char(64) DEFAULT \'\' COMMENT \'备注\'');
		$this->addColumn('{{%work_chat}}', 'des', 'char(255) DEFAULT \'\' COMMENT \'描述\'');
		$this->addColumn('{{%work_chat}}', 'close_rate', 'int(11) unsigned DEFAULT NULL COMMENT \'预计成交率\'');
		$this->addColumn('{{%work_chat}}', 'follow_num', 'int(11) unsigned DEFAULT 0 COMMENT \'跟进次数\'');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200813_071646_change_table_work_chat cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200813_071646_change_table_work_chat cannot be reverted.\n";

        return false;
    }
    */
}
