<?php

use yii\db\Migration;

/**
 * Class m210311_040806_change_table_work_msg_keyword_attachment_info
 */
class m210311_040806_change_table_work_msg_keyword_attachment_info extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->dropForeignKey('KEY_WORK_MSG_KEYWORD_ATTACHMENT_INFO_KEYWORDTAGID', '{{%work_msg_keyword_attachment_info}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210311_040806_change_table_work_msg_keyword_attachment_info cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210311_040806_change_table_work_msg_keyword_attachment_info cannot be reverted.\n";

        return false;
    }
    */
}
