<?php

use yii\db\Migration;

/**
 * Class m191125_110606_change_table_fans
 */
class m191125_110606_change_table_fans extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%fans}}', 'unsubscribe_time', 'char(16) DEFAULT NULL COMMENT \'用户取消关注时间，为时间戳。如果用户曾多次取消关注，则取最后取关时间\' AFTER `subscribe_time`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191125_110606_change_table_fans cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191125_110606_change_table_fans cannot be reverted.\n";

        return false;
    }
    */
}
