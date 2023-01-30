<?php

use yii\db\Migration;

/**
 * Class m201028_055237_change_table_money_order
 */
class m201028_055237_change_table_money_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%money_order}}', 'openid', 'char(64) DEFAULT \'\' COMMENT \'外部联系人openid（非企微客户存储）\' after `chat_send_id`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201028_055237_change_table_money_order cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201028_055237_change_table_money_order cannot be reverted.\n";

        return false;
    }
    */
}
