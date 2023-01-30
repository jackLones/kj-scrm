<?php

use yii\db\Migration;

/**
 * Class m191111_082355_change_table_auto_reply
 */
class m191111_082355_change_table_auto_reply extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%auto_reply}}', 'time_num', 'int(10) unsigned DEFAULT 0 COMMENT \'具体时间的数值\' AFTER `time_type`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191111_082355_change_table_auto_reply cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191111_082355_change_table_auto_reply cannot be reverted.\n";

        return false;
    }
    */
}
