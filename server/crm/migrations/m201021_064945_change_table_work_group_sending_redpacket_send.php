<?php

use yii\db\Migration;

/**
 * Class m201021_064945_change_table_work_group_sending_redpacket_send
 */
class m201021_064945_change_table_work_group_sending_redpacket_send extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn("{{%work_group_sending_redpacket_send}}", "rule_type", "tinyint(1) NOT NULL DEFAULT '1' COMMENT '初始单个红包金额类型：1、固定金额，2、随机金额' after `rule_id`");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201021_064945_change_table_work_group_sending_redpacket_send cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201021_064945_change_table_work_group_sending_redpacket_send cannot be reverted.\n";

        return false;
    }
    */
}
