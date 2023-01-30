<?php

use yii\db\Migration;

/**
 * Class m210127_071442_change_table_work_chat_contact_way
 */
class m210127_071442_change_table_work_chat_contact_way extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_chat_contact_way}}', 'type', 'tinyint(1) DEFAULT 1 COMMENT \'拉群方式：1群二维码2企微活码\' after `way_group_id`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210127_071442_change_table_work_chat_contact_way cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210127_071442_change_table_work_chat_contact_way cannot be reverted.\n";

        return false;
    }
    */
}
