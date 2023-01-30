<?php

use yii\db\Migration;

/**
 * Class m200918_053058_change_table_wait_project
 */
class m200918_053058_change_table_wait_project extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->addColumn('{{%wait_project}}', 'sort', 'tinyint(1) DEFAULT \'0\' COMMENT \'排序\' AFTER `finish_time`');
		$this->addColumn('{{%wait_project}}', 'agent_id', 'int(11) unsigned DEFAULT \'0\' COMMENT \'应用ID\' AFTER `corp_id`');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200918_053058_change_table_wait_project cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200918_053058_change_table_wait_project cannot be reverted.\n";

        return false;
    }
    */
}
