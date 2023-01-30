<?php

use yii\db\Migration;

/**
 * Class m201109_084845_change_table_wait_project_follow
 */
class m201109_084845_change_table_wait_project_follow extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%wait_project_follow}}', 'status', 'int(11) unsigned DEFAULT NULL COMMENT \'阶段状态ID\' after `sea_id`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201109_084845_change_table_wait_project_follow cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201109_084845_change_table_wait_project_follow cannot be reverted.\n";

        return false;
    }
    */
}
