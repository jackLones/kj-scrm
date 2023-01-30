<?php

use yii\db\Migration;

/**
 * Class m201010_064259_change_table_money_order
 */
class m201010_064259_change_table_money_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%money_order}}', 'status', 'tinyint(1) NOT NULL DEFAULT 0 COMMENT \'领取状态0待领取1已领取2已过期3已领完4发放失败\' AFTER `pay_time`');
	    $this->addColumn('{{%money_order}}', 'chat_send_id', 'int(11) NOT NULL DEFAULT 0 COMMENT \'群红包发放表id\' AFTER `status`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201010_064259_change_table_money_order cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201010_064259_change_table_money_order cannot be reverted.\n";

        return false;
    }
    */
}
