<?php

use yii\db\Migration;

/**
 * Class m200327_023813_change_table_awards_records
 */
class m200327_023813_change_table_awards_records extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->addColumn('{{%awards_records}}', 'num', 'int(11) unsigned DEFAULT \'0\'  COMMENT \'抽奖次数\' AFTER `is_record`');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200327_023813_change_table_awards_records cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200327_023813_change_table_awards_records cannot be reverted.\n";

        return false;
    }
    */
}
