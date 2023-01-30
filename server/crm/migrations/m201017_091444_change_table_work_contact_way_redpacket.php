<?php

use yii\db\Migration;

/**
 * Class m201017_091444_change_table_work_contact_way_redpacket
 */
class m201017_091444_change_table_work_contact_way_redpacket extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_contact_way_redpacket}}', 'agent_id', 'int(11) unsigned DEFAULT NULL COMMENT \'应用ID\' AFTER `corp_id`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201017_091444_change_table_work_contact_way_redpacket cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201017_091444_change_table_work_contact_way_redpacket cannot be reverted.\n";

        return false;
    }
    */
}
