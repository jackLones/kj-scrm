<?php

use yii\db\Migration;

/**
 * Class m200602_075728_change_table_work_chat_welcome
 */
class m200602_075728_change_table_work_chat_welcome extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_chat_welcome}}', 'template_id', 'varchar(255) NOT NULL DEFAULT \'\' COMMENT \'群欢迎语的素材id\' after `context`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200602_075728_change_table_work_chat_welcome cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200602_075728_change_table_work_chat_welcome cannot be reverted.\n";

        return false;
    }
    */
}
