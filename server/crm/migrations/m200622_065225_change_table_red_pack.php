<?php

use yii\db\Migration;

/**
 * Class m200622_065225_change_table_red_pack
 */
class m200622_065225_change_table_red_pack extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%red_pack}}', 'send_type', 'tinyint(1) NOT NULL DEFAULT \'1\' COMMENT \'发放红包类型：1、活动时间内自动发送，2、活动结束后自动发放\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200622_065225_change_table_red_pack cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200622_065225_change_table_red_pack cannot be reverted.\n";

        return false;
    }
    */
}
