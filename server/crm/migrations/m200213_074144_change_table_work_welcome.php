<?php

use yii\db\Migration;

/**
 * Class m200213_074144_change_table_work_welcome
 */
class m200213_074144_change_table_work_welcome extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_welcome}}', 'sync_attachment_id', 'int(11) unsigned DEFAULT \'0\' COMMENT \'同步后的素材id\' AFTER `status`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200213_074144_change_table_work_welcome cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200213_074144_change_table_work_welcome cannot be reverted.\n";

        return false;
    }
    */
}
