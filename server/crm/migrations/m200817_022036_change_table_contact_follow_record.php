<?php

use yii\db\Migration;

/**
 * Class m200817_022036_change_table_contact_follow_record
 */
class m200817_022036_change_table_contact_follow_record extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_external_contact_follow_record}}', 'chat_id', 'int(11) NOT NULL DEFAULT 0 COMMENT \'客户群ID\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200817_022036_change_table_contact_follow_record cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200817_022036_change_table_contact_follow_record cannot be reverted.\n";

        return false;
    }
    */
}
