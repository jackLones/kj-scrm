<?php

use yii\db\Migration;

/**
 * Class m210129_022628_change_table_work_external_contact_user_way_detail
 */
class m210129_022628_change_table_work_external_contact_user_way_detail extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->dropForeignKey('KEY_WORK_TAG_CONTACT_CHAT_ID', '{{%work_external_contact_user_way_detail}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210129_022628_change_table_work_external_contact_user_way_detail cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210129_022628_change_table_work_external_contact_user_way_detail cannot be reverted.\n";

        return false;
    }
    */
}
