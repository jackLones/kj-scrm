<?php

use yii\db\Migration;

/**
 * Class m191111_052828_change_table_auto_reply
 */
class m191111_052828_change_table_auto_reply extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%auto_reply}}', 'time_type', 'tinyint(1) unsigned DEFAULT 0 COMMENT \'0分钟 1小时\' AFTER `time_rule`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191111_052828_change_table_auto_reply cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191111_052828_change_table_auto_reply cannot be reverted.\n";

        return false;
    }
    */
}
