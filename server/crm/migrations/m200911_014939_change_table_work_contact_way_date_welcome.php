<?php

use yii\db\Migration;

/**
 * Class m200911_014939_change_table_work_contact_way_date_welcome
 */
class m200911_014939_change_table_work_contact_way_date_welcome extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%work_contact_way_date_welcome}}', 'day', 'varchar(255)  DEFAULT \'\' COMMENT \'周几\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200911_014939_change_table_work_contact_way_date_welcome cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200911_014939_change_table_work_contact_way_date_welcome cannot be reverted.\n";

        return false;
    }
    */
}
