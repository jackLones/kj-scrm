<?php

use yii\db\Migration;

/**
 * Class m200812_152237_change_table_work_tag_follow_user
 */
class m200812_152237_change_table_work_tag_follow_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_tag_follow_user}}', 'success', 'tinyint(1) unsigned DEFAULT \'0\' COMMENT \'0未跑完1已跑完\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200812_152237_change_table_work_tag_follow_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200812_152237_change_table_work_tag_follow_user cannot be reverted.\n";

        return false;
    }
    */
}
