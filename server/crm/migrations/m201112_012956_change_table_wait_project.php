<?php

use yii\db\Migration;

/**
 * Class m201112_012956_change_table_wait_project
 */
class m201112_012956_change_table_wait_project extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%wait_project}}', 'old_days', 'int(11) DEFAULT 0 COMMENT \'上一次设置的项目完成天数\' AFTER `finish_time`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201112_012956_change_table_wait_project cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201112_012956_change_table_wait_project cannot be reverted.\n";

        return false;
    }
    */
}
