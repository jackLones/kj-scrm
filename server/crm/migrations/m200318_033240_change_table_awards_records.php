<?php

use yii\db\Migration;

/**
 * Class m200318_033240_change_table_awards_records
 */
class m200318_033240_change_table_awards_records extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%awards_records}}', 'is_record', 'tinyint(1) unsigned DEFAULT \'0\' COMMENT \'是否中奖 0 未中奖 1 已中奖\' AFTER `award_name`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200318_033240_change_table_awards_records cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200318_033240_change_table_awards_records cannot be reverted.\n";

        return false;
    }
    */
}
