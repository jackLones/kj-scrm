<?php

use yii\db\Migration;

/**
 * Class m191210_023004_change_table_scene_statistic
 */
class m191210_023004_change_table_scene_statistic extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%scene_statistic}}', 'data_time', 'varchar(16) DEFAULT \'\' COMMENT \'统计时间\'');
	    $this->addColumn('{{%scene_statistic}}', 'is_month', 'tinyint(11) unsigned DEFAULT \'0\' COMMENT \'0:按天，1、按月\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191210_023004_change_table_scene_statistic cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191210_023004_change_table_scene_statistic cannot be reverted.\n";

        return false;
    }
    */
}
