<?php

use yii\db\Migration;

/**
 * Class m200511_061442_change_table_work_corp_agent
 */
class m200511_061442_change_table_work_corp_agent extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn("{{%work_corp_agent}}", "agent_is_money", "tinyint(2) DEFAULT 0 COMMENT '1红包应用' after `agent_use_type`");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200511_061442_change_table_work_corp_agent cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200511_061442_change_table_work_corp_agent cannot be reverted.\n";

        return false;
    }
    */
}
