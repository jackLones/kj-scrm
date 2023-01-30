<?php

use yii\db\Migration;

/**
 * Class m200717_123822_change_table_work_chat_way_list
 */
class m200717_123822_change_table_work_chat_way_list extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_chat_way_list}}', 'tag_pull_id', 'int(11) unsigned DEFAULT NULL COMMENT \'标签拉群的id \' AFTER `way_id` ');
	    $this->addForeignKey('KEY_WORK_CHAT_WAY_LIST_TAG_PULL_ID', '{{%work_chat_way_list}}', 'tag_pull_id', '{{%work_tag_pull_group}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200717_123822_change_table_work_chat_way_list cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200717_123822_change_table_work_chat_way_list cannot be reverted.\n";

        return false;
    }
    */
}
