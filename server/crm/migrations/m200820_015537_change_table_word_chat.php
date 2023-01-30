<?php

use yii\db\Migration;

/**
 * Class m200820_015537_change_table_word_chat
 */
class m200820_015537_change_table_word_chat extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%work_chat}}', 'status', ' tinyint(1) NOT NULL DEFAULT \'0\' COMMENT \'客户群状态 0-正常 1-跟进人离职 2-离职继承中 3-离职继承完成 4-群已解散\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200820_015537_change_table_word_chat cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200820_015537_change_table_word_chat cannot be reverted.\n";

        return false;
    }
    */
}
