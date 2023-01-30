<?php

use yii\db\Migration;

/**
 * Class m191219_014200_change_table_message_push
 */
class m191219_014200_change_table_message_push extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%message_push}}', 'smsid', 'varchar(64)  DEFAULT \'\' COMMENT \'短信流水号\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191219_014200_change_table_message_push cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191219_014200_change_table_message_push cannot be reverted.\n";

        return false;
    }
    */
}
