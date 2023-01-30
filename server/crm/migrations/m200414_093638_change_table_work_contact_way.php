<?php

use yii\db\Migration;

/**
 * Class m200414_093638_change_table_work_contact_way
 */
class m200414_093638_change_table_work_contact_way extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_contact_way}}', 'open_date', 'tinyint(1) DEFAULT \'0\' COMMENT \'0关闭1开启\' after `qr_code`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200414_093638_change_table_work_contact_way cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200414_093638_change_table_work_contact_way cannot be reverted.\n";

        return false;
    }
    */
}
