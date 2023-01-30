<?php

use yii\db\Migration;

/**
 * Class m200709_034108_change_table_agent_order
 */
class m200709_034108_change_table_agent_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%agent_order}}', 'agent_type', 'tinyint(1) NOT NULL DEFAULT 0 COMMENT \'帐号类型 0总账号 1代理商\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200709_034108_change_table_agent_order cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200709_034108_change_table_agent_order cannot be reverted.\n";

        return false;
    }
    */
}
