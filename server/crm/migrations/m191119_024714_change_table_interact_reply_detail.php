<?php

use yii\db\Migration;

/**
 * Class m191119_024714_change_table_interact_reply_detail
 */
class m191119_024714_change_table_interact_reply_detail extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%interact_reply_detail}}', 'inter_id', 'INT(11) unsigned DEFAULT "0" COMMENT \'智能互动id\' AFTER `author_id`');
	    $this->addForeignKey('KEY_INTERACT_DETAIL_INTERID', '{{%interact_reply_detail}}', 'inter_id', '{{%interact_reply}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191119_024714_change_table_interact_reply_detail cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191119_024714_change_table_interact_reply_detail cannot be reverted.\n";

        return false;
    }
    */
}
