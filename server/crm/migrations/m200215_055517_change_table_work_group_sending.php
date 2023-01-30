<?php

use yii\db\Migration;

/**
 * Class m200215_055517_change_table_work_group_sending
 */
class m200215_055517_change_table_work_group_sending extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_group_sending}}', 'sync_attachment_id', 'int(11)  COMMENT \'同步后的素材id\' AFTER `status`');
	    $this->addColumn('{{%work_group_sending}}', 'work_material_id', 'int(11)  COMMENT \'企业微信素材id\' AFTER `status`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200215_055517_change_table_work_group_sending cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200215_055517_change_table_work_group_sending cannot be reverted.\n";

        return false;
    }
    */
}
