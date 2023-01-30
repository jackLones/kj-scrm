<?php

use yii\db\Migration;

/**
 * Class m200212_112030_change_table_work_welcome
 */
class m200212_112030_change_table_work_welcome extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_welcome}}', 'groupId', 'int(11) unsigned DEFAULT \'0\' COMMENT \'分组id\' AFTER `status`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200212_112030_change_table_work_welcome cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200212_112030_change_table_work_welcome cannot be reverted.\n";

        return false;
    }
    */
}
