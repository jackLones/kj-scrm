<?php

use yii\db\Migration;

/**
 * Class m200729_053603_change_table_work_tag_group_user_statistic
 */
class m200729_053603_change_table_work_tag_group_user_statistic extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->addColumn('{{%work_tag_group_user_statistic}}', 'line_type', 'tinyint(1) DEFAULT \'0\' COMMENT \'是否存在排队发送0没有1有\' AFTER `status`');
		$this->addColumn('{{%work_tag_group_user_statistic}}', 'times', 'int(11) DEFAULT \'0\' COMMENT \'当前员工的确认发送次数\' AFTER `status`');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200729_053603_change_table_work_tag_group_user_statistic cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200729_053603_change_table_work_tag_group_user_statistic cannot be reverted.\n";

        return false;
    }
    */
}
