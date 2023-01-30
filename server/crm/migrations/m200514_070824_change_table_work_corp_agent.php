<?php

use yii\db\Migration;

/**
 * Class m200514_070824_change_table_work_corp_agent
 */
class m200514_070824_change_table_work_corp_agent extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn("{{%work_corp_agent}}", "basic_agent_type", "char(64) NOT NULL DEFAULT '' COMMENT '基础应用类型：moneyPay企业支付...' after `agent_type`");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200514_070824_change_table_work_corp_agent cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200514_070824_change_table_work_corp_agent cannot be reverted.\n";

        return false;
    }
    */
}
