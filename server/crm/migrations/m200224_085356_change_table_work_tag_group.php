<?php

use yii\db\Migration;

/**
 * Class m200224_085356_change_table_work_tag_group
 */
class m200224_085356_change_table_work_tag_group extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->addColumn('{{%work_tag_group}}', 'group_id', 'char(32) DEFAULT NULL COMMENT \'企业微信的分组id\' AFTER `group_name`');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200224_085356_change_table_work_tag_group cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200224_085356_change_table_work_tag_group cannot be reverted.\n";

        return false;
    }
    */
}
