<?php

use yii\db\Migration;

/**
 * Class m201231_081615_add_cloumns_fllow_id_key
 */
class m201231_081615_add_cloumns_fllow_id_key extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->createIndex("KEY_FOLLOW_RECORD_FOLLOW_ID","{{%work_external_contact_follow_record}}","follow_id");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201231_081615_add_cloumns_fllow_id_key cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201231_081615_add_cloumns_fllow_id_key cannot be reverted.\n";

        return false;
    }
    */
}
