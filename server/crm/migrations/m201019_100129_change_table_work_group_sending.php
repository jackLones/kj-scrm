<?php

use yii\db\Migration;

/**
 * Class m201019_100129_change_table_work_group_sending
 */
class m201019_100129_change_table_work_group_sending extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_group_sending}}', 'rule_type_set', 'tinyint(1) NOT NULL DEFAULT 1 COMMENT \'初始单个红包金额类型：1、固定金额，2、随机金额\' AFTER `rule_text`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201019_100129_change_table_work_group_sending cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201019_100129_change_table_work_group_sending cannot be reverted.\n";

        return false;
    }
    */
}
