<?php

use yii\db\Migration;

/**
 * Class m200709_021209_change_table_agent_order
 */
class m200709_021209_change_table_agent_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%agent_order}}', 'extrainfo', 'varchar(500) DEFAULT \'\' COMMENT \'额外信息\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200709_021209_change_table_agent_order cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200709_021209_change_table_agent_order cannot be reverted.\n";

        return false;
    }
    */
}
