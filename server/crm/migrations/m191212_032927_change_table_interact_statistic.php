<?php

use yii\db\Migration;

/**
 * Class m191212_032927_change_table_interact_statistic
 */
class m191212_032927_change_table_interact_statistic extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%interact_statistic}}', 'name', 'char(64) DEFAULT NULL COMMENT \'公众号名称\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191212_032927_change_table_interact_statistic cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191212_032927_change_table_interact_statistic cannot be reverted.\n";

        return false;
    }
    */
}
