<?php

use yii\db\Migration;

/**
 * Class m200811_072426_change_table_work_tag_pull_group
 */
class m200811_072426_change_table_work_tag_pull_group extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->addColumn('{{%work_tag_pull_group}}', 'belong_id', 'tinyint(1) unsigned DEFAULT \'2\' COMMENT \'成员归属1全部成员2部分成员\' AFTER `send_type`');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200811_072426_change_table_work_tag_pull_group cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200811_072426_change_table_work_tag_pull_group cannot be reverted.\n";

        return false;
    }
    */
}
