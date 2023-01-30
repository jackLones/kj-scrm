<?php

use yii\db\Migration;

/**
 * Class m200722_121054_change_table_work_tag_group_statistic
 */
class m200722_121054_change_table_work_tag_group_statistic extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_tag_group_statistic}}', 'push_type', 'tinyint(1) DEFAULT \'0\' COMMENT \'员工是否发送0未发送1已发送\' AFTER `send`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200722_121054_change_table_work_tag_group_statistic cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200722_121054_change_table_work_tag_group_statistic cannot be reverted.\n";

        return false;
    }
    */
}
