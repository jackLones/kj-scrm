<?php

use yii\db\Migration;

/**
 * Class m201229_033647_add_cloumns_time_key
 */
class m201229_033647_add_cloumns_time_key extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->createIndex("KEY_FOLLOW_RECORD_TIME","{{%work_external_contact_follow_record}}","time");
		$this->createIndex("KEY_FOLLOW_RECORD_USER_ID","{{%work_external_contact_follow_record}}","user_id");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201229_033647_add_cloumns_time_key cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201229_033647_add_cloumns_time_key cannot be reverted.\n";

        return false;
    }
    */
}
