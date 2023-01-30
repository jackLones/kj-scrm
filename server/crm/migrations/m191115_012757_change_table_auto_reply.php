<?php

use yii\db\Migration;

/**
 * Class m191115_012757_change_table_auto_reply
 */
class m191115_012757_change_table_auto_reply extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%auto_reply}}', 'inter_id', 'int(11) unsigned DEFAULT 0 COMMENT \'智能互动表id\' after `author_id`');
	    $this->addForeignKey('KEY_AUTO_REPLY_INTERID', '{{%auto_reply}}', 'inter_id', '{{%interact_reply}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191115_012757_change_table_auto_reply cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191115_012757_change_table_auto_reply cannot be reverted.\n";

        return false;
    }
    */
}
