<?php

use yii\db\Migration;

/**
 * Class m201023_075414_change_table_work_contact_way_redpacket
 */
class m201023_075414_change_table_work_contact_way_redpacket extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_contact_way_redpacket}}', 'out_amount', 'decimal(12,2) NOT NULL DEFAULT \'0.00\' COMMENT \'活动已发出金额\' after `redpacket_amount`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201023_075414_change_table_work_contact_way_redpacket cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201023_075414_change_table_work_contact_way_redpacket cannot be reverted.\n";

        return false;
    }
    */
}
