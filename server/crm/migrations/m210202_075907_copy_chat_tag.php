<?php

use yii\db\Migration;

/**
 * Class m210202_075907_copy_chat_tag
 */
class m210202_075907_copy_chat_tag extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    \app\models\WorkTagChat::copyChatTag();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210202_075907_copy_chat_tag cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210202_075907_copy_chat_tag cannot be reverted.\n";

        return false;
    }
    */
}
