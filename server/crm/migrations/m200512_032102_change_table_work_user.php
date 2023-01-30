<?php

use yii\db\Migration;

/**
 * Class m200512_032102_change_table_work_user
 */
class m200512_032102_change_table_work_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn("{{%work_user}}", "day_user_num", "int(3) NOT NULL DEFAULT 0 COMMENT '员工单日红包发送次数限制'");
	    $this->addColumn("{{%work_user}}", "day_user_money", "decimal(10,2) NOT NULL DEFAULT 0 COMMENT '员工单日红包发送额度限制'");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200512_032102_change_table_work_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200512_032102_change_table_work_user cannot be reverted.\n";

        return false;
    }
    */
}
