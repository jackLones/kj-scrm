<?php

use yii\db\Migration;

/**
 * Class m200511_052904_change_table_work_corp
 */
class m200511_052904_change_table_work_corp extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn("{{%work_corp}}", "day_sum_money", "decimal(10,2) NOT NULL DEFAULT 0 COMMENT '单日红包额度' after `last_customer_tag_time`");
	    $this->addColumn("{{%work_corp}}", "day_external_num", "int(3) NOT NULL DEFAULT 0 COMMENT '客户单日红包次数' after `day_sum_money`");
	    $this->addColumn("{{%work_corp}}", "day_external_money", "decimal(10,2) NOT NULL DEFAULT 0 COMMENT '客户单日红包额度' after `day_external_num`");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200511_052904_change_table_work_corp cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200511_052904_change_table_work_corp cannot be reverted.\n";

        return false;
    }
    */
}
