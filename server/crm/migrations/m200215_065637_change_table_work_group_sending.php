<?php

use yii\db\Migration;

/**
 * Class m200215_065637_change_table_work_group_sending
 */
class m200215_065637_change_table_work_group_sending extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_group_sending}}', 'attachment_id', 'int(11) unsigned DEFAULT \'0\' COMMENT \'内容引擎id\' AFTER `status`');
	    $this->addColumn('{{%work_group_sending}}', 'material_sync', 'tinyint(1) unsigned DEFAULT \'0\' COMMENT \'不同步到内容库1同步\' AFTER `status`');
	    $this->addColumn('{{%work_group_sending}}', 'groupId', 'int(11) unsigned DEFAULT \'0\' COMMENT \'分组id\' AFTER `status`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200215_065637_change_table_work_group_sending cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200215_065637_change_table_work_group_sending cannot be reverted.\n";

        return false;
    }
    */
}
