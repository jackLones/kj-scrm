<?php

use yii\db\Migration;

/**
 * Class m210305_070302_change_table_work_msg_keyword_user
 */
class m210305_070302_change_table_work_msg_keyword_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_msg_keyword_user}}', 'keyword_tag_id', 'int(11) unsigned DEFAULT 0 COMMENT \'推荐规则关联标签表ID\' AFTER `keyword_id`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210305_070302_change_table_work_msg_keyword_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210305_070302_change_table_work_msg_keyword_user cannot be reverted.\n";

        return false;
    }
    */
}
