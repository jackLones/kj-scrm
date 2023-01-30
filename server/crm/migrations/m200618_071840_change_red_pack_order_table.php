<?php

use yii\db\Migration;

/**
 * Class m200618_071840_change_red_pack_order_table
 */
class m200618_071840_change_red_pack_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%red_pack_order}}', 'type', 'tinyint(1) NOT NULL DEFAULT \'1\' COMMENT \'活动类型：1、红包引流，2、裂变引流，3、抽奖引流\' AFTER `corp_id`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200618_071840_change_red_pack_order_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200618_071840_change_red_pack_order_table cannot be reverted.\n";

        return false;
    }
    */
}
