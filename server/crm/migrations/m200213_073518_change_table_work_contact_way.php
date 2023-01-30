<?php

use yii\db\Migration;

/**
 * Class m200213_073518_change_table_work_contact_way
 */
class m200213_073518_change_table_work_contact_way extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_contact_way}}', 'sync_attachment_id', 'int(11) unsigned DEFAULT \'0\' COMMENT \'同步后的素材id\' AFTER `status`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200213_073518_change_table_work_contact_way cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200213_073518_change_table_work_contact_way cannot be reverted.\n";

        return false;
    }
    */
}
