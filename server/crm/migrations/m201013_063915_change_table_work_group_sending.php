<?php

use yii\db\Migration;

/**
 * Class m201013_063915_change_table_work_group_sending
 */
class m201013_063915_change_table_work_group_sending extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_group_sending}}', 'is_redpacket', 'tinyint(1) DEFAULT 0 COMMENT \'是否群发红包1是0否\' AFTER `msg_type`');
	    $this->addColumn('{{%work_group_sending}}', 'rule_id', 'int(11) DEFAULT 0 COMMENT \'红包规则id\' AFTER `is_redpacket`');
	    $this->addColumn('{{%work_group_sending}}', 'rule_text', 'text COMMENT \'红包规则内容（非存储规则）\' AFTER `rule_id`');
	    $this->addColumn('{{%work_group_sending}}', 'redpacket_amount', 'decimal(12,2) DEFAULT \'0.00\' COMMENT \'活动投放金额\' AFTER `rule_text`');
	    $this->addColumn('{{%work_group_sending}}', 'send_amount', 'decimal(12,2) DEFAULT \'0.00\' COMMENT \'已领取金额\' AFTER `redpacket_amount`');
	    $this->addColumn('{{%work_group_sending}}', 'send_num', 'int(11) DEFAULT 0 COMMENT \'已领取人数\' AFTER `send_amount`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201013_063915_change_table_work_group_sending cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201013_063915_change_table_work_group_sending cannot be reverted.\n";

        return false;
    }
    */
}
