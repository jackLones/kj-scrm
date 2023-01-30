<?php

use yii\db\Migration;

/**
 * Class m200212_093126_change_table_work_welcome
 */
class m200212_093126_change_table_work_welcome extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_welcome}}', 'material_sync', 'tinyint(1) unsigned DEFAULT \'0\' COMMENT \'0不同步到内容库1同步\' AFTER `status`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200212_093126_change_table_work_welcome cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200212_093126_change_table_work_welcome cannot be reverted.\n";

        return false;
    }
    */
}
