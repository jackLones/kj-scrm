<?php

use yii\db\Migration;

/**
 * Class m200817_071308_change_table_work_external_contact
 */
class m200817_071308_change_table_work_external_contact extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->createIndex('KEY_WORK_EXTERNAL_CONTACT_UNIONID', '{{%work_external_contact}}', 'unionid');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200817_071308_change_table_work_external_contact cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200817_071308_change_table_work_external_contact cannot be reverted.\n";

        return false;
    }
    */
}
