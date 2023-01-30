<?php

use yii\db\Migration;

/**
 * Class m200703_075949_change_red_pack_table
 */
class m200703_075949_change_red_pack_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%red_pack}}', 'help_limit', 'int(10) NOT NULL DEFAULT \'0\' COMMENT \'好友助力次数限制\' AFTER `complete_num`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200703_075949_change_red_pack_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200703_075949_change_red_pack_table cannot be reverted.\n";

        return false;
    }
    */
}
