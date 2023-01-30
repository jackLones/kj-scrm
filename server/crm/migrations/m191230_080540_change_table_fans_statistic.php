<?php

use yii\db\Migration;

/**
 * Class m191230_080540_change_table_fans_statistic
 */
class m191230_080540_change_table_fans_statistic extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%fans_statistic}}', 'type', 'tinyint(1) unsigned COMMENT \'类型1天2周3月\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191230_080540_change_table_fans_statistic cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191230_080540_change_table_fans_statistic cannot be reverted.\n";

        return false;
    }
    */
}
