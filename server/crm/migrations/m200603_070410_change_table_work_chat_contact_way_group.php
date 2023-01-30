<?php

use yii\db\Migration;

/**
 * Class m200603_070410_change_table_work_chat_contact_way_group
 */
class m200603_070410_change_table_work_chat_contact_way_group extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->dropForeignKey('KEY_WORK_CHAT_CONTACT_WAY_GROUP_PARENTID', '{{%work_chat_contact_way_group}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200603_070410_change_table_work_chat_contact_way_group cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200603_070410_change_table_work_chat_contact_way_group cannot be reverted.\n";

        return false;
    }
    */
}
