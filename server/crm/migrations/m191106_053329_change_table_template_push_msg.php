<?php

use yii\db\Migration;

/**
 * Class m191106_053329_change_table_template_push_msg
 */
class m191106_053329_change_table_template_push_msg extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%template_push_msg}}', 'msg_id', 'text  COMMENT \'消息id，多个逗号隔开 \' AFTER `queue_id` ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191106_053329_change_table_template_push_msg cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191106_053329_change_table_template_push_msg cannot be reverted.\n";

        return false;
    }
    */
}
