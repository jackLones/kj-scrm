<?php

use yii\db\Migration;

/**
 * Class m200506_081040_change_table_work_contact_way_statistic
 */
class m200506_081040_change_table_work_contact_way_statistic extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->addColumn("{{%work_contact_way_statistic}}", "increase_cnt", "int(11) DEFAULT 0 COMMENT '净增客户数' AFTER `negative_feedback_cnt`");
		$this->addColumn("{{%work_contact_way_statistic}}", "delete_cnt", "int(11) DEFAULT 0 COMMENT '员工删除的客户数' AFTER `negative_feedback_cnt`");
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200506_081040_change_table_work_contact_way_statistic cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200506_081040_change_table_work_contact_way_statistic cannot be reverted.\n";

        return false;
    }
    */
}
