<?php

use yii\db\Migration;

/**
 * Class m200807_014123_change_table_work_tag_follow_user
 */
class m200807_014123_change_table_work_tag_follow_user extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->addColumn('{{%work_tag_follow_user}}', 'status', 'tinyint(1) unsigned DEFAULT \'0\' COMMENT \'0不显示1显示\' AFTER `tag_id`');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200807_014123_change_table_work_tag_follow_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200807_014123_change_table_work_tag_follow_user cannot be reverted.\n";

        return false;
    }
    */
}
