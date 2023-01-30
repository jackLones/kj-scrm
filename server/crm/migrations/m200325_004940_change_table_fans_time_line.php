<?php

use yii\db\Migration;

/**
 * Class m200325_004940_change_table_fans_time_line
 */
class m200325_004940_change_table_fans_time_line extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->alterColumn('{{%fans_time_line}}', 'event', 'char(32) DEFAULT NULL COMMENT \'行为\'');
		$this->createIndex('IDX_FANS_TIME_LINE_EVENT', '{{%fans_time_line}}', 'event');
		$this->createIndex('IDX_FANS_TIME_LINE_EVENT_TIME', '{{%fans_time_line}}', 'event_time');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200325_004940_change_table_fans_time_line cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200325_004940_change_table_fans_time_line cannot be reverted.\n";

        return false;
    }
    */
}
