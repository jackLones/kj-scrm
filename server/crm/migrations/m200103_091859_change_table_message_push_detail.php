<?php

use yii\db\Migration;

/**
 * Class m200103_091859_change_table_message_push_detail
 */
class m200103_091859_change_table_message_push_detail extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%message_push_detail}}', 'num', 'tinyint(1) DEFAULT \'0\' COMMENT \'内容所占短信数\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200103_091859_change_table_message_push_detail cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200103_091859_change_table_message_push_detail cannot be reverted.\n";

        return false;
    }
    */
}
