<?php

use yii\db\Migration;

/**
 * Class m210303_111708_change_table_awards_list
 */
class m210303_111708_change_table_awards_list extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%awards_list}}', 'percentage', 'decimal(12,2) NOT NULL DEFAULT \'0.00\' COMMENT \'中奖率\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210303_111708_change_table_awards_list cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210303_111708_change_table_awards_list cannot be reverted.\n";

        return false;
    }
    */
}
